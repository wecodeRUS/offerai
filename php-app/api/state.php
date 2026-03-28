<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

boot_session();
$user = require_user_row();
$userId = (int)$user['id'];
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'GET') {
    $stmt = db()->prepare('SELECT state_json FROM coaching_state WHERE user_id = :user_id LIMIT 1');
    $stmt->execute(['user_id' => $userId]);
    $row = $stmt->fetch();

    if (!$row) {
        json_response(['state' => null]);
    }

    $state = json_decode((string)$row['state_json'], true);
    json_response(['state' => is_array($state) ? $state : null]);
}

if ($method === 'POST') {
    verify_csrf();
    $payload = request_json();
    $state = $payload['state'] ?? $payload;

    if (!is_array($state)) {
        json_response(['error' => 'Некорректное состояние'], 422);
    }

    if (isset($state['chatLogs']) && is_array($state['chatLogs'])) {
        $chatLogs = $state['chatLogs'];
        if (count($chatLogs) > 6) {
            $chatLogs = array_slice($chatLogs, -6, null, true);
        }
        foreach ($chatLogs as $key => $log) {
            if (strlen((string)$log) > 51200) {
                unset($chatLogs[$key]);
            }
        }
        $state['chatLogs'] = $chatLogs;
    }

    $json = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        json_response(['error' => 'Не удалось сериализовать состояние'], 422);
    }
    if (strlen($json) > 524288) {
        json_response(['error' => 'Состояние слишком большое'], 422);
    }

    $stmt = db()->prepare(
        'INSERT INTO coaching_state (user_id, state_json)
         VALUES (:user_id, :state_json)
         ON DUPLICATE KEY UPDATE state_json = VALUES(state_json), updated_at = CURRENT_TIMESTAMP'
    );
    $stmt->execute([
        'user_id' => $userId,
        'state_json' => $json,
    ]);

    json_response(['success' => true]);
}

json_response(['error' => 'Метод не поддерживается'], 405);
