<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

boot_session();

function reset_password_find_user(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }

    $stmt = db()->prepare(
        'SELECT id, email, name
         FROM users
         WHERE password_reset_token = :token
           AND password_reset_expires_at > NOW()
         LIMIT 1'
    );
    $stmt->execute(['token' => $token]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function reset_password_render(string $title, string $message, bool $showForm, string $token = '', string $error = '', string $success = ''): void
{
    $csrf = htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8');
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
    $safeToken = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
    $safeError = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    $safeSuccess = htmlspecialchars($success, ENT_QUOTES, 'UTF-8');

    ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $safeTitle ?></title>
  <style>
    :root{color-scheme:dark;--bg:#0b0b12;--panel:#171723;--border:rgba(255,255,255,.09);--text:#f4f5fb;--muted:#a6abc4;--accent:#6c5ce7;--danger:#ff7675;--success:#00b894}
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:radial-gradient(circle at top,#1a1b2d 0%,#0b0b12 60%);font-family:"Noto Sans",system-ui,sans-serif;color:var(--text)}
    .card{width:min(100%,420px);background:var(--panel);border:1px solid var(--border);border-radius:20px;padding:28px;box-shadow:0 24px 80px rgba(0,0,0,.35)}
    h1{margin:0 0 12px;font-size:1.5rem}
    p{margin:0 0 18px;line-height:1.55;color:var(--muted)}
    label{display:block;margin:0 0 8px;font-size:.92rem;color:var(--text)}
    input{width:100%;padding:14px 16px;border-radius:14px;border:1px solid var(--border);background:#10101a;color:var(--text);font-size:1rem}
    input + label{margin-top:14px}
    button,a.cta{display:inline-flex;align-items:center;justify-content:center;width:100%;margin-top:18px;padding:14px 18px;border-radius:14px;border:none;background:var(--accent);color:#fff;text-decoration:none;font-weight:700;cursor:pointer}
    .error,.success{margin-top:14px;padding:12px 14px;border-radius:14px;font-size:.92rem}
    .error{background:rgba(255,118,117,.12);border:1px solid rgba(255,118,117,.25);color:#ffd5d4}
    .success{background:rgba(0,184,148,.12);border:1px solid rgba(0,184,148,.25);color:#cbfff1}
    .hint{margin-top:12px;font-size:.84rem}
  </style>
</head>
<body>
  <div class="card">
    <h1><?= $safeTitle ?></h1>
    <p><?= $safeMessage ?></p>

    <?php if ($safeError !== ''): ?>
      <div class="error"><?= $safeError ?></div>
    <?php endif; ?>

    <?php if ($safeSuccess !== ''): ?>
      <div class="success"><?= $safeSuccess ?></div>
    <?php endif; ?>

    <?php if ($showForm): ?>
      <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="token" value="<?= $safeToken ?>">
        <label for="password">Новый пароль</label>
        <input id="password" name="password" type="password" minlength="6" required placeholder="Минимум 6 символов">
        <label for="password_confirm">Повторите пароль</label>
        <input id="password_confirm" name="password_confirm" type="password" minlength="6" required placeholder="Повторите новый пароль">
        <button type="submit">Сохранить новый пароль</button>
      </form>
      <p class="hint">После смены пароля вы сможете войти в кабинет с новым паролем.</p>
    <?php else: ?>
      <a class="cta" href="/app?auth=login">Войти в кабинет</a>
    <?php endif; ?>
  </div>
</body>
</html>
    <?php
    exit;
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    verify_csrf();

    $token = trim((string)($_POST['token'] ?? ''));
    $user = reset_password_find_user($token);
    if (!$user) {
        reset_password_render(
            'Ссылка недействительна',
            'Ссылка для смены пароля недействительна или устарела.',
            false
        );
    }

    $password = (string)($_POST['password'] ?? '');
    $passwordConfirm = (string)($_POST['password_confirm'] ?? '');
    if (mb_strlen($password) < 6) {
        reset_password_render(
            'Смена пароля',
            'Задайте новый пароль для аккаунта.',
            true,
            $token,
            'Пароль должен содержать минимум 6 символов.'
        );
    }

    if (!hash_equals($password, $passwordConfirm)) {
        reset_password_render(
            'Смена пароля',
            'Задайте новый пароль для аккаунта.',
            true,
            $token,
            'Пароли не совпадают.'
        );
    }

    $stmt = db()->prepare(
        'UPDATE users
         SET
            password_hash = :password_hash,
            password_reset_token = NULL,
            password_reset_expires_at = NULL,
            password_reset_sent_at = NULL
         WHERE id = :id'
    );
    $stmt->execute([
        'id' => (int)$user['id'],
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    reset_password_render(
        'Пароль обновлён',
        'Новый пароль сохранён. Теперь вы можете войти в кабинет.',
        false,
        '',
        '',
        'Пароль успешно изменён.'
    );
}

$token = trim((string)($_GET['token'] ?? ''));
$user = reset_password_find_user($token);
if (!$user) {
    reset_password_render(
        'Ссылка недействительна',
        'Ссылка для смены пароля недействительна или устарела.',
        false
    );
}

reset_password_render(
    'Смена пароля',
    'Задайте новый пароль для аккаунта ' . (string)$user['email'] . '.',
    true,
    $token
);
