<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();
$error='';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    verify_csrf();
    $old=(string)($_POST['atual'] ?? '');
    $new=(string)($_POST['nova'] ?? '');
    $confirm=(string)($_POST['confirmacao'] ?? '');
    $st=lab_database()->prepare('SELECT senha_hash FROM usuarios WHERE id=?');
    $st->execute([current_user()['id']]);
    $hash=$st->fetchColumn();
    if (!$hash || !password_verify($old,(string)$hash)) $error='Senha atual incorreta.';
    elseif (strlen($new)<10 || strlen($new)>200 || $new!==$confirm) $error='A nova senha precisa ter 10 a 200 caracteres e confirmação idêntica.';
    else {
        $st=lab_database()->prepare('UPDATE usuarios SET senha_hash=? WHERE id=?');
        $st->execute([password_hash($new,PASSWORD_DEFAULT),current_user()['id']]);
        flash('Senha alterada com sucesso.');
        redirect('index.php');
    }
}
$page='senha';$page_title='Alterar senha';require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><div class="eyebrow">CONTA</div><h1>Alterar minha senha</h1><p>Proteja sua conta com uma senha exclusiva.</p></div></div>
<section class="panel" style="max-width:660px">
<?php if ($error): ?><div class="alert" role="alert"><?= h($error) ?></div><?php endif; ?>
<form class="form-grid" method="post"><?= csrf_field() ?><label class="full">Senha atual<input type="password" name="atual" required autocomplete="current-password"></label><label class="full">Nova senha<input type="password" name="nova" minlength="10" maxlength="200" autocomplete="new-password" required></label><label class="full">Confirmar nova senha<input type="password" name="confirmacao" minlength="10" maxlength="200" autocomplete="new-password" required></label><div class="full actions"><button type="submit" class="button">Atualizar senha</button><a class="button secondary" href="<?= h(url('index.php')) ?>">Voltar</a></div></form></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
