<?php
require __DIR__ . '/../inc/bootstrap.php';
if (!empty($_SESSION['admin'])) redirect('index.php');
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $_SESSION['tries'] = ($_SESSION['tries'] ?? 0) + 1;
    if ($_SESSION['tries'] > 8) { $err = 'تلاش‌های ناموفق زیاد بود؛ بعداً دوباره امتحان کنید.'; }
    else {
        $st = db()->prepare('SELECT * FROM admins WHERE username=?'); $st->execute([trim($_POST['username'] ?? '')]); $a = $st->fetch();
        if ($a && password_verify($_POST['password'] ?? '', $a['password'])) {
            session_regenerate_id(true); $_SESSION['admin'] = $a['id']; $_SESSION['tries'] = 0; redirect('index.php');
        }
        usleep(400000); $err = 'نام کاربری یا رمز عبور درست نیست.';
    }
}
?><!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ورود مدیر</title><link rel="stylesheet" href="../assets/style.css"></head>
<body><form method="post" class="login"><h1>ورود به پنل مدیریت</h1>
<?php if ($err): ?><div class="flash err"><?= e($err) ?></div><?php endif; ?>
<?= csrf_field() ?>
<label for="u">نام کاربری</label><input id="u" name="username" autocomplete="username" required autofocus>
<label for="p">رمز عبور</label><input id="p" type="password" name="password" autocomplete="current-password" required>
<p><button class="btn block">ورود</button></p></form></body></html>
