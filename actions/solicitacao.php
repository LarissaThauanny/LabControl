<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
require_post();
$pdo=lab_database();
$action=(string)($_POST['acao'] ?? '');
$id=(int)($_POST['id'] ?? 0);
if ($action==='solicitar') {
    require_student();
    $equipment=(int)($_POST['equipamento_id'] ?? 0);
    $date=(string)($_POST['data_devolucao_prevista'] ?? '');
    $reason=trim((string)($_POST['justificativa'] ?? ''));
    if ($equipment<=0 || !is_date($date) || $date<date('Y-m-d') || $date>date('Y-m-d',strtotime('+365 days')) || lab_length($reason)>255) {
        http_response_code(422); exit('Selecione um equipamento e uma data futura válida (até 365 dias).');
    }
    try {
        $pdo->beginTransaction();
        $st=$pdo->prepare('SELECT status FROM equipamentos WHERE id=? FOR UPDATE');
        $st->execute([$equipment]);
        if ($st->fetchColumn()!=='disponivel') throw new DomainException('Equipamento indisponível.');
        $st=$pdo->prepare("SELECT COUNT(*) FROM emprestimos WHERE usuario_id=? AND equipamento_id=? AND status='pendente'");
        $st->execute([current_user()['id'],$equipment]);
        if ((int)$st->fetchColumn()>0) throw new DomainException('Você já possui uma solicitação pendente para esse equipamento.');
        $st=$pdo->prepare("INSERT INTO emprestimos (usuario_id,equipamento_id,status,justificativa,data_devolucao_prevista) VALUES (?,?,'pendente',?,?)");
        $st->execute([current_user()['id'],$equipment,$reason!==''?$reason:null,$date]);
        $pdo->commit();
        flash('Solicitação enviada! Aguarde aprovação do administrador.');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        if (!($e instanceof DomainException)) error_log('[LabControl] Solicitação: '.$e->getMessage());
        flash($e instanceof DomainException ? $e->getMessage() : 'Não foi possível enviar esta solicitação.');
    }
    redirect('solicitacoes.php');
}
if ($id<=0 || !in_array($action,['cancelar','aprovar','recusar','devolver'],true)) { http_response_code(422); exit('Ação inválida.'); }
if ($action==='cancelar') require_student();
else require_admin();
try {
    $pdo->beginTransaction();
    $st=$pdo->prepare('SELECT id,usuario_id,equipamento_id,status FROM emprestimos WHERE id=? FOR UPDATE');
    $st->execute([$id]);
    $loan=$st->fetch();
    if (!$loan) throw new DomainException('Solicitação não encontrada.');
    $from=(string)$loan['status'];
    if ($action==='cancelar') {
        if ((int)$loan['usuario_id']!==(int)current_user()['id'] || $from!=='pendente') throw new DomainException('Você só pode cancelar suas próprias solicitações pendentes.');
        $st=$pdo->prepare("UPDATE emprestimos SET status='cancelado' WHERE id=?");
        $st->execute([$id]);
        $success='Solicitação cancelada.';
    } elseif ($action==='recusar') {
        if ($from!=='pendente') throw new DomainException('Somente solicitações pendentes podem ser recusadas.');
        $note=trim((string)($_POST['observacao_admin'] ?? ''));
        if (lab_length($note)>255) throw new DomainException('A observação excede 255 caracteres.');
        $st=$pdo->prepare("UPDATE emprestimos SET status='recusado',observacao_admin=? WHERE id=?");
        $st->execute([$note!==''?$note:null,$id]);
        $success='Solicitação recusada.';
    } elseif ($action==='aprovar') {
        if ($from!=='pendente') throw new DomainException('Esta solicitação não está pendente.');
        $date=(string)($_POST['data_devolucao_prevista'] ?? '');
        if (!is_date($date) || $date<date('Y-m-d') || $date>date('Y-m-d',strtotime('+365 days'))) throw new DomainException('Escolha um prazo de até 365 dias.');
        $st=$pdo->prepare('SELECT status FROM equipamentos WHERE id=? FOR UPDATE');
        $st->execute([$loan['equipamento_id']]);
        if ($st->fetchColumn()!=='disponivel') throw new DomainException('Equipamento indisponível para aprovação.');
        $st=$pdo->prepare("UPDATE equipamentos SET status='emprestado' WHERE id=? AND status='disponivel'");
        $st->execute([$loan['equipamento_id']]);
        if ($st->rowCount()!==1) throw new DomainException('Equipamento já foi reservado.');
        $st=$pdo->prepare("UPDATE emprestimos SET status='aprovado',data_aprovacao=NOW(),data_devolucao_prevista=? WHERE id=?");
        $st->execute([$date,$id]);
        // Solicitações concorrentes não podem ficar pendentes para equipamento ocupado.
        $st=$pdo->prepare("UPDATE emprestimos SET status='recusado',observacao_admin='Indisponível: outra solicitação foi aprovada.' WHERE equipamento_id=? AND status='pendente' AND id<>?");
        $st->execute([$loan['equipamento_id'],$id]);
        $success='Empréstimo aprovado e equipamento reservado.';
    } else {
        if ($from!=='aprovado') throw new DomainException('Apenas empréstimos aprovados podem ser devolvidos.');
        $st=$pdo->prepare("UPDATE emprestimos SET status='devolvido',data_devolucao=NOW() WHERE id=?");
        $st->execute([$id]);
        $st=$pdo->prepare("UPDATE equipamentos SET status='disponivel' WHERE id=? AND status='emprestado'");
        $st->execute([$loan['equipamento_id']]);
        if ($st->rowCount()!==1) throw new DomainException('Situação do equipamento inconsistente.');
        $success='Devolução registrada e equipamento liberado.';
    }
    $pdo->commit();
    flash($success);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if (!($e instanceof DomainException)) error_log('[LabControl] Operação de empréstimo: '.$e->getMessage());
    flash($e instanceof DomainException ? $e->getMessage() : 'Não foi possível concluir a operação.');
}
redirect('solicitacoes.php');
