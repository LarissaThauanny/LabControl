<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
date_default_timezone_set('America/Porto_Velho');
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_trans_sid', '0');
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
function h(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function lab_length(string $value): int { return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value); }
function base_path(): string
{
    static $root = null;
    if ($root !== null) return $root;
    $directory = realpath(dirname(__DIR__));
    $document = realpath((string)($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $inferred = '/LabControl/';
    if ($directory && $document) {
        if ($directory === $document) $inferred = '/';
        elseif (str_starts_with($directory, rtrim($document, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)) {
            $inferred = '/' . trim(str_replace(DIRECTORY_SEPARATOR, '/', substr($directory, strlen(rtrim($document, DIRECTORY_SEPARATOR)))), '/') . '/';
        }
    }
    $configured = lab_env('APP_BASE_PATH', $inferred);
    $root = preg_match('#^/[A-Za-z0-9_/-]*$#', $configured) ? $configured : $inferred;
    // No dominio principal a pasta do projeto coincide com a raiz publica.
    if ($directory && $document && $directory === $document) $root = '/';
    return rtrim($root, '/') . '/';
}
function url(string $file = ''): string { return base_path() . ltrim($file, '/'); }
function redirect(string $path): never { header('Location: ' . url($path), true, 303); exit; }
function current_user(): ?array { return isset($_SESSION['user']['id']) ? $_SESSION['user'] : null; }
function is_logged_in(): bool { return current_user() !== null; }
function is_admin(): bool { return is_logged_in() && ($_SESSION['user']['tipo'] ?? '') === 'admin'; }
function require_login(): void { if (!is_logged_in()) redirect('login.php'); }
function require_admin(): void { require_login(); if (!is_admin()) { http_response_code(403); exit('Acesso negado: esta area e exclusiva de administradores.'); } }
function require_student(): void { require_login(); if (is_admin()) { http_response_code(403); exit('Esta acao e exclusiva de alunos.'); } }
function csrf_token(): string { if (!isset($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32)); return (string)$_SESSION['_csrf']; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . h(csrf_token()) . '">'; }
function verify_csrf(): void {
    if (!isset($_POST['_csrf']) || !is_string($_POST['_csrf']) || !hash_equals(csrf_token(), $_POST['_csrf'])) {
        http_response_code(403); exit('Requisicao nao autorizada. Recarregue a pagina e tente novamente.');
    }
}
function require_post(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); exit('Metodo nao permitido.'); }
    verify_csrf();
}
function flash(string $message): void { $_SESSION['_flash'] = $message; }
function take_flash(): ?string { $v=$_SESSION['_flash'] ?? null; unset($_SESSION['_flash']); return $v; }
function is_date(string $text): bool { $d = DateTimeImmutable::createFromFormat('!Y-m-d', $text); return $d !== false && $d->format('Y-m-d') === $text; }
function status_label(string $value): string { return ['pendente'=>'Pendente','aprovado'=>'Aprovado','recusado'=>'Recusado','cancelado'=>'Cancelado','devolvido'=>'Devolvido','disponivel'=>'Disponível','emprestado'=>'Emprestado','manutencao'=>'Manutenção'][$value] ?? $value; }
function status_css(string $status): string { return ['pendente'=>'maintenance','aprovado'=>'loaned','recusado'=>'late','cancelado'=>'maintenance','devolvido'=>'available','disponivel'=>'available','emprestado'=>'loaned','manutencao'=>'maintenance'][$status] ?? ''; }
