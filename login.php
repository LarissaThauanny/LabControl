<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (is_logged_in()) redirect('index.php');
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    verify_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['senha'] ?? '');
    $lockedUntil = (int)($_SESSION['login_locked_until'] ?? 0);
    if ($lockedUntil > time()) {
        $error = 'Muitas tentativas. Aguarde alguns minutos para tentar novamente.';
    } else {
        $st = lab_database()->prepare('SELECT id,nome,email,senha_hash,tipo FROM usuarios WHERE email=? LIMIT 1');
        $st->execute([$email]);
        $user = $st->fetch();
        if ($user && password_verify($password, (string)$user['senha_hash'])) {
            session_regenerate_id(true);
            unset($user['senha_hash']);
            $_SESSION['user'] = $user;
            unset($_SESSION['login_errors'], $_SESSION['login_locked_until']);
            redirect('index.php');
        }
        $_SESSION['login_errors'] = (int)($_SESSION['login_errors'] ?? 0) + 1;
        if ($_SESSION['login_errors'] >= 5) {
            $_SESSION['login_locked_until'] = time() + 600;
            $_SESSION['login_errors'] = 0;
        }
        $error = 'E-mail ou senha incorretos.';
    }
}
$flash = take_flash();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Entrar · LabControl</title><link rel="stylesheet" href="<?= h(url('assets/style.css')) ?>"></head>
<body class="login-body"><main class="login-shell">
<div class="login-intro"><div class="brand"><span class="brand-mark">L<span>+</span></span><span class="brand-type">LabControl <small>CONTROLE DE LABORATÓRIO</small></span></div><div><h2>Organize o laboratório com praticidade.</h2><p>Solicitações de alunos e empréstimos acompanhados pela administração.</p></div><span class="intro-bottom">Projeto acadêmico · IFRO</span></div>
<div class="login-card"><div class="login-topline">ACESSO SEGURO</div><h1>Entrar na conta</h1><p>Use seu e-mail e senha para acessar sua área.</p>
<?php if ($error): ?><div class="alert" role="alert"><?= h($error) ?></div><?php endif; ?>
<?php if ($flash): ?><div class="notice" role="status"><?= h($flash) ?></div><?php endif; ?>
<form class="login-form" method="post"><?= csrf_field() ?><label>E-mail<input type="email" name="email" maxlength="190" autocomplete="email" required value="<?= h($_POST['email'] ?? '') ?>"></label><label>Senha<input type="password" name="senha" autocomplete="current-password" required></label><button class="button full-button" type="submit">Entrar →</button></form>
<p class="foot-note">Ainda não tem conta? <a href="<?= h(url('cadastro.php')) ?>">Cadastre-se como aluno</a>.</p>
<a class="back-link" href="<?= h(url('demo.php')) ?>">← Ver demonstração pública</a>
</div></main></body></html>
