<?php
/**
 * Catálogo Fantástico - Conexão com o Banco de Dados (bd.php)
 * Utiliza PDO para segurança contra SQL Injections.
 * Estilo Quarteto Fantástico (Azul Cósmico & Laranja Flamejante)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';

$pdo = null;
$driver_ativo = 'mysql';

try {
    // Tentativa 1: Conexão com MySQL via PDO
    $dsn_mysql = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
    $pdo_temp = new PDO($dsn_mysql, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 2
    ]);

    // Cria a base se não existir
    $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Conecta diretamente ao banco de dados MySQL
    $dsn_final = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn_final, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $driver_ativo = 'mysql';
} catch (Throwable $e) {
    // Fallback gracioso: Se o servidor MySQL não estiver rodando no momento,
    // utilizamos SQLite via PDO para que o sistema funcione instantaneamente sem erros.
    $sqlite_path = __DIR__ . '/database/catalogo.sqlite';
    $dsn_sqlite = "sqlite:" . $sqlite_path;
    $pdo = new PDO($dsn_sqlite, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdo->exec("PRAGMA foreign_keys = ON;");
    $driver_ativo = 'sqlite';
}

// Inicializar tabelas e seeds se necessário
require_once __DIR__ . '/database/seed.php';
inicializar_banco_se_necessario($pdo, $driver_ativo);

/**
 * Função para gerar o link direto de redirecionamento para o streaming
 */
function obter_link_streaming($streaming, $titulo, $streaming_url = null) {
    if (!empty($streaming_url) && filter_var($streaming_url, FILTER_VALIDATE_URL)) {
        return $streaming_url;
    }

    $stLower = mb_strtolower($streaming ?? '');
    $tituloEncoded = urlencode($titulo ?? '');

    if (str_contains($stLower, 'disney')) {
        return "https://www.disneyplus.com/pt-br/search?q={$tituloEncoded}";
    } elseif (str_contains($stLower, 'netflix')) {
        return "https://www.netflix.com/search?q={$tituloEncoded}";
    } elseif (str_contains($stLower, 'max') || str_contains($stLower, 'hbo')) {
        return "https://www.max.com/search?q={$tituloEncoded}";
    } elseif (str_contains($stLower, 'prime') || str_contains($stLower, 'amazon')) {
        return "https://www.primevideo.com/search/ref=atv_nb_sr?phrase={$tituloEncoded}";
    } elseif (str_contains($stLower, 'apple')) {
        return "https://tv.apple.com/search?term={$tituloEncoded}";
    }

    return "https://www.google.com/search?q=" . urlencode("onde assistir {$titulo} streaming");
}

/**
 * Função para registrar Logs de Auditoria
 */
function registrar_log($pdo, $acao, $detalhes, $usuario_id = null, $usuario_nome = null) {
    try {
        if ($usuario_id === null && isset($_SESSION['usuario']['id'])) {
            $usuario_id = $_SESSION['usuario']['id'];
            $usuario_nome = $_SESSION['usuario']['nome'] ?? $_SESSION['usuario']['usuario'];
        }

        if (!$usuario_nome) {
            $usuario_nome = 'Visitante / Anônimo';
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }

        $stmt = $pdo->prepare("INSERT INTO logs_auditoria (usuario_id, usuario_nome, acao, detalhes, ip, criado_em) VALUES (:usuario_id, :usuario_nome, :acao, :detalhes, :ip, :criado_em)");
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':usuario_nome' => $usuario_nome,
            ':acao' => mb_strtoupper($acao),
            ':detalhes' => $detalhes,
            ':ip' => $ip,
            ':criado_em' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        error_log("Erro ao gravar log: " . $e->getMessage());
    }
}

/**
 * Utilitários de Sessão e Autenticação
 */
function usuario_autenticado() {
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']['id']);
}

function obter_usuario() {
    return $_SESSION['usuario'] ?? null;
}

function usuario_tem_poder() {
    if (!usuario_autenticado()) return false;
    return true;
}

function definir_toast($mensagem, $tipo = 'sucesso') {
    $_SESSION['toast'] = [
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ];
}

function obter_toast() {
    if (isset($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        unset($_SESSION['toast']);
        return $toast;
    }
    return null;
}

function limpar_string($str) {
    return htmlspecialchars(trim($str ?? ''), ENT_QUOTES, 'UTF-8');
}
