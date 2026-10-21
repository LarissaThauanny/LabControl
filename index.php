<?php
require_once __DIR__.'/includes/bootstrap.php';
// Visitantes devem chegar diretamente à demonstração pública, sem exigir login.
if (!is_logged_in()) redirect('demo.php');
$pdo=lab_database();
$page='inicio';$page_title='Visão geral';
$total=(int)$pdo->query('SELECT COUNT(*) FROM equipamentos')->fetchColumn();
$available=(int)$pdo->query("SELECT COUNT(*) FROM equipamentos WHERE status='disponivel'")->fetchColumn();
if (is_admin()) {
    $pending=(int)$pdo->query("SELECT COUNT(*) FROM emprestimos WHERE status='pendente'")->fetchColumn();
    $active=(int)$pdo->query("SELECT COUNT(*) FROM emprestimos WHERE status='aprovado'")->fetchColumn();
    $rows=$pdo->query("SELECT em.id,em.status,em.data_solicitacao,em.data_devolucao_prevista,u.nome aluno,e.nome equipamento FROM emprestimos em JOIN usuarios u ON u.id=em.usuario_id JOIN equipamentos e ON e.id=em.equipamento_id ORDER BY em.id DESC LIMIT 7")->fetchAll();
} else {
    $st=$pdo->prepare("SELECT status,COUNT(*) quantidade FROM emprestimos WHERE usuario_id=? GROUP BY status");
    $st->execute([current_user()['id']]);
    $counts=[];foreach($st as $v) $counts[$v['status']]=(int)$v['quantidade'];
    $pending=$counts['pendente']??0;$active=$counts['aprovado']??0;
    $st=$pdo->prepare("SELECT em.id,em.status,em.data_solicitacao,em.data_devolucao_prevista,e.nome equipamento FROM emprestimos em JOIN equipamentos e ON e.id=em.equipamento_id WHERE em.usuario_id=? ORDER BY em.id DESC LIMIT 7");
    $st->execute([current_user()['id']]);$rows=$st->fetchAll();
}
require __DIR__.'/includes/header.php';
?>
<section class="page-heading"><div><div class="eyebrow"><span class="eyebrow-line"></span> LABORATÓRIO DE INFORMÁTICA</div><h1>Olá, <?= h(current_user()['nome']) ?>!</h1><p><?= is_admin()?'Acompanhe inventário, solicitações e devoluções.':'Encontre equipamentos e acompanhe suas solicitações de empréstimo.' ?></p></div><div class="heading-actions"><span class="date-chip"><?= h(date('d/m/Y')) ?></span><a class="button" href="<?= h(url(is_admin()?'solicitacoes.php':'equipamentos.php')) ?>"><?= is_admin()?'Analisar solicitações':'Ver equipamentos' ?> →</a></div></section>
<section class="stats">
  <article class="stat"><span class="stat-icon">▤</span><span>Total de equipamentos</span><strong><?= $total ?></strong><small>Itens cadastrados</small></article>
  <article class="stat"><span class="stat-icon mint">✓</span><span>Disponíveis</span><strong><?= $available ?></strong><small>Prontos para solicitação</small></article>
  <article class="stat"><span class="stat-icon sky">⇄</span><span><?= is_admin()?'Solicitações pendentes':'Minhas pendentes' ?></span><strong><?= $pending ?></strong><small>Aguardando resposta</small></article>
  <article class="stat"><span class="stat-icon rose">◷</span><span><?= is_admin()?'Empréstimos ativos':'Meus empréstimos ativos' ?></span><strong><?= $active ?></strong><small>Aguardando devolução</small></article>
</section>
<div class="dashboard-grid">
<section class="panel"><div class="panel-title split"><div><h2><?= is_admin()?'Movimentações recentes':'Minhas solicitações recentes' ?></h2><p>Atualizado conforme os dados do banco.</p></div><a class="text-link" href="<?= h(url('solicitacoes.php')) ?>">Ver todas ↗</a></div><div class="table-wrap"><table><thead><tr><th>Equipamento</th><?php if(is_admin()): ?><th>Aluno</th><?php endif; ?><th>Data</th><th>Estado</th></tr></thead><tbody>
<?php foreach($rows as $loan): ?><tr><td><strong><?= h($loan['equipamento']) ?></strong></td><?php if(is_admin()): ?><td><?= h($loan['aluno']) ?></td><?php endif; ?><td><?= h(date('d/m/Y',strtotime($loan['data_solicitacao']))) ?></td><td><span class="pill <?= h(status_css($loan['status'])) ?>"><?= h(status_label($loan['status'])) ?></span></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td class="empty" colspan="<?= is_admin()?4:3 ?>">Nenhuma solicitação ainda. <?= is_admin()?'Os pedidos aparecerão aqui.':'Escolha um equipamento para começar.' ?></td></tr><?php endif; ?>
</tbody></table></div></section>
<aside class="panel"><div class="panel-title"><div><h2>Ações rápidas</h2><p>O que você deseja fazer?</p></div></div><div class="quick-links"><a href="<?= h(url('equipamentos.php')) ?>"><span class="quick-icon mint">▤</span><span><strong><?= is_admin()?'Gerenciar inventário':'Consultar catálogo' ?></strong><small><?= is_admin()?'Cadastrar e editar equipamentos':'Encontrar itens disponíveis' ?></small></span><span class="arrow">↗</span></a><a href="<?= h(url('solicitacoes.php')) ?>"><span class="quick-icon sky">⇄</span><span><strong><?= is_admin()?'Moderar pedidos':'Pedir empréstimo' ?></strong><small><?= is_admin()?'Aprovar, recusar e devolver':'Acompanhar meus pedidos' ?></small></span><span class="arrow">↗</span></a><?php if(is_admin()): ?><a href="<?= h(url('relatorios.php')) ?>"><span class="quick-icon purple">▥</span><span><strong>Relatórios</strong><small>Consultar histórico e exportar CSV</small></span><span class="arrow">↗</span></a><?php endif; ?><a href="<?= h(url('demo.php')) ?>"><span class="quick-icon mint">✦</span><span><strong>Demonstração pública</strong><small>Dados fictícios para avaliação</small></span><span class="arrow">↗</span></a></div></aside>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>
