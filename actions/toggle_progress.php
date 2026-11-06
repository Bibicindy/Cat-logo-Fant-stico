<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../bd.php';

$filme_id = (int)($_POST['filme_id'] ?? 0);
$franquia_id = (int)($_POST['franquia_id'] ?? 0);
$assistido = isset($_POST['assistido']) && $_POST['assistido'] === '1' ? 1 : 0;

if ($filme_id <= 0 || $franquia_id <= 0) {
    echo json_encode(['sucesso' => false, 'erro' => 'Parâmetros inválidos.']);
    exit;
}

try {
    // Obter informações do filme para o log
    $stmtF = $pdo->prepare("SELECT titulo FROM filmes WHERE id = :id");
    $stmtF->execute([':id' => $filme_id]);
    $filme = $stmtF->fetch();
    $titulo_filme = $filme ? $filme['titulo'] : "Filme #{$filme_id}";

    $usuario_id = null;
    $usuario_nome = 'Visitante (Temporário)';

    if (usuario_autenticado()) {
        $u = obter_usuario();
        $usuario_id = $u['id'];
        $usuario_nome = $u['nome'];

        if ($assistido === 1) {
            if ($driver_ativo === 'sqlite') {
                $stmt = $pdo->prepare("INSERT OR REPLACE INTO progresso_usuario (usuario_id, filme_id, assistido, atualizado_em) VALUES (:uid, :fid, 1, :data)");
                $stmt->execute([':uid' => $usuario_id, ':fid' => $filme_id, ':data' => date('Y-m-d H:i:s')]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO progresso_usuario (usuario_id, filme_id, assistido, atualizado_em) VALUES (:uid, :fid, 1, :data) ON DUPLICATE KEY UPDATE assistido = 1, atualizado_em = :data");
                $stmt->execute([':uid' => $usuario_id, ':fid' => $filme_id, ':data' => date('Y-m-d H:i:s')]);
            }
            registrar_log($pdo, 'MARCOU_ASSISTIDO', "Marcou '{$titulo_filme}' como assistido.", $usuario_id, $usuario_nome);
        } else {
            $stmt = $pdo->prepare("DELETE FROM progresso_usuario WHERE usuario_id = :uid AND filme_id = :fid");
            $stmt->execute([':uid' => $usuario_id, ':fid' => $filme_id]);
            registrar_log($pdo, 'DESMARCOU_ASSISTIDO', "Desmarcou '{$titulo_filme}' como assistido.", $usuario_id, $usuario_nome);
        }

        // Calcular estatísticas no banco de dados
        $stmtTotal = $pdo->prepare("SELECT COUNT(*) as total FROM filmes WHERE franquia_id = :frid");
        $stmtTotal->execute([':frid' => $franquia_id]);
        $total = (int)$stmtTotal->fetch()['total'];

        $stmtAssistidos = $pdo->prepare("
            SELECT COUNT(*) as assistidos 
            FROM progresso_usuario p
            INNER JOIN filmes f ON f.id = p.filme_id
            WHERE f.franquia_id = :frid AND p.usuario_id = :uid AND p.assistido = 1
        ");
        $stmtAssistidos->execute([':frid' => $franquia_id, ':uid' => $usuario_id]);
        $assistidos = (int)$stmtAssistidos->fetch()['assistidos'];

    } else {
        // Visitante não logado - guarda em sessão nativa temporária
        if (!isset($_SESSION['progresso_temp'])) {
            $_SESSION['progresso_temp'] = [];
        }

        if ($assistido === 1) {
            $_SESSION['progresso_temp'][$filme_id] = true;
        } else {
            unset($_SESSION['progresso_temp'][$filme_id]);
        }

        // Total da franquia
        $stmtTotal = $pdo->prepare("SELECT id FROM filmes WHERE franquia_id = :frid");
        $stmtTotal->execute([':frid' => $franquia_id]);
        $todosFilmes = $stmtTotal->fetchAll(PDO::FETCH_COLUMN);
        $total = count($todosFilmes);

        $assistidos = 0;
        foreach ($todosFilmes as $fid) {
            if (!empty($_SESSION['progresso_temp'][$fid])) {
                $assistidos++;
            }
        }
        registrar_log($pdo, 'VISITANTE_PROGRESSO', "Visitante alterou status de '{$titulo_filme}'.");
    }

    $porcentagem = $total > 0 ? round(($assistidos / $total) * 100) : 0;

    echo json_encode([
        'sucesso' => true,
        'assistido' => $assistido,
        'assistidos' => $assistidos,
        'total' => $total,
        'porcentagem' => $porcentagem,
        'mensagem' => $assistido ? "Marcado como visto! ({$porcentagem}%)" : "Desmarcado! ({$porcentagem}%)",
        'autenticado' => usuario_autenticado()
    ]);
} catch (Exception $e) {
    error_log("Erro no toggle_progress: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao registrar progresso: ' . $e->getMessage()]);
}
