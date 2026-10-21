<?php
require_once __DIR__.'/includes/bootstrap.php';
require_admin();
$q=trim((string)($_GET['q']??''));if(lab_length($q)>120)$q=substr($q,0,120);
$status=(string)($_GET['status']??'todos');
if(!in_array($status,['todos','pendente','aprovado','recusado','cancelado','devolvido','atrasado'],true))$status='todos';
$first=(string)($_GET['inicio']??'');$last=(string)($_GET['fim']??'');
if($first!==''&&!is_date($first))$first='';if($last!==''&&!is_date($last))$last='';
$where=[];$args=[];
if($q!==''){$where[]='(e.nome LIKE ? OR e.codigo LIKE ? OR u.nome LIKE ?)';$like='%'.$q.'%';array_push($args,$like,$like,$like);}
if($first!==''){$where[]='em.data_solicitacao>=?';$args[]=$first.' 00:00:00';}
if($last!==''){$where[]='em.data_solicitacao<=?';$args[]=$last.' 23:59:59';}
if($status==='atrasado')$where[]="em.status='aprovado' AND em.data_devolucao_prevista<CURDATE()";
elseif($status!=='todos'){$where[]='em.status=?';$args[]=$status;}
$whereSQL=$where?'WHERE '.implode(' AND ',$where):'';
$sql="SELECT em.id,em.status,em.justificativa,em.observacao_admin,em.data_solicitacao,em.data_aprovacao,em.data_devolucao_prevista,em.data_devolucao,e.nome equipamento,e.codigo patrimonio,u.nome aluno FROM emprestimos em JOIN usuarios u ON u.id=em.usuario_id JOIN equipamentos e ON e.id=em.equipamento_id {$whereSQL} ORDER BY em.id DESC LIMIT 1500";
$st=lab_database()->prepare($sql);$st->execute($args);$rows=$st->fetchAll();
if(($_GET['exportar']??'')==='csv'){
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="labcontrol-relatorio.csv"');
    echo "\xEF\xBB\xBF";
    $out=fopen('php://output','w');
    fputcsv($out,['ID','Aluno','Equipamento','Patrimônio','Status','Solicitado','Aprovado','Prazo','Devolvido','Motivo','Observação admin'],';');
    foreach($rows as $row){$cells=[$row['id'],$row['aluno'],$row['equipamento'],$row['patrimonio'],$row['status'],$row['data_solicitacao'],$row['data_aprovacao'],$row['data_devolucao_prevista'],$row['data_devolucao'],$row['justificativa'],$row['observacao_admin']];foreach($cells as &$cell){$cell=(string)($cell??'');if(preg_match('/^[\s]*[=+@\-]/u',$cell))$cell="'".$cell;}unset($cell);fputcsv($out,$cells,';');}
    fclose($out);exit;
}
$page='relatorios';$page_title='Relatórios';require __DIR__.'/includes/header.php';
$export=$_GET;$export['exportar']='csv';
?>
<div class="page-heading"><div><div class="eyebrow">ADMINISTRAÇÃO</div><h1>Relatórios</h1><p>Histórico completo de solicitações, empréstimos e devoluções.</p></div></div>
<section class="panel"><div class="panel-title"><div><h2>Filtros de relatório</h2><p>O relatório contém dados internos e só pode ser acessado por administradores.</p></div></div><form method="get" class="form-grid"><label>Equipamento, patrimônio ou aluno<input name="q" maxlength="120" value="<?= h($q) ?>"></label><label>Situação<select name="status"><option value="todos">Todos os estados</option><?php foreach(['pendente','aprovado','recusado','cancelado','devolvido','atrasado'] as $value): ?><option value="<?= h($value) ?>" <?= $status===$value?'selected':'' ?>><?= h($value==='atrasado'?'Atrasados':status_label($value)) ?></option><?php endforeach; ?></select></label><label>A partir de<input type="date" name="inicio" value="<?= h($first) ?>"></label><label>Até<input type="date" name="fim" value="<?= h($last) ?>"></label><div class="full actions"><button class="button" type="submit">Aplicar filtros</button><a class="button secondary" href="<?= h(url('relatorios.php')) ?>">Limpar</a><a class="button secondary" href="<?= h(url('relatorios.php?'.http_build_query($export))) ?>">Exportar CSV</a></div></form></section>
<section class="panel"><div class="panel-title"><div><h2>Resultados (<?= count($rows) ?>)</h2><p>Máximo de 1.500 registros por consulta.</p></div></div><div class="table-wrap"><table><thead><tr><th>Aluno</th><th>Equipamento</th><th>Solicitado</th><th>Prazo</th><th>Devolução</th><th>Status</th></tr></thead><tbody><?php foreach($rows as $row): ?><tr><td><strong><?= h($row['aluno']) ?></strong></td><td><?= h($row['equipamento']) ?></td><td><?= h(date('d/m/Y',strtotime($row['data_solicitacao']))) ?></td><td><?= h(date('d/m/Y',strtotime($row['data_devolucao_prevista']))) ?></td><td><?= h($row['data_devolucao']?date('d/m/Y',strtotime($row['data_devolucao'])):'—') ?></td><td><span class="pill <?= h(status_css($row['status'])) ?>"><?= h(status_label($row['status'])) ?></span></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="6" class="empty">Nenhum resultado.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__.'/includes/footer.php'; ?>
