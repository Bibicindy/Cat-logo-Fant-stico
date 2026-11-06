<?php
require_once __DIR__ . '/../bd.php';

if (!usuario_autenticado() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    definir_toast('Você precisa estar autenticado para configurar permissões.', 'aviso');
    header('Location: ../login.php');
    exit;
}

$u = obter_usuario();
$usuario_id = $u['id'];
$franquia_id = (int)($_POST['franquia_id'] ?? 0);
$tipo_acesso = trim($_POST['tipo_acesso'] ?? 'publico');
$pode_editar = isset($_POST['pode_editar']) ? 1 : 0;
$pode_excluir = isset($_POST['pode_excluir']) ? 1 : 0;
$pode_administrar = isset($_POST['pode_administrar']) ? 1 : 0;

if (!in_array($tipo_acesso, ['publico', 'privado', 'compartilhado'])) {
    $tipo_acesso = 'publico';
}

try {
    // Verificar se já existe registro de permissão
    $stmtCheck = $pdo->prepare("SELECT id, codigo_convite FROM permissoes_listas WHERE dono_id = :uid AND franquia_id = :frid");
    $stmtCheck->execute([':uid' => $usuario_id, ':frid' => $franquia_id]);
    $existente = $stmtCheck->fetch();

    $codigo = $existente ? $existente['codigo_convite'] : bin2hex(random_bytes(16));

    if ($existente) {
        $stmtUp = $pdo->prepare("
            UPDATE permissoes_listas 
            SET tipo_acesso = :tipo, pode_editar = :peditar, pode_excluir = :pexcluir, pode_administrar = :padmin, codigo_convite = :codigo
            WHERE id = :id
        ");
        $stmtUp->execute([
            ':tipo' => $tipo_acesso,
            ':peditar' => $pode_editar,
            ':pexcluir' => $pode_excluir,
            ':padmin' => $pode_administrar,
            ':codigo' => $codigo,
            ':id' => $existente['id']
        ]);
    } else {
        $stmtIn = $pdo->prepare("
            INSERT INTO permissoes_listas (dono_id, franquia_id, tipo_acesso, pode_editar, pode_excluir, pode_administrar, codigo_convite, criado_em)
            VALUES (:uid, :frid, :tipo, :peditar, :pexcluir, :padmin, :codigo, :data)
        ");
        $stmtIn->execute([
            ':uid' => $usuario_id,
            ':frid' => $franquia_id,
            ':tipo' => $tipo_acesso,
            ':peditar' => $pode_editar,
            ':pexcluir' => $pode_excluir,
            ':padmin' => $pode_administrar,
            ':codigo' => $codigo,
            ':data' => date('Y-m-d H:i:s')
        ]);
    }

    registrar_log($pdo, 'CONFIGUROU_PERMISSAO', "Alterou permissões da franquia #{$franquia_id} para acesso '{$tipo_acesso}'.", $usuario_id, $u['nome']);
    definir_toast('Permissões de compartilhamento atualizadas com sucesso!', 'sucesso');
    header('Location: ../perfil.php');
    exit;

} catch (Exception $e) {
    error_log("Erro em update_permissions: " . $e->getMessage());
    definir_toast('Erro ao atualizar permissões: ' . $e->getMessage(), 'erro');
    header('Location: ../perfil.php');
    exit;
}
