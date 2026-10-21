<?php
declare(strict_types=1);
/** Le .env ou variaveis do servidor sem divulgar credenciais. */
function lab_env(string $name, string $fallback = ''): string
{
    static $vars = null;
    if ($vars === null) {
        $vars = [];
        $path = dirname(__DIR__) . '/.env';
        if (is_file($path) && is_readable($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $row) {
                $row = trim($row);
                if ($row === '' || str_starts_with($row, '#') || !str_contains($row, '=')) continue;
                [$key, $val] = explode('=', $row, 2);
                $key = trim($key);
                if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $key)) continue;
                $val = trim($val);
                if (strlen($val) >= 2 && (($val[0] === '"' && str_ends_with($val, '"')) || ($val[0] === "'" && str_ends_with($val, "'")))) {
                    $val = substr($val, 1, -1);
                }
                $vars[$key] = $val;
            }
        }
    }
    $external = getenv($name);
    return $external !== false ? (string)$external : ($vars[$name] ?? $fallback);
}
function lab_database(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) return $connection;
    $host = lab_env('DB_HOST', '127.0.0.1');
    $name = lab_env('DB_NAME', 'labcontrol');
    $port = lab_env('DB_PORT', '3306');
    if (!ctype_digit($port) || (int)$port < 1 || (int)$port > 65535 || $host === '' || $name === '') {
        http_response_code(503);
        exit('Banco nao configurado. Siga a secao Instalacao e execucao da spec.md.');
    }
    try {
        $connection = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", lab_env('DB_USER', 'root'), lab_env('DB_PASS'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $error) {
        error_log('[LabControl] Erro de conexão: ' . $error->getMessage());
        http_response_code(503);
        exit('Banco indisponivel. Consulte a configuracao .env e o servidor MySQL.');
    }
    return $connection;
}
