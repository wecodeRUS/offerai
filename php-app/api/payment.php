<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

require_method('POST');

function payment_error(string $message, int $status = 400, array $extra = []): void
{
    json_response(array_merge(['error' => $message], $extra), $status);
}

function payment_basic_auth_header(): string
{
    return 'Authorization: Basic ' . base64_encode(YUKASSA_SHOP_ID . ':' . YUKASSA_SECRET);
}

function yookassa_request(string $method, string $url, ?array $payload = null, array $extraHeaders = []): array
{
    $headers = array_merge([
        'Content-Type: application/json',
        payment_basic_auth_header(),
    ], $extraHeaders);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 15,
    ]);

    if ($payload !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $curlError !== '') {
        payment_error('Не удалось связаться с ЮKassa', 502);
    }

    $data = json_decode((string)$response, true);
    if (!is_array($data)) {
        payment_error('ЮKassa вернула некорректный ответ', 502);
    }

    return [$httpCode, $data];
}

function handle_create_payment(array $payload): void
{
    boot_session();
    verify_csrf();
    $user = require_user_row();
    $planId = (string)($payload['plan'] ?? '');
    $plans = plan_catalog();

    if (!isset($plans[$planId])) {
        payment_error('Неизвестный тариф', 422);
    }

    $planOrder = [null => 0, 'start' => 1, 'prep' => 2, 'offer' => 3];
    $currentPlan = $user['plan'] ?? null;
    $requestedOrder = $planOrder[$planId] ?? 0;
    $currentOrder = $planOrder[$currentPlan] ?? 0;

    if ($requestedOrder <= $currentOrder) {
        payment_error(
            'Нельзя приобрести тариф ниже или равный текущему. ' .
            'Доступен только переход на более высокий тариф.',
            422
        );
    }

    $plan = $plans[$planId];
    $idempotenceKey = bin2hex(random_bytes(16));
    $amount = number_format((float)$plan['price'], 2, '.', '');
    $returnUrl = app_base_url() . '/app?payment=success';

    $requestBody = [
        'amount' => [
            'value' => $amount,
            'currency' => 'RUB',
        ],
        'capture' => true,
        'confirmation' => [
            'type' => 'redirect',
            'return_url' => $returnUrl,
        ],
        'description' => 'ОфферАИ — тариф ' . $plan['name'],
        'metadata' => [
            'user_id' => (string)$user['id'],
            'plan' => $planId,
            'uses_to_add' => (string)$plan['uses'],
        ],
    ];

    [$status, $response] = yookassa_request(
        'POST',
        'https://api.yookassa.ru/v3/payments',
        $requestBody,
        ['Idempotence-Key: ' . $idempotenceKey]
    );

    if ($status < 200 || $status >= 300 || empty($response['confirmation']['confirmation_url'])) {
        payment_error($response['description'] ?? 'Не удалось создать платёж', 502);
    }

    json_response([
        'confirmation_url' => (string)$response['confirmation']['confirmation_url'],
        'payment_id' => (string)($response['id'] ?? ''),
    ]);
}

function handle_webhook(array $payload): void
{
    $event = (string)($payload['event'] ?? '');
    $object = $payload['object'] ?? [];
    if (!is_array($object)) {
        payment_error('Некорректный webhook payload', 400);
    }

    $paymentId = (string)($object['id'] ?? '');
    if ($paymentId === '') {
        payment_error('В webhook нет payment id', 400);
    }

    if ($event !== 'payment.succeeded' && (string)($object['status'] ?? '') !== 'succeeded') {
        json_response(['status' => 'ignored']);
    }

    [$status, $payment] = yookassa_request(
        'GET',
        'https://api.yookassa.ru/v3/payments/' . rawurlencode($paymentId)
    );

    if ($status < 200 || $status >= 300) {
        payment_error('Не удалось подтвердить платёж в ЮKassa', 502);
    }

    if ((string)($payment['status'] ?? '') !== 'succeeded') {
        json_response(['status' => 'ignored']);
    }

    $metadata = is_array($payment['metadata'] ?? null) ? $payment['metadata'] : [];
    $userId = (int)($metadata['user_id'] ?? 0);
    $planId = (string)($metadata['plan'] ?? '');
    $usesToAdd = (int)($metadata['uses_to_add'] ?? 0);
    $plans = plan_catalog();

    if ($userId <= 0 || $planId === '' || !isset($plans[$planId])) {
        payment_error('Верифицированный платёж не содержит валидные metadata', 422);
    }

    if ($usesToAdd <= 0) {
        $usesToAdd = (int)$plans[$planId]['uses'];
    }

    $amount = (float)($payment['amount']['value'] ?? 0);
    $pdo = db();

    $existing = $pdo->prepare('SELECT id FROM payments WHERE yukassa_payment_id = :payment_id LIMIT 1');
    $existing->execute(['payment_id' => $paymentId]);
    if ($existing->fetch()) {
        json_response(['status' => 'already_processed']);
    }

    $pdo->beginTransaction();
    try {
        $insert = $pdo->prepare(
            'INSERT INTO payments (user_id, plan, amount, yukassa_payment_id) VALUES (:user_id, :plan, :amount, :payment_id)'
        );
        $insert->execute([
            'user_id' => $userId,
            'plan' => $planId,
            'amount' => $amount,
            'payment_id' => $paymentId,
        ]);

        $update = $pdo->prepare(
            'UPDATE users SET plan = :plan, uses_left = uses_left + :uses_to_add WHERE id = :user_id'
        );
        $update->execute([
            'plan' => $planId,
            'uses_to_add' => $usesToAdd,
            'user_id' => $userId,
        ]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        payment_error('Не удалось зафиксировать оплату', 500);
    }

    json_response(['status' => 'ok']);
}

$payload = request_json();

if (($payload['action'] ?? '') === 'create') {
    handle_create_payment($payload);
}

handle_webhook($payload);
