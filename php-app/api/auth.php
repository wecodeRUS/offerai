<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

boot_session();

$action = $_GET['action'] ?? '';

const AUTH_USER_SELECT = '
    SELECT
        id,
        email,
        password_hash,
        name,
        plan,
        uses_left,
        email_verified,
        email_verification_code,
        email_verification_expires_at,
        email_verification_sent_at,
        password_reset_token,
        password_reset_expires_at,
        password_reset_sent_at,
        created_at
    FROM users
';

function auth_error(string $message, int $status = 400, array $extra = []): void
{
    json_response(array_merge(['error' => $message], $extra), $status);
}

function auth_parse_time(?string $value): ?int
{
    if (!is_string($value) || trim($value) === '') {
        return null;
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? null : $timestamp;
}

function auth_time_in_future(?string $value): bool
{
    $timestamp = auth_parse_time($value);
    return $timestamp !== null && $timestamp > time();
}

function auth_time_within_last_seconds(?string $value, int $seconds): bool
{
    $timestamp = auth_parse_time($value);
    if ($timestamp === null) {
        return false;
    }

    return $timestamp >= (time() - $seconds);
}

function auth_last_consume_record(): ?array
{
    $record = $_SESSION['last_consume'] ?? null;
    if (!is_array($record)) {
        return null;
    }

    $userId = (int)($record['user_id'] ?? 0);
    $consumedAt = (int)($record['consumed_at'] ?? 0);
    if ($userId <= 0 || $consumedAt <= 0) {
        return null;
    }

    return [
        'user_id' => $userId,
        'consumed_at' => $consumedAt,
        'module' => trim((string)($record['module'] ?? '')),
        'reason' => trim((string)($record['reason'] ?? '')),
        'refunded' => (bool)($record['refunded'] ?? false),
    ];
}

function auth_store_consume_record(int $userId, string $module = '', string $reason = ''): void
{
    $_SESSION['last_consume'] = [
        'user_id' => $userId,
        'consumed_at' => time(),
        'module' => trim($module),
        'reason' => trim($reason),
        'refunded' => false,
    ];
}

function auth_mark_consume_refunded(): void
{
    $record = auth_last_consume_record();
    if ($record === null) {
        return;
    }

    $record['refunded'] = true;
    $_SESSION['last_consume'] = $record;
}

function auth_clear_consume_record(): void
{
    unset($_SESSION['last_consume']);
}

function auth_find_user_by_email(string $email): ?array
{
    $stmt = db()->prepare(AUTH_USER_SELECT . ' WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function auth_future_datetime_string(int $minutes): string
{
    return (new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow')))
        ->modify('+' . $minutes . ' minutes')
        ->format('Y-m-d H:i:s');
}

function auth_input_email(array $payload, bool $strict = false): string
{
    $email = mb_strtolower(trim((string)($payload['email'] ?? '')));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        auth_error('Укажите корректный email');
    }

    $domain = substr(strrchr($email, '@') ?: '', 1);
    if ($domain === '' || !str_contains($domain, '.')) {
        auth_error('Укажите корректный email');
    }

    $lastDot = strrpos($domain, '.');
    $tld = $lastDot === false ? '' : substr($domain, $lastDot + 1);
    if ($tld === '' || mb_strlen($tld) < 2) {
        auth_error('Укажите корректный email');
    }

    if (!$strict) {
        return $email;
    }

    $disposableDomains = [
        'mailinator.com',
        'guerrillamail.com',
        'tempmail.com',
        'throwaway.email',
        'yopmail.com',
        'sharklasers.com',
        'guerrillamailblock.com',
        'grr.la',
        'guerrillamail.info',
        'spam4.me',
        'trashmail.com',
        'trashmail.me',
        'dispostable.com',
        'mailnull.com',
        'spamgourmet.com',
        'spamgourmet.net',
        'maildrop.cc',
        'discard.email',
        'spamspot.com',
    ];

    if (in_array($domain, $disposableDomains, true)) {
        auth_error('Используйте постоянный email-адрес');
    }

    if (function_exists('checkdnsrr')) {
        $previousTimeout = ini_get('default_socket_timeout');
        $timeoutChanged = @ini_set('default_socket_timeout', '3');

        $hasDns = false;
        $dnsChecksFailed = false;
        try {
            $hasMx = @checkdnsrr($domain, 'MX');
            $hasA = @checkdnsrr($domain, 'A');
            $hasDns = $hasMx || $hasA;
            $dnsChecksFailed = !$hasDns;
        } finally {
            if ($timeoutChanged !== false && $previousTimeout !== false) {
                @ini_set('default_socket_timeout', (string)$previousTimeout);
            }
        }

        if ($dnsChecksFailed) {
            auth_error('Проверьте правильность email');
        }
    }

    return $email;
}

function auth_input_password(array $payload, string $field = 'password'): string
{
    $password = (string)($payload[$field] ?? '');
    if (mb_strlen($password) < 6) {
        auth_error('Пароль минимум 6 символов');
    }
    if (mb_strlen($password) > 200) {
        auth_error('Пароль слишком длинный');
    }

    return $password;
}

function auth_input_verification_code(array $payload): string
{
    $code = preg_replace('/\D+/', '', (string)($payload['code'] ?? ''));
    if (!is_string($code) || !preg_match('/^\d{6}$/', $code)) {
        auth_error('Введите 6 цифр из письма');
    }

    return $code;
}

function verify_turnstile_token(string $token): void
{
    $remoteIp = (string)($_SERVER['REMOTE_ADDR'] ?? '');

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
        ],
        CURLOPT_POSTFIELDS => http_build_query([
            'secret' => TURNSTILE_SECRET_KEY,
            'response' => $token,
            'remoteip' => $remoteIp,
        ]),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $curlError !== '' || $httpCode < 200 || $httpCode >= 300) {
        auth_error('Не удалось проверить капчу', 502);
    }

    $data = json_decode((string)$response, true);
    if (!is_array($data) || ($data['success'] ?? false) !== true) {
        auth_error('Проверка не пройдена', 400);
    }
}

function auth_store_verification_code(int $userId, string $code): void
{
    $expiresAt = auth_future_datetime_string(EMAIL_VERIFICATION_CODE_TTL_MINUTES);
    $stmt = db()->prepare(
        'UPDATE users
         SET
            email_verification_code = :code,
            email_verification_expires_at = :expires_at,
            email_verification_sent_at = NOW()
         WHERE id = :id'
    );
    $stmt->execute([
        'id' => $userId,
        'code' => $code,
        'expires_at' => $expiresAt,
    ]);
}

function auth_send_verification_code(array $row, bool $forceNewCode = true): bool
{
    $code = $forceNewCode ? generate_six_digit_code() : (string)($row['email_verification_code'] ?? '');
    if ($code === '') {
        $code = generate_six_digit_code();
    }

    auth_store_verification_code((int)$row['id'], $code);

    return send_verification_email_code((string)$row['email'], (string)$row['name'], $code);
}

function auth_pending_verification_response(array $row, ?string $message = null, ?bool $mailSent = null, int $status = 200): void
{
    $payload = [
        'status' => 'pending_verification',
        'email' => (string)$row['email'],
        'user' => public_user_payload($row),
        'message' => $message ?: 'Введите 6-значный код из письма.',
    ];

    if ($mailSent !== null) {
        $payload['mailSent'] = $mailSent;
    }

    json_response($payload, $status);
}

function auth_password_reset_message(): void
{
    json_response([
        'status' => 'password_reset_requested',
        'message' => 'Если такой email существует, мы отправили ссылку для смены пароля.',
    ]);
}

switch ($action) {
    case 'check':
        require_method('GET');
        $row = current_user_row();
        if (!$row) {
            auth_error('Не авторизован', 401);
        }
        json_response(['user' => public_user_payload($row)]);
        break;

    case 'register':
        require_method('POST');
        verify_csrf();
        check_rate_limit('auth_attempt', 10);

        $payload = request_json();
        $name = trim((string)($payload['name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 100) {
            auth_error('Укажите имя');
        }

        $turnstileToken = (string)($payload['cf-turnstile-response'] ?? '');
        if ($turnstileToken === '') {
            auth_error('Пройдите проверку', 400);
        }

        $email = auth_input_email($payload, true);
        $password = auth_input_password($payload);
        verify_turnstile_token($turnstileToken);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = db()->prepare(
                'INSERT INTO users (
                    email,
                    password_hash,
                    name,
                    plan,
                    uses_left,
                    email_verified,
                    email_verification_code,
                    email_verification_expires_at,
                    email_verification_sent_at,
                    password_reset_token,
                    password_reset_expires_at,
                    password_reset_sent_at
                ) VALUES (
                    :email,
                    :password_hash,
                    :name,
                    NULL,
                    0,
                    0,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL
                )'
            );
            $stmt->execute([
                'email' => $email,
                'password_hash' => $hash,
                'name' => $name,
            ]);
            $userId = (int)db()->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                auth_error('Пользователь с таким email уже существует', 409);
            }
            auth_error('Не удалось создать пользователя', 500);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;

        $row = require_user_row();
        $mailSent = auth_send_verification_code($row, true);
        $fresh = require_user_row();
        $message = $mailSent
            ? 'Код подтверждения отправлен на ваш email.'
            : 'Аккаунт создан, но письмо с кодом пока не отправилось. Нажмите «Отправить код повторно».';

        auth_pending_verification_response($fresh, $message, $mailSent, 201);
        break;

    case 'login':
        require_method('POST');
        verify_csrf();
        check_rate_limit('auth_attempt', 10);

        $payload = request_json();
        $email = auth_input_email($payload);
        $password = auth_input_password($payload);
        $row = auth_find_user_by_email($email);

        if (!$row || !password_verify($password, (string)$row['password_hash'])) {
            auth_error('Неверный email или пароль', 401);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$row['id'];

        if (!(int)$row['email_verified']) {
            $mailSent = null;
            $message = 'Введите 6-значный код из письма.';

            if (!auth_time_in_future($row['email_verification_expires_at'] ?? null)) {
                $mailSent = auth_send_verification_code($row, true);
                $message = $mailSent
                    ? 'Мы отправили новый код подтверждения на ваш email.'
                    : 'Код подтверждения не удалось отправить автоматически. Нажмите «Отправить код повторно».';
            }

            $fresh = require_user_row();
            auth_pending_verification_response($fresh, $message, $mailSent);
        }

        json_response(['user' => public_user_payload($row)]);
        break;

    case 'verify_email_code':
        require_method('POST');
        verify_csrf();
        check_rate_limit('verify_email_code', 10);

        $row = require_user_row();
        if ((int)$row['email_verified']) {
            json_response([
                'status' => 'verified',
                'message' => 'Email уже подтверждён.',
                'user' => public_user_payload($row),
            ]);
        }

        $payload = request_json();
        $code = auth_input_verification_code($payload);

        if ((string)($row['email_verification_code'] ?? '') !== $code || !auth_time_in_future($row['email_verification_expires_at'] ?? null)) {
            auth_error('Код недействителен или истёк', 400, ['code' => 'invalid_verification_code']);
        }

        $stmt = db()->prepare(
            'UPDATE users
             SET
                email_verified = 1,
                uses_left = CASE WHEN uses_left > 0 THEN uses_left ELSE 1 END,
                email_verification_code = NULL,
                email_verification_expires_at = NULL,
                email_verification_sent_at = NULL
             WHERE id = :id'
        );
        $stmt->execute(['id' => (int)$row['id']]);

        $fresh = require_user_row();
        json_response([
            'status' => 'verified',
            'message' => 'Email подтверждён.',
            'user' => public_user_payload($fresh),
        ]);
        break;

    case 'resend_verification':
        require_method('POST');
        verify_csrf();

        $row = require_user_row();
        if ((int)$row['email_verified']) {
            auth_error('Email уже подтверждён', 400);
        }

        if (auth_time_within_last_seconds($row['email_verification_sent_at'] ?? null, 120)) {
            auth_error('Новый код можно отправить не чаще одного раза в 2 минуты', 429, [
                'code' => 'verification_rate_limited',
            ]);
        }

        $mailSent = auth_send_verification_code($row, true);
        $fresh = require_user_row();
        $message = $mailSent
            ? 'Новый код подтверждения отправлен.'
            : 'Не удалось отправить код. Попробуйте ещё раз чуть позже.';

        auth_pending_verification_response($fresh, $message, $mailSent);
        break;

    case 'request_password_reset':
        require_method('POST');
        verify_csrf();
        check_rate_limit('password_reset_request', 5);

        $payload = request_json();
        $email = auth_input_email($payload);
        $row = auth_find_user_by_email($email);

        if ($row && (int)$row['email_verified']) {
            if (!auth_time_within_last_seconds($row['password_reset_sent_at'] ?? null, 120)) {
                $token = generate_secure_token();
                $expiresAt = auth_future_datetime_string(PASSWORD_RESET_TTL_MINUTES);
                $stmt = db()->prepare(
                    'UPDATE users
                     SET
                        password_reset_token = :token,
                        password_reset_expires_at = :expires_at,
                        password_reset_sent_at = NOW()
                     WHERE id = :id'
                );
                $stmt->execute([
                    'id' => (int)$row['id'],
                    'token' => $token,
                    'expires_at' => $expiresAt,
                ]);
                send_password_reset_email((string)$row['email'], (string)$row['name'], $token);
            }
        }

        auth_password_reset_message();
        break;

    case 'consume':
        require_method('POST');
        verify_csrf();
        $payload = request_json();

        $row = require_user_row();
        if (!(int)($row['email_verified'] ?? 0)) {
            auth_error('Подтвердите email для использования сервиса', 403, [
                'code' => 'email_not_verified',
                'user' => public_user_payload($row),
            ]);
        }

        if ((int)$row['uses_left'] <= 0) {
            auth_error('Сессии закончились', 403, ['user' => public_user_payload($row)]);
        }

        $stmt = db()->prepare('UPDATE users SET uses_left = uses_left - 1 WHERE id = :id AND uses_left > 0');
        $stmt->execute(['id' => (int)$row['id']]);

        $fresh = require_user_row();
        if ((int)$stmt->rowCount() === 0) {
            auth_error('Сессии закончились', 403, ['user' => public_user_payload($fresh)]);
        }

        auth_store_consume_record(
            (int)$fresh['id'],
            trim((string)($payload['module'] ?? '')),
            trim((string)($payload['reason'] ?? ''))
        );

        json_response(['user' => public_user_payload($fresh)]);
        break;

    case 'refund':
        require_method('POST');
        verify_csrf();
        check_rate_limit('session_refund', 1);

        $row = require_user_row();
        $payload = request_json();
        $record = auth_last_consume_record();

        if ($record === null || (int)$record['user_id'] !== (int)$row['id']) {
            auth_clear_consume_record();
            auth_error('Нет подходящей сессии для возврата', 409, ['user' => public_user_payload($row)]);
        }

        if (!empty($record['refunded'])) {
            auth_error('Сессия уже была возвращена', 409, ['user' => public_user_payload($row)]);
        }

        if ((time() - (int)$record['consumed_at']) > 120) {
            auth_clear_consume_record();
            auth_error('Окно возврата уже истекло', 409, ['user' => public_user_payload($row)]);
        }

        $requestedModule = trim((string)($payload['module'] ?? ''));
        if (
            $requestedModule !== ''
            && (string)($record['module'] ?? '') !== ''
            && $requestedModule !== (string)$record['module']
        ) {
            auth_error('Нет подходящей сессии для возврата', 409, ['user' => public_user_payload($row)]);
        }

        $stmt = db()->prepare('UPDATE users SET uses_left = uses_left + 1 WHERE id = :id');
        $stmt->execute(['id' => (int)$row['id']]);
        auth_mark_consume_refunded();

        $fresh = require_user_row();
        json_response(['user' => public_user_payload($fresh)]);
        break;

    case 'logout':
        require_method('POST');
        verify_csrf();
        auth_clear_consume_record();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, [
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'] ?: '',
                'secure' => (bool)$params['secure'],
                'httponly' => (bool)$params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        session_destroy();
        json_response(['success' => true]);
        break;

    default:
        auth_error('Неизвестное действие', 404);
}
