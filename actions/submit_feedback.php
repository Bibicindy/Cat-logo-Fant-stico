<?php
require_once __DIR__ . '/../bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../feedbacks.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$nota = (int)($_POST['nota'] ?? 5);
$tipo = trim($_POST['tipo'] ?? 'sugestao');
$mensagem = trim($_POST['mensagem'] ?? '');

if (empty($nome) || empty($mensagem)) {
    definir_toast('Por favor, informe seu nome e a sua mensagem ou avaliação.', 'erro');
    header('Location: ../feedbacks.php');
    exit;
}

if ($nota < 1 || $nota > 5) {
    $nota = 5;
}

$tipos_validos = ['sugestao', 'elogio', 'critica', 'problema'];
if (!in_array($tipo, $tipos_validos)) {
    $tipo = 'sugestao';
}

try {
    $usuario_id = null;
    if (usuario_autenticado()) {
        $u = obter_usuario();
        $usuario_id = $u['id'];
        if (empty($nome)) $nome = $u['nome'];
        if (empty($email)) $email = $u['email'];
    }

    $stmt = $pdo->prepare("
        INSERT INTO avaliacoes_feedbacks (usuario_id, nome, email, nota, tipo, mensagem, criado_em)
        VALUES (:usuario_id, :nome, :email, :nota, :tipo, :mensagem, :criado_em)
    ");

    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':nome' => $nome,
        ':email' => $email ?: null,
        ':nota' => $nota,
        ':tipo' => $tipo,
        ':mensagem' => $mensagem,
        ':criado_em' => date('Y-m-d H:i:s')
    ]);

    registrar_log($pdo, 'ENVIOU_FEEDBACK', "Enviou avaliação tipo '{$tipo}' com nota {$nota} estrelas.", $usuario_id, $nome);
    definir_toast('Muito obrigado! Sua avaliação e sugestão foram registradas com sucesso.', 'sucesso');
    header('Location: ../feedbacks.php');
    exit;

} catch (Exception $e) {
    error_log("Erro ao enviar feedback: " . $e->getMessage());
    definir_toast('Erro ao registrar feedback: ' . $e->getMessage(), 'erro');
    header('Location: ../feedbacks.php');
    exit;
}
