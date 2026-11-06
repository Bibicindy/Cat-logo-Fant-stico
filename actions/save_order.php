<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../bd.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || empty($data['franquia_id']) || empty($data['ordem'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados de ordenação inválidos.']);
    exit;
}

$franquia_id = (int)$data['franquia_id'];
$ordem_itens = $data['ordem'];
$tipo_ordem = $data['tipo'] ?? 'personalizada'; // 'cronologica' ou 'personalizada'

try {
    $usuario_id = null;
    $usuario_nome = 'Visitante (Temporário)';

    if (usuario_autenticado()) {
        $u = obter_usuario();
        $usuario_id = $u['id'];
        $usuario_nome = $u['nome'];

        if ($tipo_ordem === 'cronologica' && usuario_tem_poder()) {
            // Atualizar diretamente a ordem cronológica global oficial do universo cinematográfico
            $stmtUpdate = $pdo->prepare("UPDATE filmes SET ordem_cronologica = :pos WHERE id = :id AND franquia_id = :frid");
            foreach ($ordem_itens as $item) {
                $stmtUpdate->execute([
                    ':pos' => (int)$item['posicao'],
                    ':id' => (int)$item['filme_id'],
                    ':frid' => $franquia_id
                ]);
            }
            registrar_log($pdo, 'ALTEROU_ORDEM_CRONOLOGICA', "Atualizou a ordem cronológica oficial da franquia #{$franquia_id}.", $usuario_id, $usuario_nome);
        } else {
            // Salvar na ordem personalizada do usuário
            foreach ($ordem_itens as $item) {
                $fid = (int)$item['filme_id'];
                $pos = (int)$item['posicao'];

                if ($driver_ativo === 'sqlite') {
                    $stmt = $pdo->prepare("INSERT OR REPLACE INTO ordem_personalizada (usuario_id, franquia_id, filme_id, posicao) VALUES (:uid, :frid, :fid, :pos)");
                    $stmt->execute([':uid' => $usuario_id, ':frid' => $franquia_id, ':fid' => $fid, ':pos' => $pos]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO ordem_personalizada (usuario_id, franquia_id, filme_id, posicao) VALUES (:uid, :frid, :fid, :pos) ON DUPLICATE KEY UPDATE posicao = :pos");
                    $stmt->execute([':uid' => $usuario_id, ':frid' => $franquia_id, ':fid' => $fid, ':pos' => $pos]);
                }
            }
            registrar_log($pdo, 'ALTEROU_ORDEM_PERSONALIZADA', "Reorganizou sua lista personalizada da franquia #{$franquia_id}.", $usuario_id, $usuario_nome);
        }
    } else {
        // Usuário visitante - salva na sessão
        if (!isset($_SESSION['ordem_temp'])) {
            $_SESSION['ordem_temp'] = [];
        }
        $_SESSION['ordem_temp'][$franquia_id] = $ordem_itens;
        registrar_log($pdo, 'VISITANTE_REORDENOU', "Visitante reorganizou filmes na franquia #{$franquia_id}.");
    }

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Ordem atualizada com sucesso!',
        'autenticado' => usuario_autenticado()
    ]);
} catch (Exception $e) {
    error_log("Erro no save_order: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao salvar ordem: ' . $e->getMessage()]);
}
