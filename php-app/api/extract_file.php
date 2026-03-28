<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

const MAX_RESUME_FILE_SIZE = 5242880;
const MAX_EXTRACTED_CHARS = 4000;
const MIN_EXTRACTED_CHARS = 50;

boot_session();
require_method('POST');
verify_csrf();
require_user_row();
check_rate_limit('extract_file', 10);

if (!isset($_FILES['resume']) || !is_array($_FILES['resume'])) {
    json_response(['error' => 'Файл не найден'], 422);
}

$file = $_FILES['resume'];
$uploadError = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
if ($uploadError !== UPLOAD_ERR_OK) {
    json_response(['error' => upload_error_message($uploadError)], 422);
}

$tmpPath = (string)($file['tmp_name'] ?? '');
$originalName = trim((string)($file['name'] ?? 'resume'));
$fileSize = (int)($file['size'] ?? 0);
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
    json_response(['error' => 'Не удалось получить загруженный файл'], 422);
}

if ($fileSize <= 0 || $fileSize > MAX_RESUME_FILE_SIZE) {
    json_response(['error' => 'Файл слишком большой. Максимум — 5 MB.'], 422);
}

if (!in_array($extension, ['pdf', 'docx'], true)) {
    json_response(['error' => 'Можно загрузить только PDF или DOCX.'], 422);
}

$mime = detect_mime_type($tmpPath);
if (!is_allowed_resume_file($extension, $mime, $tmpPath)) {
    json_response(['error' => 'Файл повреждён или формат не поддерживается.'], 422);
}

$text = $extension === 'pdf'
    ? extract_pdf_text($tmpPath)
    : extract_docx_text($tmpPath);

$text = clean_extracted_text($text);
if (mb_strlen($text, 'UTF-8') > MAX_EXTRACTED_CHARS) {
    $text = mb_substr($text, 0, MAX_EXTRACTED_CHARS, 'UTF-8');
    $text = trim($text);
}

if (mb_strlen($text, 'UTF-8') < MIN_EXTRACTED_CHARS) {
    json_response(['error' => 'Не удалось извлечь текст. Скопируйте резюме вручную.'], 422);
}

json_response([
    'text' => $text,
    'chars' => mb_strlen($text, 'UTF-8'),
]);

function upload_error_message(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Файл слишком большой. Максимум — 5 MB.',
        UPLOAD_ERR_PARTIAL => 'Файл загрузился не полностью. Попробуйте ещё раз.',
        UPLOAD_ERR_NO_FILE => 'Файл не найден.',
        default => 'Не удалось загрузить файл.',
    };
}

function detect_mime_type(string $path): string
{
    if (!is_file($path)) {
        return '';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        return '';
    }

    $mime = (string)(finfo_file($finfo, $path) ?: '');
    finfo_close($finfo);

    return strtolower(trim($mime));
}

function is_allowed_resume_file(string $extension, string $mime, string $path): bool
{
    if (!matches_file_signature($path, $extension)) {
        return false;
    }

    if ($mime === '') {
        return false;
    }

    $allowed = [
        'pdf' => [
            'application/pdf',
            'application/x-pdf',
            'application/acrobat',
            'applications/vnd.pdf',
            'text/pdf',
            'text/x-pdf',
            'application/octet-stream',
        ],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream',
        ],
    ];

    if (in_array($mime, $allowed[$extension] ?? [], true)) {
        return true;
    }

    return $extension === 'docx' && str_contains($mime, 'zip');
}

function matches_file_signature(string $path, string $extension): bool
{
    $prefix = (string)file_get_contents($path, false, null, 0, 8);
    if ($prefix === '') {
        return false;
    }

    return match ($extension) {
        'pdf' => str_starts_with($prefix, '%PDF-'),
        'docx' => str_starts_with($prefix, "PK\x03\x04")
            || str_starts_with($prefix, "PK\x05\x06")
            || str_starts_with($prefix, "PK\x07\x08"),
        default => false,
    };
}

function extract_pdf_text(string $path): string
{
    $shellText = try_pdftotext_extract($path);
    if ($shellText !== '') {
        return $shellText;
    }

    return extract_pdf_text_fallback($path);
}

function try_pdftotext_extract(string $path): string
{
    if (!can_use_shell_exec()) {
        return '';
    }

    $binary = trim((string)@shell_exec('command -v pdftotext 2>/dev/null'));
    if ($binary === '') {
        return '';
    }

    $outputPath = tempnam(sys_get_temp_dir(), 'offerai_pdf_');
    if ($outputPath === false) {
        return '';
    }

    $command = escapeshellarg($binary)
        . ' -layout '
        . escapeshellarg($path)
        . ' '
        . escapeshellarg($outputPath)
        . ' 2>/dev/null';

    @shell_exec($command);
    $text = is_file($outputPath) ? (string)@file_get_contents($outputPath) : '';
    @unlink($outputPath);

    return clean_extracted_text($text);
}

function can_use_shell_exec(): bool
{
    if (!function_exists('shell_exec')) {
        return false;
    }

    $disabled = array_map('trim', explode(',', (string)ini_get('disable_functions')));
    return !in_array('shell_exec', $disabled, true);
}

function extract_pdf_text_fallback(string $path): string
{
    $raw = (string)@file_get_contents($path);
    if ($raw === '') {
        return '';
    }

    $unicodeText = extract_pdf_text_with_unicode_maps($raw);
    if ($unicodeText !== '' && !looks_like_corrupted_resume_text($unicodeText)) {
        return $unicodeText;
    }

    $chunks = [];
    foreach (build_pdf_candidates($raw) as $candidate) {
        foreach (extract_pdf_text_blocks($candidate) as $blockText) {
            if ($blockText !== '') {
                $chunks[] = $blockText;
            }
        }
    }

    $chunks = array_values(array_unique(array_filter($chunks)));
    $text = implode("\n", $chunks);
    return looks_like_corrupted_resume_text($text) ? '' : $text;
}

function build_pdf_candidates(string $raw): array
{
    $candidates = [$raw];

    if (!preg_match_all('/<<(.*?)>>\s*stream\s*(.*?)\s*endstream/s', $raw, $matches, PREG_SET_ORDER)) {
        return $candidates;
    }

    foreach ($matches as $match) {
        $dict = (string)($match[1] ?? '');
        $stream = ltrim((string)($match[2] ?? ''), "\r\n");

        if ($stream === '') {
            continue;
        }

        if (stripos($dict, '/FlateDecode') !== false) {
            $decoded = @gzuncompress($stream);
            if ($decoded === false) {
                $decoded = @gzinflate($stream);
            }
            if ($decoded === false && strlen($stream) > 6) {
                $decoded = @gzinflate(substr($stream, 2));
            }
            if (is_string($decoded) && $decoded !== '') {
                $candidates[] = $decoded;
            }
            continue;
        }

        $candidates[] = $stream;
    }

    return $candidates;
}

function extract_pdf_text_with_unicode_maps(string $raw): string
{
    $objects = parse_pdf_objects($raw);
    if ($objects === []) {
        return '';
    }

    $fontMaps = build_pdf_font_unicode_maps($objects);
    $chunks = [];

    foreach ($objects as $object) {
        $dict = (string)($object['dict'] ?? '');
        if (!preg_match('/\/Type\s*\/Page\b/', $dict)) {
            continue;
        }

        $fontAliases = extract_pdf_page_font_aliases($dict, $objects, $fontMaps);
        $contentObjectIds = extract_pdf_content_object_ids($dict);
        if ($contentObjectIds === []) {
            continue;
        }

        $pageChunks = [];
        foreach ($contentObjectIds as $contentObjectId) {
            if (!isset($objects[$contentObjectId])) {
                continue;
            }

            $content = decode_pdf_stream_object($objects[$contentObjectId]);
            if ($content === '') {
                continue;
            }

            foreach (extract_pdf_text_blocks($content, $fontAliases) as $blockText) {
                if ($blockText !== '') {
                    $pageChunks[] = $blockText;
                }
            }
        }

        $pageText = clean_extracted_text(implode("\n", $pageChunks));
        if ($pageText !== '') {
            $chunks[] = $pageText;
        }
    }

    return implode("\n\n", $chunks);
}

function parse_pdf_objects(string $raw): array
{
    if (!preg_match_all('/(\d+)\s+(\d+)\s+obj(.*?)endobj/s', $raw, $matches, PREG_SET_ORDER)) {
        return [];
    }

    $objects = [];
    foreach ($matches as $match) {
        $objectId = (int)($match[1] ?? 0);
        $body = (string)($match[3] ?? '');
        $dict = trim($body);
        $stream = '';

        if (preg_match('/^(.*?)(?:stream\r?\n)(.*?)(?:\r?\nendstream)$/s', $body, $streamMatch)) {
            $dict = trim((string)($streamMatch[1] ?? ''));
            $stream = (string)($streamMatch[2] ?? '');
        }

        $objects[$objectId] = [
            'dict' => $dict,
            'stream' => $stream,
        ];
    }

    return $objects;
}

function decode_pdf_stream_object(array $object): string
{
    $stream = (string)($object['stream'] ?? '');
    if ($stream === '') {
        return '';
    }

    $dict = (string)($object['dict'] ?? '');
    if (stripos($dict, '/FlateDecode') !== false) {
        $decoded = @gzuncompress($stream);
        if ($decoded === false) {
            $decoded = @gzinflate($stream);
        }
        if ($decoded === false && strlen($stream) > 6) {
            $decoded = @gzinflate(substr($stream, 2));
        }

        return is_string($decoded) ? $decoded : '';
    }

    return $stream;
}

function build_pdf_font_unicode_maps(array $objects): array
{
    $maps = [];

    foreach ($objects as $objectId => $object) {
        $dict = (string)($object['dict'] ?? '');
        if (!preg_match('/\/ToUnicode\s+(\d+)\s+\d+\s+R/', $dict, $match)) {
            continue;
        }

        $unicodeObjectId = (int)$match[1];
        if (!isset($objects[$unicodeObjectId])) {
            continue;
        }

        $cmap = decode_pdf_stream_object($objects[$unicodeObjectId]);
        if ($cmap === '') {
            continue;
        }

        $parsed = parse_pdf_to_unicode_cmap($cmap);
        if ($parsed['map'] !== []) {
            $maps[$objectId] = $parsed;
        }
    }

    return $maps;
}

function parse_pdf_to_unicode_cmap(string $cmap): array
{
    $map = [];
    $codeLengths = [];

    if (preg_match_all('/\d+\s+begincodespacerange(.*?)endcodespacerange/s', $cmap, $ranges, PREG_SET_ORDER)) {
        foreach ($ranges as $range) {
            if (!preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', (string)($range[1] ?? ''), $rangePairs, PREG_SET_ORDER)) {
                continue;
            }
            foreach ($rangePairs as $pair) {
                $length = (int)(strlen((string)$pair[1]) / 2);
                if ($length > 0) {
                    $codeLengths[$length] = $length;
                }
            }
        }
    }

    if (preg_match_all('/\d+\s+beginbfchar(.*?)endbfchar/s', $cmap, $blocks, PREG_SET_ORDER)) {
        foreach ($blocks as $block) {
            if (!preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', (string)($block[1] ?? ''), $pairs, PREG_SET_ORDER)) {
                continue;
            }
            foreach ($pairs as $pair) {
                $source = strtoupper((string)$pair[1]);
                $decoded = decode_pdf_unicode_hex((string)$pair[2]);
                if ($decoded !== '') {
                    $map[$source] = $decoded;
                }
            }
        }
    }

    if (preg_match_all('/\d+\s+beginbfrange(.*?)endbfrange/s', $cmap, $blocks, PREG_SET_ORDER)) {
        foreach ($blocks as $block) {
            $lines = preg_split('/\R/u', (string)($block[1] ?? '')) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                if (preg_match('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $line, $match)) {
                    $srcStart = hexdec($match[1]);
                    $srcEnd = hexdec($match[2]);
                    $dstStart = hexdec($match[3]);
                    $sourceWidth = strlen($match[1]);
                    $destWidth = strlen($match[3]);

                    for ($offset = 0; $srcStart + $offset <= $srcEnd; $offset++) {
                        $source = strtoupper(str_pad(dechex($srcStart + $offset), $sourceWidth, '0', STR_PAD_LEFT));
                        $dest = strtoupper(str_pad(dechex($dstStart + $offset), $destWidth, '0', STR_PAD_LEFT));
                        $decoded = decode_pdf_unicode_hex($dest);
                        if ($decoded !== '') {
                            $map[$source] = $decoded;
                        }
                    }
                    continue;
                }

                if (preg_match('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>\s*\[(.*?)\]/', $line, $match)) {
                    $srcStart = hexdec($match[1]);
                    $srcEnd = hexdec($match[2]);
                    $sourceWidth = strlen($match[1]);

                    if (!preg_match_all('/<([0-9A-Fa-f]+)>/', (string)$match[3], $destinations)) {
                        continue;
                    }

                    $destIndex = 0;
                    for ($code = $srcStart; $code <= $srcEnd; $code++) {
                        $destHex = strtoupper((string)($destinations[1][$destIndex] ?? ''));
                        $destIndex++;
                        if ($destHex === '') {
                            break;
                        }
                        $decoded = decode_pdf_unicode_hex($destHex);
                        if ($decoded !== '') {
                            $source = strtoupper(str_pad(dechex($code), $sourceWidth, '0', STR_PAD_LEFT));
                            $map[$source] = $decoded;
                        }
                    }
                }
            }
        }
    }

    $lengths = array_values($codeLengths);
    rsort($lengths);

    return [
        'map' => $map,
        'code_lengths' => $lengths === [] ? [1] : $lengths,
    ];
}

function decode_pdf_unicode_hex(string $hex): string
{
    $hex = preg_replace('/\s+/', '', $hex) ?? '';
    if ($hex === '') {
        return '';
    }

    if (strlen($hex) % 2 === 1) {
        $hex = '0' . $hex;
    }

    $binary = @hex2bin($hex);
    if ($binary === false || $binary === '') {
        return '';
    }

    if (function_exists('mb_convert_encoding')) {
        $converted = @mb_convert_encoding($binary, 'UTF-8', 'UTF-16BE');
        if (is_string($converted) && $converted !== '') {
            return $converted;
        }
    }

    return normalize_extracted_encoding($binary);
}

function extract_pdf_page_font_aliases(string $pageDict, array $objects, array $fontMaps): array
{
    $resourceDict = '';

    if (preg_match('/\/Resources\s+(\d+)\s+\d+\s+R/', $pageDict, $match)) {
        $resourceObjectId = (int)$match[1];
        $resourceDict = (string)($objects[$resourceObjectId]['dict'] ?? '');
    } elseif (preg_match('/\/Resources\s*<<(.*?)>>/s', $pageDict, $match)) {
        $resourceDict = '<<' . (string)$match[1] . '>>';
    }

    if ($resourceDict === '') {
        return [];
    }

    if (!preg_match('/\/Font\s*<<(.*?)>>/s', $resourceDict, $fontBlock)) {
        return [];
    }

    if (!preg_match_all('/\/([^\s\/]+)\s+(\d+)\s+\d+\s+R/', (string)$fontBlock[1], $fontRefs, PREG_SET_ORDER)) {
        return [];
    }

    $aliases = [];
    foreach ($fontRefs as $fontRef) {
        $alias = (string)($fontRef[1] ?? '');
        $fontObjectId = (int)($fontRef[2] ?? 0);
        if ($alias === '' || !isset($fontMaps[$fontObjectId])) {
            continue;
        }
        $aliases[$alias] = $fontMaps[$fontObjectId];
    }

    return $aliases;
}

function extract_pdf_content_object_ids(string $pageDict): array
{
    if (preg_match('/\/Contents\s+(\d+)\s+\d+\s+R/', $pageDict, $match)) {
        return [(int)$match[1]];
    }

    if (!preg_match('/\/Contents\s*\[(.*?)\]/s', $pageDict, $match)) {
        return [];
    }

    if (!preg_match_all('/(\d+)\s+\d+\s+R/', (string)$match[1], $refs)) {
        return [];
    }

    return array_map('intval', $refs[1]);
}

function extract_pdf_text_blocks(string $content, array $fontAliases = []): array
{
    if (!preg_match_all('/BT(.*?)ET/s', $content, $blocks, PREG_SET_ORDER)) {
        return [];
    }

    $result = [];
    foreach ($blocks as $block) {
        $text = '';
        $body = (string)($block[1] ?? '');
        $currentFont = null;

        if (!preg_match_all('/\/([^\s\/]+)\s+[-+]?\d*\.?\d+\s+Tf|(\((?:\\\\.|[^()\\\\])*\)|<[\dA-Fa-f\s]+>)\s*(?:Tj|\'|")|\[(.*?)\]\s*TJ|((?:[-+]?\d*\.?\d+\s+){2}T[dD]|(?:[-+]?\d*\.?\d+\s+){6}Tm|T\*)/s', $body, $operations, PREG_SET_ORDER)) {
            continue;
        }

        foreach ($operations as $operation) {
            if (!empty($operation[1])) {
                $currentFont = $fontAliases[(string)$operation[1]] ?? null;
                continue;
            }

            if (!empty($operation[2])) {
                $text .= decode_pdf_string_token((string)$operation[2], $currentFont);
                continue;
            }

            if (!empty($operation[4])) {
                if ($text !== '' && !preg_match('/(?:\n|\s)$/u', $text)) {
                    $text .= "\n";
                }
                continue;
            }

            if (empty($operation[3])) {
                continue;
            }

            if (!preg_match_all('/\((?:\\\\.|[^()\\\\])*\)|<[\dA-Fa-f\s]+>/s', (string)$operation[3], $tokens)) {
                continue;
            }
            foreach ($tokens[0] as $token) {
                $text .= decode_pdf_string_token((string)$token, $currentFont);
            }
        }

        $text = clean_extracted_text($text);
        if ($text !== '') {
            $result[] = $text;
        }
    }

    return $result;
}

function decode_pdf_string_token(string $token, ?array $fontMap = null): string
{
    $token = trim($token);
    if ($token === '') {
        return '';
    }

    if ($token[0] === '(') {
        return decode_pdf_literal_string($token, $fontMap);
    }

    if ($token[0] === '<') {
        return decode_pdf_hex_string($token, $fontMap);
    }

    return '';
}

function decode_pdf_literal_string(string $token, ?array $fontMap = null): string
{
    $inner = substr($token, 1, -1);
    $length = strlen($inner);
    $result = '';

    for ($i = 0; $i < $length; $i++) {
        $char = $inner[$i];
        if ($char !== '\\') {
            $result .= $char;
            continue;
        }

        $i++;
        if ($i >= $length) {
            break;
        }

        $escaped = $inner[$i];
        switch ($escaped) {
            case 'n':
                $result .= "\n";
                break;
            case 'r':
                $result .= "\r";
                break;
            case 't':
                $result .= "\t";
                break;
            case 'b':
                $result .= "\x08";
                break;
            case 'f':
                $result .= "\f";
                break;
            case '(':
            case ')':
            case '\\':
                $result .= $escaped;
                break;
            case "\r":
                if (($inner[$i + 1] ?? '') === "\n") {
                    $i++;
                }
                break;
            case "\n":
                break;
            default:
                if ($escaped >= '0' && $escaped <= '7') {
                    $octal = $escaped;
                    for ($j = 0; $j < 2; $j++) {
                        $next = $inner[$i + 1] ?? '';
                        if ($next < '0' || $next > '7') {
                            break;
                        }
                        $octal .= $next;
                        $i++;
                    }
                    $result .= chr(octdec($octal));
                } else {
                    $result .= $escaped;
                }
        }
    }

    if (is_array($fontMap) && ($fontMap['map'] ?? []) !== []) {
        $decoded = decode_pdf_bytes_with_cmap($result, $fontMap);
        if ($decoded !== '') {
            return $decoded;
        }
    }

    return normalize_extracted_encoding($result);
}

function decode_pdf_hex_string(string $token, ?array $fontMap = null): string
{
    $hex = preg_replace('/\s+/', '', trim($token, '<>')) ?? '';
    if ($hex === '') {
        return '';
    }

    if (strlen($hex) % 2 === 1) {
        $hex .= '0';
    }

    $binary = @hex2bin($hex);
    if ($binary === false) {
        return '';
    }

    if (is_array($fontMap) && ($fontMap['map'] ?? []) !== []) {
        $decoded = decode_pdf_bytes_with_cmap($binary, $fontMap);
        if ($decoded !== '') {
            return $decoded;
        }
    }

    return normalize_extracted_encoding($binary);
}

function decode_pdf_bytes_with_cmap(string $bytes, array $fontMap): string
{
    $map = is_array($fontMap['map'] ?? null) ? $fontMap['map'] : [];
    if ($bytes === '' || $map === []) {
        return '';
    }

    $lengths = array_values(array_unique(array_map('intval', $fontMap['code_lengths'] ?? [1])));
    $lengths = array_values(array_filter($lengths, static fn (int $length): bool => $length > 0));
    if ($lengths === []) {
        $lengths = [1];
    }
    rsort($lengths);

    $decoded = '';
    $byteLength = strlen($bytes);

    for ($offset = 0; $offset < $byteLength;) {
        $matched = false;

        foreach ($lengths as $length) {
            if ($offset + $length > $byteLength) {
                continue;
            }

            $code = strtoupper(bin2hex(substr($bytes, $offset, $length)));
            if (!isset($map[$code])) {
                continue;
            }

            $decoded .= $map[$code];
            $offset += $length;
            $matched = true;
            break;
        }

        if ($matched) {
            continue;
        }

        $decoded .= normalize_extracted_encoding($bytes[$offset]);
        $offset++;
    }

    return $decoded;
}

function normalize_extracted_encoding(string $text): string
{
    if ($text === '') {
        return '';
    }

    if (!function_exists('mb_detect_encoding') || !function_exists('mb_convert_encoding')) {
        return str_replace("\0", '', $text);
    }

    if (str_contains($text, "\0")) {
        $utf16be = @mb_convert_encoding($text, 'UTF-8', 'UTF-16BE');
        if (is_string($utf16be) && $utf16be !== '') {
            return $utf16be;
        }
        $utf16le = @mb_convert_encoding($text, 'UTF-8', 'UTF-16LE');
        if (is_string($utf16le) && $utf16le !== '') {
            return $utf16le;
        }
    }

    $encoding = mb_detect_encoding($text, ['UTF-8', 'Windows-1251', 'Windows-1252', 'ISO-8859-1'], true);
    if ($encoding && $encoding !== 'UTF-8') {
        $converted = @mb_convert_encoding($text, 'UTF-8', $encoding);
        if (is_string($converted) && $converted !== '') {
            return $converted;
        }
    }

    return str_replace("\0", '', $text);
}

function looks_like_corrupted_resume_text(string $text): bool
{
    $text = clean_extracted_text($text);
    if ($text === '') {
        return true;
    }

    $tokens = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    if (!is_array($tokens) || $tokens === []) {
        return true;
    }

    $wordish = 0;
    $singleChar = 0;
    $longWord = 0;
    $symbolOnly = 0;

    foreach ($tokens as $token) {
        if (preg_match('/^[\p{P}\p{S}]+$/u', $token)) {
            $symbolOnly++;
            continue;
        }

        $plain = preg_replace('/[^\p{L}\p{N}]+/u', '', $token) ?? '';
        if ($plain === '') {
            continue;
        }

        $wordish++;
        $length = mb_strlen($plain, 'UTF-8');
        if ($length === 1) {
            $singleChar++;
        }
        if ($length >= 3) {
            $longWord++;
        }
    }

    if ($wordish < 12) {
        return false;
    }

    $singleRatio = $singleChar / max($wordish, 1);
    $longRatio = $longWord / max($wordish, 1);
    $symbolRatio = $symbolOnly / max(count($tokens), 1);

    return ($singleRatio >= 0.35 && $longRatio <= 0.45)
        || ($symbolRatio >= 0.18 && $longRatio <= 0.5);
}

function extract_docx_text(string $path): string
{
    $xml = extract_docx_xml($path);
    if ($xml === '') {
        return '';
    }

    $xml = str_replace(
        ['</w:p>', '</w:tr>', '<w:br/>', '<w:br />', '<w:cr/>', '<w:cr />', '<w:tab/>', '<w:tab />'],
        ["\n", "\n", "\n", "\n", "\n", "\n", "\t", "\t"],
        $xml
    );

    $text = strip_tags($xml);
    return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function extract_docx_xml(string $path): string
{
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            $xml = (string)$zip->getFromName('word/document.xml');
            $zip->close();
            if ($xml !== '') {
                return $xml;
            }
        }
    }

    return extract_zip_entry_fallback($path, 'word/document.xml');
}

function extract_zip_entry_fallback(string $path, string $targetName): string
{
    $data = (string)@file_get_contents($path);
    if ($data === '') {
        return '';
    }

    $eocdOffset = strrpos($data, "\x50\x4b\x05\x06");
    if ($eocdOffset === false) {
        return '';
    }

    $eocd = unpack(
        'Vsignature/vdisk/vdiskStart/ventriesOnDisk/ventriesTotal/VcentralDirSize/VcentralDirOffset/vcommentLength',
        substr($data, $eocdOffset, 22)
    );
    if (!is_array($eocd)) {
        return '';
    }

    $offset = (int)$eocd['centralDirOffset'];
    $limit = $offset + (int)$eocd['centralDirSize'];

    while ($offset < $limit) {
        $header = unpack(
            'Vsignature/vversionMadeBy/vversionNeeded/vflags/vmethod/vtime/vdate/Vcrc/VcompressedSize/VuncompressedSize/vfileNameLength/vextraFieldLength/vfileCommentLength/vdiskNumberStart/vinternalAttributes/VexternalAttributes/VlocalHeaderOffset',
            substr($data, $offset, 46)
        );
        if (!is_array($header) || (int)$header['signature'] !== 0x02014b50) {
            break;
        }

        $nameOffset = $offset + 46;
        $name = substr($data, $nameOffset, (int)$header['fileNameLength']);
        if ($name === $targetName) {
            return extract_zip_entry_payload($data, $header);
        }

        $offset += 46
            + (int)$header['fileNameLength']
            + (int)$header['extraFieldLength']
            + (int)$header['fileCommentLength'];
    }

    return '';
}

function extract_zip_entry_payload(string $archive, array $header): string
{
    $localOffset = (int)$header['localHeaderOffset'];
    $local = unpack(
        'Vsignature/vversionNeeded/vflags/vmethod/vtime/vdate/Vcrc/VcompressedSize/VuncompressedSize/vfileNameLength/vextraFieldLength',
        substr($archive, $localOffset, 30)
    );
    if (!is_array($local) || (int)$local['signature'] !== 0x04034b50) {
        return '';
    }

    $payloadOffset = $localOffset + 30 + (int)$local['fileNameLength'] + (int)$local['extraFieldLength'];
    $compressedSize = (int)$header['compressedSize'];
    $payload = substr($archive, $payloadOffset, $compressedSize);
    $method = (int)$header['method'];

    return match ($method) {
        0 => $payload,
        8 => inflate_zip_payload($payload),
        default => '',
    };
}

function inflate_zip_payload(string $payload): string
{
    $decoded = @gzinflate($payload);
    if ($decoded !== false) {
        return $decoded;
    }

    $decoded = @zlib_decode($payload);
    return $decoded !== false ? $decoded : '';
}

function clean_extracted_text(string $text): string
{
    $text = normalize_extracted_encoding($text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5 | ENT_XML1, 'UTF-8');
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? $text;
    $text = preg_replace("/[ \t]+\n/u", "\n", $text) ?? $text;
    $text = preg_replace('/[ \t]{2,}/u', ' ', $text) ?? $text;
    $text = preg_replace("/\n{3,}/u", "\n\n", $text) ?? $text;

    return trim($text);
}
