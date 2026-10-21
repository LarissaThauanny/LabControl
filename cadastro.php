<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (is_logged_in()) redirect('index.php');
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['nome'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['senha'] ?? '');
    $confirmation = (string)($_POST['confirmacao'] ?? '');
    if (lab_length($name) < 2 || lab_length($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        $error = 'Informe um nome (2 a 120 letras) e um e-mail válidos.';
    } elseif (strlen($password) < 10 || strlen($password) > 200 || $password !== $confirmation) {
        $error = 'A senha precisa ter 10 a 200 caracteres e ser confirmada corretamente.';
    } else {
        try {
            $st = lab_database()->prepare("INSERT INTO usuarios (nome,email,senha_hash,tipo) VALUES (?,?,?,'aluno')");
            $st->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Conta de aluno criada com sucesso. Faça login.');
            redirect('login.php');
        } catch (PDOException $errorDatabase) {
            error_log('[LabControl] Cadastro: ' . $errorDatabase->getMessage());
            $error = 'Não foi possível cadastrar este e-mail. Confira se já existe uma conta.';
        }
    }
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Criar conta · LabControl</title><link rel="stylesheet" href="<?= h(url('assets/style.css')) ?>"></head><body class="login-body"><main class="setup-card"><div class="login-topline">CADASTRO DE ALUNO</div><h1>Criar minha conta</h1><p>As novas contas têm acesso de aluno. Somente o administrador pode liberar solicitações e controlar equipamentos.</p>
<?php if ($error): ?><div class="alert" role="alert"><?= h($error) ?></div><?php endif; ?>
<form method="post" class="login-form"><?= csrf_field() ?><label class="full">Nome completo<input name="nome" maxlength="120" required autocomplete="name" value="<?= h($_POST['nome'] ?? '') ?>"></label><label class="full">E-mail<input type="email" name="email" maxlength="190" required autocomplete="email" value="<?= h($_POST['email'] ?? '') ?>"></label><label class="full">Senha (mínimo 10 caracteres)<input type="password" name="senha" minlength="10" maxlength="200" autocomplete="new-password" required></label><label class="full">Confirmar senha<input type="password" name="confirmacao" minlength="10" maxlength="200" autocomplete="new-password" required></label><button class="button full-button" type="submit">Criar conta</button></form><a class="back-link" href="<?= h(url('login.php')) ?>">← Voltar ao login</a></main></body></html>
