<?php
require_once __DIR__.'/includes/bootstrap.php';
require_admin();
$users=lab_database()->query("SELECT u.id,u.nome,u.email,u.tipo,u.criado_em,COUNT(em.id) pedidos FROM usuarios u LEFT JOIN emprestimos em ON em.usuario_id=u.id GROUP BY u.id,u.nome,u.email,u.tipo,u.criado_em ORDER BY u.criado_em DESC LIMIT 250")->fetchAll();
$page='usuarios';$page_title='Usuários';require __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><div class="eyebrow">ADMINISTRAÇÃO</div><h1>Usuários cadastrados</h1><p>Apenas administradores podem consultar os usuários do sistema.</p></div></div>
<section class="panel"><div class="panel-title"><div><h2>Contas do LabControl</h2><p>Informações limitadas ao necessário para gerenciar os pedidos.</p></div></div><div class="table-wrap"><table><thead><tr><th>Nome</th><th>E-mail</th><th>Tipo</th><th>Pedidos</th><th>Cadastro</th></tr></thead><tbody>
<?php foreach($users as $user): ?><tr><td><strong><?= h($user['nome']) ?></strong></td><td><?= h($user['email']) ?></td><td><span class="pill <?= $user['tipo']==='admin'?'loaned':'available' ?>"><?= $user['tipo']==='admin'?'Administrador':'Aluno' ?></span></td><td><?= (int)$user['pedidos'] ?></td><td><?= h(date('d/m/Y',strtotime($user['criado_em']))) ?></td></tr><?php endforeach; ?>
<?php if(!$users): ?><tr><td colspan="5" class="empty">Nenhum usuário cadastrado.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__.'/includes/footer.php'; ?>
