<?php
/** Instalação: primeiro admin mediante token secreto. Nenhuma senha padrão. */
require_once __DIR__ . '/includes/bootstrap.php';
$pdo = lab_database();
$adminCount = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo='admin'")->fetchColumn();
if ($adminCount > 0) {
    http_response_code(403);
    exit('A conta administrativa inicial ja existe. Esta pagina esta bloqueada.');
}
$token = lab_env('ADMIN_SETUP_TOKEN');
if (strlen($token) < 24) {
    http_response_code(503);
    exit('Defina ADMIN_SETUP_TOKEN no .env, com ao menos 24 caracteres aleatorios, para concluir a instalacao.');
}
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    verify_csrf();
    $provided = (string)($_POST['token'] ?? '');
    $name = trim((string)($_POST['nome'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['senha'] ?? '');
    $confirmation = (string)($_POST['confirmacao'] ?? '');
    if (!hash_equals($token, $provided)) $error = 'Token inválido.';
    elseif (lab_length($name) < 2 || lab_length($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email)>190) $error='Nome ou e-mail inválido.';
    elseif (strlen($password)<12 || strlen($password)>200 || $password!==$confirmation) $error='Senha de administrador: 12 a 200 caracteres e confirmação idêntica.';
    else {
        try {
            $st=$pdo->prepare("INSERT INTO usuarios (nome,email,senha_hash,tipo) VALUES (?,?,?,'admin')");
            $st->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
            flash('Administrador inicial criado. Entre na conta e retire ADMIN_SETUP_TOKEN do .env.');
            redirect('login.php');
        } catch (PDOException $e) {
            error_log('[LabControl] Setup: '.$e->getMessage());
            $error='Falha ao criar administrador. Confira se já existe esse e-mail.';
        }
    }
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Primeiro administrador · LabControl</title><link rel="stylesheet" href="<?= h(url('assets/style.css')) ?>"></head><body class="login-body"><main class="setup-card"><div class="login-topline">PRIMEIRO ACESSO ADMINISTRATIVO</div><h1>Configurar o administrador</h1><p>Esta página é bloqueada automaticamente após a criação da primeira conta de administrador. O token deve ficar somente no arquivo .env.</p>
<?php if ($error): ?><div class="alert" role="alert"><?= h($error) ?></div><?php endif; ?>
<form method="post" class="login-form"><?= csrf_field() ?><label class="full">Token privado de instalação<input type="password" name="token" required autocomplete="off"></label><label class="full">Nome completo<input name="nome" maxlength="120" required></label><label class="full">E-mail<input type="email" name="email" maxlength="190" required></label><label class="full">Senha (mínimo 12 caracteres)<input type="password" name="senha" minlength="12" maxlength="200" required></label><label class="full">Confirmar senha<input type="password" name="confirmacao" minlength="12" maxlength="200" required></label><button class="button full-button" type="submit">Criar administrador</button></form><p>Após concluir, retire o token do .env e exclua setup.php da pasta pública do site hospedado.</p></main></body></html>
