<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();
require_post();
$pdo=lab_database();
$action=(string)($_POST['acao'] ?? '');
$id=(int)($_POST['id'] ?? 0);
if (!in_array($action,['criar','editar','excluir'],true)) { http_response_code(400); exit('Ação inválida.'); }
if ($action === 'excluir') {
    if ($id<=0) { http_response_code(422); exit('Equipamento inválido.'); }
    try {
        $st=$pdo->prepare('DELETE FROM equipamentos WHERE id=?');
        $st->execute([$id]);
        flash($st->rowCount() ? 'Equipamento removido.' : 'Equipamento não encontrado.');
    } catch (PDOException $e) {
        flash('Este equipamento tem histórico de empréstimos e não pode ser apagado. Coloque-o em manutenção se necessário.');
    }
    redirect('equipamentos.php');
}
$name=trim((string)($_POST['nome'] ?? ''));
$code=trim((string)($_POST['codigo'] ?? ''));
$category=trim((string)($_POST['categoria'] ?? ''));
$description=trim((string)($_POST['descricao'] ?? ''));
$status=(string)($_POST['status'] ?? 'disponivel');
if (lab_length($name)<2 || lab_length($name)>120 || lab_length($code)>80 || lab_length($category)>80 || lab_length($description)>2000 || !in_array($status,['disponivel','manutencao'],true)) {
    http_response_code(422); exit('Preencha os campos corretamente. Status emprestado depende de uma aprovação.');
}
try {
    if ($action==='criar') {
        $st=$pdo->prepare('INSERT INTO equipamentos(nome,descricao,codigo,categoria,status) VALUES(?,?,?,?,?)');
        $st->execute([$name,$description,$code!==''?$code:null,$category!==''?$category:'Outros',$status]);
        flash('Equipamento cadastrado.');
    } else {
        if ($id<=0) { http_response_code(422); exit('Equipamento inválido.'); }
        $pdo->beginTransaction();
        $st=$pdo->prepare('SELECT status FROM equipamentos WHERE id=? FOR UPDATE');
        $st->execute([$id]);
        $stored=$st->fetchColumn();
        if ($stored===false || $stored==='emprestado') {
            $pdo->rollBack();
            flash('Equipamento emprestado ou inexistente. Devolva antes de editar.');
            redirect('equipamentos.php');
        }
        $st=$pdo->prepare('UPDATE equipamentos SET nome=?,descricao=?,codigo=?,categoria=?,status=? WHERE id=?');
        $st->execute([$name,$description,$code!==''?$code:null,$category!==''?$category:'Outros',$status,$id]);
        $pdo->commit();
        flash('Equipamento atualizado.');
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('[LabControl] Equipamento: '.$e->getMessage());
    flash('Não foi possível salvar. Verifique se o patrimônio já está cadastrado.');
}
redirect('equipamentos.php');
