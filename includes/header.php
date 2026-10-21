<?php
require_once __DIR__ . '/bootstrap.php';
$page = $page ?? 'inicio';
$page_title = $page_title ?? 'Painel';
$person = current_user();
$initial = $person ? strtoupper(substr((string)$person['nome'], 0, 1)) : 'L';
?>
<!doctype html>
<html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#122a3c">
<meta name="description" content="LabControl — Gestão de equipamentos e empréstimos do laboratório">
<title><?= h($page_title) ?> · LabControl</title>
<link rel="stylesheet" href="<?= h(url('assets/style.css')) ?>">
<script defer src="<?= h(url('assets/app.js')) ?>"></script>
</head><body class="app-body">
<div class="app-shell">
<aside class="sidebar" aria-label="Navegação principal">
  <a class="brand" href="<?= h(url('index.php')) ?>"><span class="brand-mark">L<span>+</span></span><span class="brand-type">LabControl <small>GESTÃO DO LABORATÓRIO</small></span></a>
  <div class="nav-caption">ÁREA <?= is_admin() ? 'ADMINISTRATIVA' : 'DO ALUNO' ?></div>
  <nav class="side-nav" aria-label="Menu">
    <a class="<?= $page==='inicio'?'active':'' ?>" href="<?= h(url('index.php')) ?>"><span aria-hidden="true">◫</span> Visão geral</a>
    <a class="<?= $page==='equipamentos'?'active':'' ?>" href="<?= h(url('equipamentos.php')) ?>"><span aria-hidden="true">▤</span> Equipamentos</a>
    <a class="<?= $page==='solicitacoes'?'active':'' ?>" href="<?= h(url('solicitacoes.php')) ?>"><span aria-hidden="true">⇄</span> <?= is_admin()?'Solicitações':'Meus empréstimos' ?></a>
    <?php if (is_admin()): ?>
    <a class="<?= $page==='relatorios'?'active':'' ?>" href="<?= h(url('relatorios.php')) ?>"><span aria-hidden="true">▥</span> Relatórios</a>
    <a class="<?= $page==='usuarios'?'active':'' ?>" href="<?= h(url('usuarios.php')) ?>"><span aria-hidden="true">♙</span> Usuários</a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-help"><span class="help-symbol">✦</span><strong>Organização em dia</strong><p>Equipamentos, solicitações e devoluções em um só lugar.</p><a href="<?= h(url('demo.php')) ?>">Ver demonstração pública ↗</a></div>
  <div class="sidebar-bottom"><div class="sidebar-user"><span class="avatar"><?= h($initial) ?></span><span><strong><?= h($person['nome'] ?? '') ?></strong><small><?= is_admin()?'Administrador':'Aluno' ?></small></span></div>
  <div class="account-actions"><a href="<?= h(url('senha.php')) ?>">Alterar senha</a><form action="<?= h(url('logout.php')) ?>" method="post"><?= csrf_field() ?><button type="submit">Sair</button></form></div></div>
</aside>
<div class="workspace"><header class="topbar"><div class="topbar-left"><span class="topbar-kicker">LABCONTROL</span><span class="topbar-divider">/</span><span><?= h($page_title) ?></span></div><div class="topbar-right"><span class="status-dot"></span><span class="online-text">Acesso protegido</span><span class="topbar-avatar"><?= h($initial) ?></span></div></header>
<main class="content" id="conteudo">
<?php if ($notification=take_flash()): ?><div class="notice" role="status"><?= h($notification) ?></div><?php endif; ?>
