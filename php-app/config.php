<?php
declare(strict_types=1);

define('DEMO_MODE', false);
define('APP_BASE_URL', 'https://getofferai.ru');
define('AITUNNEL_KEY', 'sk-aitunnel-wMmov7tsR6Wf14eZHnoiH3h5y4pY6f2m');
define('AITUNNEL_MODEL', 'claude-sonnet-4.6');
define('DB_HOST', 'localhost');
define('DB_NAME', 'endolomo_vacancy');
define('DB_USER', 'endolomo_vacancy');
define('DB_PASS', 'Y!vbE4zew0EI');
define('YUKASSA_SHOP_ID', 'ВСТАВЬ');
define('YUKASSA_SECRET', 'ВСТАВЬ');
define('TURNSTILE_SITE_KEY', '0x4AAAAAACwWWn3tGpoiBGfM');
define('TURNSTILE_SECRET_KEY', '0x4AAAAAACwWWqkVrDuxLjZ0rEnlVZj_SZw');
define('APP_EMAIL_FROM', 'noreply@getofferai.ru');
define('APP_EMAIL_NAME', 'ОфферАИ');
define('SMTP_HOST', 'smtp.beget.com');
define('SMTP_PORT', 465);
define('SMTP_ENCRYPTION', 'ssl');
define('SMTP_USER', 'noreply@getofferai.ru');
define('SMTP_PASS', 'yMB1q*cjpfar');
define('EMAIL_VERIFICATION_CODE_TTL_MINUTES', 15);
define('PASSWORD_RESET_TTL_MINUTES', 60);
define('SESSION_SECRET', 'offerai2026xK9mP3nQ7rT1vW5yZ8aB2');

date_default_timezone_set('Europe/Moscow');

function app_base_url(): string
{
    if (APP_BASE_URL !== 'https://example.com') {
        return rtrim(APP_BASE_URL, '/');
    }

    $https = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    );
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host;
}

function sanitize_mail_header(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

function smtp_read_response($socket): string
{
    $response = '';
    while (!feof($socket)) {
        $line = fgets($socket, 515);
        if ($line === false) {
            break;
        }
        $response .= $line;
        if (preg_match('/^\d{3}\s/', $line) === 1) {
            break;
        }
    }

    return $response;
}

function smtp_expect($socket, array $allowedCodes): string
{
    $response = smtp_read_response($socket);
    $code = (int)substr($response, 0, 3);
    if (!in_array($code, $allowedCodes, true)) {
        throw new RuntimeException('SMTP error: ' . trim($response));
    }

    return $response;
}

function smtp_command($socket, string $command, array $allowedCodes): string
{
    $written = fwrite($socket, $command . "\r\n");
    if ($written === false) {
        throw new RuntimeException('SMTP write failed');
    }

    return smtp_expect($socket, $allowedCodes);
}

function send_plain_mail(string $email, string $subject, string $body): bool
{
    $to = trim($email);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $fromEmail = sanitize_mail_header(APP_EMAIL_FROM);
    $fromName = sanitize_mail_header(APP_EMAIL_NAME);
    $host = trim(SMTP_HOST);
    $port = (int)SMTP_PORT;
    $encryption = strtolower(trim(SMTP_ENCRYPTION));
    $smtpUser = trim(SMTP_USER);
    $smtpPass = SMTP_PASS;
    if (
        $fromEmail === ''
        || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)
        || $host === ''
        || $port <= 0
        || !in_array($encryption, ['', 'ssl', 'tls'], true)
        || $smtpUser === ''
        || $smtpPass === ''
    ) {
        return false;
    }

    $remoteHost = ($encryption === 'ssl' ? 'ssl://' : '') . $host;
    $helloHost = parse_url(app_base_url(), PHP_URL_HOST);
    if (!is_string($helloHost) || $helloHost === '') {
        $helloHost = 'localhost';
    }

    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
            'peer_name' => $host,
            'SNI_enabled' => true,
        ],
    ]);

    $socket = @stream_socket_client(
        $remoteHost . ':' . $port,
        $errno,
        $errstr,
        20,
        STREAM_CLIENT_CONNECT,
        $context
    );
    if (!$socket) {
        return false;
    }

    stream_set_timeout($socket, 20);

    try {
        smtp_expect($socket, [220]);
        smtp_command($socket, 'EHLO ' . $helloHost, [250]);

        if ($encryption === 'tls') {
            smtp_command($socket, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('SMTP TLS failed');
            }
            smtp_command($socket, 'EHLO ' . $helloHost, [250]);
        }

        smtp_command($socket, 'AUTH LOGIN', [334]);
        smtp_command($socket, base64_encode($smtpUser), [334]);
        smtp_command($socket, base64_encode($smtpPass), [235]);
        smtp_command($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
        smtp_command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
        smtp_command($socket, 'DATA', [354]);

        $encodedSubject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");
        $headers = [
            'Date: ' . date(DATE_RFC2822),
            'From: ' . $fromName . ' <' . $fromEmail . '>',
            'To: <' . $to . '>',
            'Subject: ' . $encodedSubject,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
        ];

        $normalizedBody = str_replace(["\r\n", "\r"], "\n", $body);
        $encodedBody = chunk_split(base64_encode($normalizedBody), 76, "\r\n");
        $message = implode("\r\n", $headers) . "\r\n\r\n" . $encodedBody . "\r\n.";

        $written = fwrite($socket, $message . "\r\n");
        if ($written === false) {
            throw new RuntimeException('SMTP data write failed');
        }

        smtp_expect($socket, [250]);
        smtp_command($socket, 'QUIT', [221]);
        fclose($socket);
        return true;
    } catch (Throwable $e) {
        if (is_resource($socket)) {
            @fwrite($socket, "QUIT\r\n");
            @fclose($socket);
        }
        error_log('SMTP send failed: ' . $e->getMessage());
        return false;
    }
}

function generate_six_digit_code(): string
{
    return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function generate_secure_token(int $bytes = 32): string
{
    return bin2hex(random_bytes($bytes));
}

function send_verification_email_code(string $email, string $name, string $code): bool
{
    $safeName = trim($name) !== '' ? trim($name) : 'друг';
    $subject = 'Код подтверждения — ОфферАИ';
    $body = "Привет, {$safeName}!\n\n"
        . "Ваш код подтверждения для ОфферАИ: {$code}\n\n"
        . "Код действует " . EMAIL_VERIFICATION_CODE_TTL_MINUTES . " минут.\n"
        . "Если вы не регистрировались — просто проигнорируйте это письмо.\n\n"
        . "— ОфферАИ";

    return send_plain_mail($email, $subject, $body);
}

function send_password_reset_email(string $email, string $name, string $token): bool
{
    $safeName = trim($name) !== '' ? trim($name) : 'друг';
    $resetUrl = app_base_url() . '/api/reset_password.php?token=' . urlencode($token);
    $subject = 'Сброс пароля — ОфферАИ';
    $body = "Привет, {$safeName}!\n\n"
        . "Чтобы задать новый пароль, перейдите по ссылке:\n\n"
        . $resetUrl . "\n\n"
        . "Ссылка действует " . PASSWORD_RESET_TTL_MINUTES . " минут.\n"
        . "Если вы не запрашивали сброс пароля — просто проигнорируйте это письмо.\n\n"
        . "— ОфферАИ";

    return send_plain_mail($email, $subject, $body);
}

function plan_catalog(): array
{
    return [
        'start' => ['name' => 'Старт', 'price' => 490, 'uses' => 8],
        'prep' => ['name' => 'Подготовка', 'price' => 990, 'uses' => 25],
        'offer' => ['name' => 'Оффер', 'price' => 1990, 'uses' => 40],
    ];
}

function modules_by_plan(): array
{
    return [
        null => [
            'kickoff_analysis',
            'decode',
            'resume_quick',
            'resume_standard',
            'resume_deep',
            'pitch',
            'practice_q',
            'practice_score',
        ],
        'start' => [
            'kickoff_analysis',
            'decode',
            'resume_quick',
            'resume_standard',
            'resume_deep',
            'pitch',
            'practice_q',
            'practice_score',
        ],
        'prep' => [
            'kickoff_analysis',
            'decode',
            'resume_quick',
            'resume_standard',
            'resume_deep',
            'pitch',
            'practice_q',
            'practice_score',
            'hype',
            'mock_q',
            'mock_debrief',
            'prep',
            'concerns',
            'story',
        ],
        'offer' => [
            'kickoff_analysis',
            'decode',
            'resume_quick',
            'resume_standard',
            'resume_deep',
            'pitch',
            'practice_q',
            'practice_score',
            'hype',
            'mock_q',
            'mock_debrief',
            'prep',
            'concerns',
            'story',
            'salary',
            'negotiate',
        ],
    ];
}

function allowed_ai_modules(): array
{
    return [
        'kickoff_analysis',
        'decode',
        'resume_quick',
        'resume_standard',
        'resume_deep',
        'pitch',
        'practice_q',
        'practice_score',
        'mock_q',
        'mock_debrief',
        'prep',
        'concerns',
        'story',
        'hype',
        'salary',
        'negotiate',
    ];
}

function demo_response_for_module(string $module): string
{
    $normalized = str_starts_with($module, 'resume_') ? 'resume' : $module;

    $responses = [
        'kickoff_analysis' => "**Короткий разбор профиля**\n\nУ вас уже есть базовый контур для подготовки: понятная роль и рабочий опыт. Главный риск — пойти сразу в отработку ответов, не зафиксировав сильные формулировки под целевую вакансию.\n\n**С чего начать:**\n1. Сначала разберите вакансию, чтобы понять реальные требования и скрытые ожидания.\n2. Затем обновите резюме под эти требования.\n3. Только после этого переходите к тренировке ответов.\n\n**Следующий шаг на сегодня:** вставьте одну реальную вакансию в модуль разбора и соберите список ключевых требований.",
        'decode' => "**Разбор вакансии по 6 важным пунктам:**\n\n**1. Обязательные навыки** [высокая уверенность]\n- Опыт от 3 лет в разработке\n- Знание современных технологий\n- Умение работать в команде\n\n**2. Какой человек нужен компании** [средняя уверенность]\n- Компания ценит самостоятельность\n- Ожидается инициативность\n- Плоская структура, много общения\n\n**3. Скрытые требования** [низкая уверенность]\n- Возможно, нужен опыт наставничества\n- Возможно, ищут будущего руководителя\n- Это стоит уточнить у рекрутера\n\n**4. Тревожные сигналы** [средняя уверенность]\n- \"Многозадачность\" может означать перегрузку\n- \"Быстрый темп\" может означать постоянные срочные задачи\n\n**5. Что спросить у рекрутера** [высокая уверенность]\n- Размер команды и основные технологии\n- Как устроена проверка кода\n- Какие возможности роста есть в компании\n\n**6. Как откликаться на вакансию** [высокая уверенность]\n- Делайте акцент на конкретных достижениях с цифрами\n- Покажите опыт ответственности и лидерства\n- Подстройте резюме под ключевые требования вакансии",
        'resume' => "**Разбор резюме**\n\n**Что уже хорошо:**\n- Понятная структура\n- Видны основные зоны ответственности\n- Опыт указан в логичном порядке\n\n**Что улучшить без выдумок:**\n\n**Пункт 1 (было):** \"Работал над проектами компании\"\n**Пункт 1 (стало):** \"Отвечал за [нужно уточнение], чтобы [нужен результат]\"\n\n**Пункт 2 (было):** \"Участвовал в разработке серверной части\"\n**Пункт 2 (стало):** \"Разрабатывал серверную часть для [нужно уточнение], что помогло [нужен результат]\"\n\n**Пункт 3 (было):** \"Помогал младшим сотрудникам\"\n**Пункт 3 (стало):** \"Поддерживал коллег в задачах по [нужно уточнение] и помогал быстрее входить в рабочий контекст\"\n\n**Если в резюме нет технологий:**\n- Не дописывайте стек от себя\n- Используйте пометку [добавьте технологию]\n- Подтверждайте только то, что реально было в опыте\n\n**Тревожные места и как их объяснить:**\n\nПробел в опыте → \"В этот период я занимался [нужно уточнение]. За это время я сохранил рабочий ритм и могу коротко объяснить, чем был занят.\"\n\nЧастая смена работ → \"Каждый переход был осознанным шагом: на одной роли я получил [нужно уточнение], на следующей усилил [нужно уточнение]. Сейчас ищу место, где смогу надолго вкладываться в результат.\"\n\nНет таких проблем → \"Явных тревожных мест не обнаружено.\"",
        'pitch' => "**Ваш рассказ о себе**\n\n**30 секунд:**\n\"Я специалист с сильным опытом в своём треке. В последние годы я решал задачи, где нужно было улучшать результат и помогать команде двигаться быстрее. Сейчас ищу роль, где смогу применить этот опыт на более высоком уровне.\"\n\n**60 секунд:**\n\"Сейчас я работаю на стыке результата и ответственности. За последний год помог команде вырасти по ключевым показателям и взять на себя более сложные задачи. Моя сильная сторона — находить понятные решения для непростых проблем.\"\n\n**90 секунд:**\nДобавьте один конкретный пример с цифрой и личным выводом — именно это делает рассказ сильным.",
        'practice_q' => "Расскажите о ситуации, когда вам пришлось принять сложное решение в условиях неопределённости. Что было на кону, какие варианты вы рассматривали и к чему это привело?",
        'practice_score' => "**Оценка вашего ответа:**\n\nСодержание: 3/5\nСтруктура: 4/5\nПопадание в тему: 4/5\nУбедительность: 3/5\nУникальность: 2/5\n\n**Что хорошо:**\n- Понятный ход ответа\n- Хорошее попадание в вопрос\n- Логика рассуждений читается легко\n\n**Что улучшить:**\n- Добавьте конкретные цифры: сроки, масштаб, результат\n- Сформулируйте личный вывод\n- Усильте уникальность: почему ваш подход был не таким, как у других",
        'mock_q' => "Опишите проект, которым вы больше всего гордитесь за последние 2 года. Какую роль вы играли и каков был результат?",
        'mock_debrief' => "**Полный разбор пробного интервью**\n\nСодержание: 3/5\nСтруктура: 4/5\nПопадание в тему: 3/5\nУбедительность: 4/5\nУникальность: 3/5\n\nОценка интервью: Берём\n\n**Общее впечатление:**\nИнтервью получилось крепким. У вас хороший опыт, и вы отвечаете последовательно. Чтобы дойти до уровня \"Однозначно берём\", не хватает ярких деталей и более сильных личных выводов.",
        'prep' => "**Подготовка к собеседованию**\n\n**Вероятные вопросы (по приоритету):**\n- Почему вы хотите работать именно у нас?\n- Расскажите, как вы выстраивали или усиливали команду\n- Как вы решаете конфликты в команде?\n- Как бы вы спроектировали систему?\n- Расскажите о проекте, который не получился\n\n**Стратегия интервью:**\n- Начинайте ответ с главного результата\n- В каждом ответе старайтесь назвать хотя бы одну цифру\n- Подготовьте 2-3 умных вопроса в конце",
        'concerns' => "**Возражения рекрутера — анализ и ответы**\n\n**1. Частая смена работы**\nОтвет: \"Каждый переход был шагом вперёд: новая роль давала мне новый уровень задач и ответственности. Сейчас я ищу место для долгосрочного вклада.\"\n\n**2. Слишком сильный кандидат для роли**\nОтвет: \"Меня привлекает именно масштаб задач. Мой опыт поможет быстрее дать результат и быть полезным команде с первых месяцев.\"\n\n**3. Пробелы в опыте**\nОтвет: \"Я быстро осваиваю новые направления. Например, похожий навык я довёл до рабочего уровня за короткий срок и уже применял на практике.\"",
        'story' => "**История из опыта**\n\n**Ситуация:** [Что происходило]\n\n**Задача:** [Что нужно было сделать]\n\n**Действие:** [Что именно сделали вы]\n\n**Результат:** [Какой вышел результат, желательно с цифрами]\n\n**Навык:** решение проблем, лидерство\n**Сильная сторона:** системное мышление\n\n**Уникальный вывод:** Когда команда сопротивляется изменениям, лучше не убеждать всех сразу, а показать пользу на одном понятном примере.",
        'hype' => "**Настрой перед собесом**\n\nВы нервничаете — и это нормально. Нервозность означает, что вам не всё равно.\n\n**Что важно помнить:**\n1. Вас уже заметили.\n2. Ваш опыт настоящий.\n3. Компания тоже хочет, чтобы вы подошли.\n\n**Что сделать прямо сейчас:**\n- Спокойно подышите по квадрату\n- Расправьте плечи и встаньте устойчиво на 2 минуты\n- Сфокусируйтесь только на первых 30 секундах разговора",
        'salary' => "**Как называть сумму**\n\n**Принципы разговора о деньгах:**\n1. Не называйте цифру первым\n2. Начинайте с верхней границы\n3. Смотрите на весь пакет\n\n**Готовая фраза:**\n\"Исходя из моего опыта и данных, которые я собрал, мои ожидания находятся в диапазоне [ваша верхняя граница]. Но я готов обсуждать весь пакет условий.\"",
        'negotiate' => "**Переговоры по офферу**\n\n**1. Основной оклад**\nСравните его со своими данными из открытых источников.\n\n**2. Бонусы и условия выплаты**\nУточните: бонус гарантирован или зависит от результата?\n\n**3. Остальные условия**\nУдалёнка, гибкий график, обучение, конференции — это тоже часть ценности предложения.\n\n**Готовая фраза:**\n\"Спасибо за оффер! Я очень хочу присоединиться к команде. У меня есть несколько пунктов, которые я хотел бы обсудить.\"",
    ];

    return $responses[$normalized] ?? "Это демонстрационный ответ выбранного раздела. В рабочем режиме здесь будет персональный ответ, построенный на вашем профиле и контексте.\n\nЧтобы включить рабочий режим:\n1. Зарегистрируйтесь на aitunnel.ru\n2. Пополните баланс\n3. Вставьте API-ключ в код\n4. Поставьте DEMO_MODE = false";
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function boot_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    );

    session_name('offerai_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_json(): array
{
    static $cached = null;
    static $loaded = false;

    if ($loaded) {
        return $cached ?? [];
    }

    $loaded = true;
    $raw = file_get_contents('php://input') ?: '';
    if ($raw === '') {
        $cached = [];
        return $cached;
    }

    $data = json_decode($raw, true);
    $cached = is_array($data) ? $data : [];

    return $cached;
}

function require_method(string $expected): void
{
    if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== strtoupper($expected)) {
        json_response(['error' => 'Метод не поддерживается'], 405);
    }
}

function get_csrf_token(): string
{
    boot_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string)$_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    boot_session();
    $payload = request_json();
    $token = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($payload['csrf_token'] ?? ($_POST['csrf_token'] ?? '')));
    $sessionToken = (string)($_SESSION['csrf_token'] ?? '');

    if ($sessionToken === '' || $token === '' || !hash_equals($sessionToken, $token)) {
        json_response(['error' => 'Неверный CSRF-токен'], 403);
    }
}

function check_rate_limit(string $endpoint, int $maxPerMinute): void
{
    $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    $pdo = db();

    $pdo->prepare(
        'DELETE FROM rate_limits
         WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 MINUTE)'
    )->execute();

    $stmt = $pdo->prepare(
        'SELECT hit_count
         FROM rate_limits
         WHERE ip = :ip AND endpoint = :endpoint
         LIMIT 1'
    );
    $stmt->execute([
        'ip' => $ip,
        'endpoint' => $endpoint,
    ]);
    $row = $stmt->fetch();

    if ($row && (int)$row['hit_count'] >= $maxPerMinute) {
        json_response(['error' => 'Слишком много запросов. Подождите минуту.'], 429);
    }

    $pdo->prepare(
        'INSERT INTO rate_limits (ip, endpoint, hit_count)
         VALUES (:ip, :endpoint, 1)
         ON DUPLICATE KEY UPDATE hit_count = hit_count + 1'
    )->execute([
        'ip' => $ip,
        'endpoint' => $endpoint,
    ]);
}

function current_user_row(): ?array
{
    boot_session();
    $userId = (int)($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        return null;
    }

    $stmt = db()->prepare(
        'SELECT
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
         WHERE id = :id
         LIMIT 1'
    );
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function require_user_row(): array
{
    $row = current_user_row();
    if (!$row) {
        json_response(['error' => 'Не авторизован'], 401);
    }

    return $row;
}

function public_user_payload(array $row): array
{
    return [
        'id' => (int)$row['id'],
        'email' => (string)$row['email'],
        'name' => (string)$row['name'],
        'plan' => $row['plan'] !== null ? (string)$row['plan'] : null,
        'usesLeft' => (int)$row['uses_left'],
        'emailVerified' => (bool)($row['email_verified'] ?? false),
        'createdAt' => (string)$row['created_at'],
    ];
}
