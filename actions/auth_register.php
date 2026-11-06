<?php
require_once __DIR__ . '/../bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cadastro.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';
$consentimento = isset($_POST['consentimento']) ? 1 : 0;

// Validações
if (empty($nome) || empty($usuario) || empty($senha)) {
    definir_toast('Nome, nome de usuário e senha são obrigatórios.', 'erro');
    header('Location: ../cadastro.php');
    exit;
}

if (empty($email) && empty($telefone)) {
    definir_toast('Informe ao menos um e-mail ou telefone para contato/recuperação.', 'erro');
    header('Location: ../cadastro.php');
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    definir_toast('E-mail em formato inválido.', 'erro');
    header('Location: ../cadastro.php');
    exit;
}

if (strlen($senha) < 6) {
    definir_toast('A senha deve ter no mínimo 6 caracteres.', 'erro');
    header('Location: ../cadastro.php');
    exit;
}

if ($senha !== $confirmar_senha) {
    definir_toast('As senhas digitadas não coincidem.', 'erro');
    header('Location: ../cadastro.php');
    exit;
}

try {
    // Verificar se já existe nome de usuário
    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario OR (email = :email AND :email != '') LIMIT 1");
    $stmtCheck->execute([
        ':usuario' => $usuario,
        ':email' => $email
    ]);
    if ($stmtCheck->fetch()) {
        definir_toast('Nome de usuário ou e-mail já cadastrado.', 'erro');
        header('Location: ../cadastro.php');
        exit;
    }

    // Hash bcrypt da senha
    $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

    $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nome, usuario, email, telefone, senha, papel, consentimento_dados, criado_em) VALUES (:nome, :usuario, :email, :telefone, :senha, 'usuario', :consentimento, :criado_em)");
    $stmtInsert->execute([
        ':nome' => $nome,
        ':usuario' => $usuario,
        ':email' => !empty($email) ? $email : null,
        ':telefone' => !empty($telefone) ? $telefone : null,
        ':senha' => $senha_hash,
        ':consentimento' => $consentimento,
        ':criado_em' => date('Y-m-d H:i:s')
    ]);

    $novo_id = $pdo->lastInsertId();

    // Iniciar sessão automaticamente
    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id' => $novo_id,
        'nome' => $nome,
        'usuario' => $usuario,
        'email' => $email,
        'telefone' => $telefone,
        'papel' => 'usuario'
    ];

    // Sincronizar progresso temporário se houver
    if (!empty($_SESSION['progresso_temp']) && is_array($_SESSION['progresso_temp'])) {
        foreach ($_SESSION['progresso_temp'] as $filme_id => $v) {
            $stmtProg = $pdo->prepare("INSERT INTO progresso_usuario (usuario_id, filme_id, assistido) VALUES (:uid, :fid, 1)");
            try {
                $stmtProg->execute([':uid' => $novo_id, ':fid' => $filme_id]);
            } catch (Exception $e) {}
        }
        unset($_SESSION['progresso_temp']);
    }

    registrar_log($pdo, 'CADASTRO_USUARIO', "Novo usuário cadastrado com sucesso: {$usuario}", $novo_id, $nome);
    definir_toast('Cadastro realizado com sucesso! Suas listas agora estão salvas no seu perfil.', 'sucesso');
    header('Location: ../index.php');
    exit;

} catch (Exception $e) {
    error_log("Erro no cadastro: " . $e->getMessage());
    definir_toast('Erro no servidor ao realizar cadastro. Tente novamente.', 'erro');
    header('Location: ../cadastro.php');
    exit;
}
