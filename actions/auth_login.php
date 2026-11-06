<?php
require_once __DIR__ . '/../bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$login = trim($_POST['login'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($login) || empty($senha)) {
    definir_toast('Preencha todos os campos obrigatórios.', 'erro');
    header('Location: ../login.php');
    exit;
}

try {
    // Busca por usuário ou email ou telefone
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :login OR email = :login OR telefone = :login LIMIT 1");
    $stmt->execute([':login' => $login]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Login bem sucedido
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'usuario' => $usuario['usuario'],
            'email' => $usuario['email'],
            'telefone' => $usuario['telefone'],
            'papel' => $usuario['papel']
        ];

        // Sincronizar progresso temporário da sessão se houver
        if (!empty($_SESSION['progresso_temp']) && is_array($_SESSION['progresso_temp'])) {
            $stmtProg = $pdo->prepare("INSERT INTO progresso_usuario (usuario_id, filme_id, assistido) VALUES (:uid, :fid, 1) ON DUPLICATE KEY UPDATE assistido = 1");
            foreach ($_SESSION['progresso_temp'] as $filme_id => $v) {
                if ($driver_ativo === 'sqlite') {
                    $stmtProgSqlite = $pdo->prepare("INSERT OR REPLACE INTO progresso_usuario (usuario_id, filme_id, assistido) VALUES (:uid, :fid, 1)");
                    $stmtProgSqlite->execute([':uid' => $usuario['id'], ':fid' => $filme_id]);
                } else {
                    $stmtProg->execute([':uid' => $usuario['id'], ':fid' => $filme_id]);
                }
            }
            unset($_SESSION['progresso_temp']);
        }

        registrar_log($pdo, 'LOGIN_SUCESSO', "Usuário {$usuario['usuario']} conectou-se ao sistema.", $usuario['id'], $usuario['nome']);
        definir_toast("Bem-vindo(a) de volta, {$usuario['nome']}!", 'sucesso');
        header('Location: ../index.php');
        exit;
    } else {
        registrar_log($pdo, 'LOGIN_FALHA', "Tentativa de login malsucedida para: {$login}", null, 'Anônimo');
        definir_toast('Credenciais inválidas. Verifique seu usuário e senha.', 'erro');
        header('Location: ../login.php');
        exit;
    }
} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    definir_toast('Erro no servidor ao processar login. Tente novamente.', 'erro');
    header('Location: ../login.php');
    exit;
}
