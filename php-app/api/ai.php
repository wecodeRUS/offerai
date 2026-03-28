<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

boot_session();
require_method('POST');
verify_csrf();
$user = require_user_row();
check_rate_limit('ai_request', 20);

if (!(int)($user['email_verified'] ?? 0)) {
    json_response([
        'error' => 'Подтвердите email для использования сервиса',
        'code' => 'email_not_verified',
    ], 403);
}

$payload = request_json();
$module = (string)($payload['module'] ?? '');
$messages = $payload['messages'] ?? null;
$maxTokens = (int)($payload['max_tokens'] ?? 1000);

if ($module === '' || !in_array($module, allowed_ai_modules(), true)) {
    json_response(['error' => 'Неизвестный AI-модуль'], 422);
}

$plan = $user['plan'] ?? null;
$allowedByPlan = modules_by_plan();
$allowedForPlan = $allowedByPlan[$plan] ?? $allowedByPlan[null];
if (!in_array($module, $allowedForPlan, true)) {
    json_response(['error' => 'Этот раздел недоступен на вашем тарифе'], 403);
}

if (!is_array($messages) || $messages === [] || count($messages) > 20) {
    json_response(['error' => 'Некорректный список сообщений'], 422);
}

$sanitizedMessages = [];
foreach ($messages as $message) {
    if (!is_array($message)) {
        continue;
    }

    $role = (string)($message['role'] ?? '');
    $content = trim((string)($message['content'] ?? ''));

    if (!in_array($role, ['system', 'user', 'assistant'], true) || $content === '') {
        continue;
    }

    $sanitizedMessages[] = [
        'role' => $role,
        'content' => mb_substr($content, 0, 12000),
    ];
}

if ($sanitizedMessages === []) {
    json_response(['error' => 'Нет валидных сообщений для отправки'], 422);
}

$maxTokens = max(100, min($maxTokens, 4000));

if (DEMO_MODE) {
    json_response([
        'content' => demo_response_for_module($module),
        'demo' => true,
    ]);
}

$requestBody = [
    'model' => AITUNNEL_MODEL,
    'max_tokens' => $maxTokens,
    'messages' => $sanitizedMessages,
];

$ch = curl_init('https://api.aitunnel.ru/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . AITUNNEL_KEY,
    ],
    CURLOPT_POSTFIELDS => json_encode($requestBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_TIMEOUT => 60,
    CURLOPT_CONNECTTIMEOUT => 15,
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $curlError !== '') {
    json_response(['error' => 'Ошибка соединения с AI-сервисом'], 502);
}

http_response_code($httpCode > 0 ? $httpCode : 502);
header('Content-Type: application/json; charset=utf-8');
echo $response;
exit;
