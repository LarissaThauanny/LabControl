<?php
/** Página de demonstração sem credenciais e sem conexão com banco de dados.
 * Todos os registros abaixo são fictícios. Nenhuma ação altera dados reais.
 */
$equipamentos=[
 ['nome'=>'Notebook Dell Latitude','codigo'=>'DEMO-001','categoria'=>'Computadores','status'=>'disponivel'],
 ['nome'=>'Projetor Epson PowerLite','codigo'=>'DEMO-002','categoria'=>'Audiovisual','status'=>'emprestado'],
 ['nome'=>'Mouse Logitech M90','codigo'=>'DEMO-003','categoria'=>'Periféricos','status'=>'disponivel'],
 ['nome'=>'Monitor LG 24"','codigo'=>'DEMO-004','categoria'=>'Monitores','status'=>'manutencao'],
 ['nome'=>'Teclado USB ABNT2','codigo'=>'DEMO-005','categoria'=>'Periféricos','status'=>'disponivel'],
 ['nome'=>'Cabo HDMI 2m','codigo'=>'DEMO-006','categoria'=>'Acessórios','status'=>'emprestado'],
 ['nome'=>'Webcam Logitech HD','codigo'=>'DEMO-007','categoria'=>'Periféricos','status'=>'disponivel'],
 ['nome'=>'Kit Arduino Uno','codigo'=>'DEMO-008','categoria'=>'Eletrônica','status'=>'disponivel'],
 ];
$labels=['disponivel'=>'Disponível','emprestado'=>'Emprestado','manutencao'=>'Manutenção'];
$counts=array_count_values(array_column($equipamentos,'status'));
function e(string $value): string {return htmlspecialchars($value,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#0c1427"><title>Demonstração pública · LabControl</title><meta name="description" content="Conheça o LabControl em uma demonstração interativa com dados fictícios"><link rel="stylesheet" href="assets/style.css"></head>
<body class="demo-body">
<header class="demo-top"><a class="brand" href="demo.php"><span class="brand-mark">L<span>+</span></span><span class="brand-type">LabControl <small>LABORATORY MANAGEMENT</small></span></a><nav class="demo-actions" aria-label="Acesso"><a class="text-link" href="#demonstracao">Explorar demonstração</a><a class="button" href="login.php">Entrar no sistema ↗</a></nav></header>
<main class="demo-container"><section class="demo-hero"><div><div class="demo-kicker">✦ CONTROLE DE LABORATÓRIO</div><h1>Seu laboratório, muito mais organizado.</h1><p>Controle o inventário, acompanhe empréstimos e identifique equipamentos disponíveis em uma interface simples e moderna.</p></div><span class="demo-badge">● DEMONSTRAÇÃO PÚBLICA</span></section>
<p class="demo-note" role="note"><strong>Ambiente demonstrativo:</strong> todos os itens e números abaixo são fictícios. Pesquise e filtre livremente; nada é salvo no banco real. Operações de cadastro, empréstimo e devolução exigem login de uma conta autorizada.</p>
<section class="stats demo-stat-grid" aria-label="Indicadores fictícios"><article class="stat"><span class="stat-icon">▤</span><span>Equipamentos</span><strong><?= count($equipamentos) ?></strong><small>Dados fictícios</small></article><article class="stat"><span class="stat-icon mint">✓</span><span>Disponíveis</span><strong><?= $counts['disponivel']??0 ?></strong><small>Prontos para uso</small></article><article class="stat"><span class="stat-icon sky">⇄</span><span>Emprestados</span><strong><?= $counts['emprestado']??0 ?></strong><small>Exemplos de empréstimo</small></article><article class="stat"><span class="stat-icon rose">!</span><span>Em manutenção</span><strong><?= $counts['manutencao']??0 ?></strong><small>Requerem atenção</small></article></section>
<section id="demonstracao"><div class="demo-intro"><h2>Explore o inventário</h2><p>Digite um termo ou selecione uma situação. Os resultados são filtrados instantaneamente.</p></div>
<div class="demo-search"><label for="demo-query" class="sr-only">Buscar equipamento demonstrativo</label><input id="demo-query" type="search" placeholder="⌕  Pesquise por nome, patrimônio ou categoria" autocomplete="off"><label for="demo-status" class="sr-only">Filtrar situação</label><select id="demo-status"><option value="todos">Todas as situações</option><option value="disponivel">Disponível</option><option value="emprestado">Emprestado</option><option value="manutencao">Manutenção</option></select><button class="button secondary" id="demo-reset" type="button">Limpar filtros</button></div>
<div class="panel demo-table"><div class="panel-title split"><div><h2>Equipamentos de exemplo</h2><p>Dados usados exclusivamente para demonstrar a interface.</p></div><span class="section-tag" id="demo-count"><?= count($equipamentos) ?> RESULTADOS</span></div><div class="table-wrap"><table><thead><tr><th>Equipamento</th><th>Patrimônio</th><th>Categoria</th><th>Situação</th></tr></thead><tbody id="demo-list">
<?php foreach($equipamentos as $item): ?><tr class="demo-row" data-search="<?= e(strtolower(implode(' ',array_values($item)))) ?>" data-status="<?= e($item['status']) ?>"><td><strong><?= e($item['nome']) ?></strong></td><td><?= e($item['codigo']) ?></td><td><?= e($item['categoria']) ?></td><td><span class="pill <?= e($item['status']==='manutencao'?'maintenance':($item['status']==='emprestado'?'loaned':'available')) ?>">● <?= e($labels[$item['status']]) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div><p id="demo-empty" class="demo-empty" hidden>Nenhum item de demonstração corresponde aos filtros.</p></div></section>
<section class="panel"><div class="panel-title"><div><h2>Conheça o sistema completo</h2><p>No ambiente autenticado, o responsável pode cadastrar equipamentos, registrar devoluções e baixar relatórios em CSV.</p></div><a class="button" href="login.php">Acessar com minha conta →</a></div><p class="form-hint">Primeira instalação local? Consulte o README e a SPEC para criar uma conta administrativa com um token privado.</p></section></main><footer class="demo-footer">LabControl · Projeto acadêmico · Amostras fictícias, sem dados de alunos reais</footer>
<script>
(() => {
 const q=document.getElementById('demo-query'),s=document.getElementById('demo-status'),list=[...document.querySelectorAll('.demo-row')],count=document.getElementById('demo-count'),empty=document.getElementById('demo-empty');
 const normalize=v=>v.toLocaleLowerCase('pt-BR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
 function filter(){const term=normalize(q.value.trim()),state=s.value;let shown=0;list.forEach(row=>{const match=normalize(row.dataset.search).includes(term)&&(state==='todos'||row.dataset.status===state);row.hidden=!match;if(match)shown++;});count.textContent=shown+' RESULTADOS';empty.hidden=shown!==0;}
 q.addEventListener('input',filter);s.addEventListener('change',filter);document.getElementById('demo-reset').addEventListener('click',()=>{q.value='';s.value='todos';filter();q.focus();});
})();
</script>
</body></html>
