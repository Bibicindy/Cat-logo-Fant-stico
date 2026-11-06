<?php
require_once __DIR__ . '/../bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$filme_id = (int)($_POST['filme_id'] ?? 0);
$slug_retorno = trim($_POST['slug_retorno'] ?? '');

try {
    $stmtF = $pdo->prepare("SELECT f.*, fr.nome as franquia_nome, fr.slug as franquia_slug FROM filmes f INNER JOIN franquias fr ON fr.id = f.franquia_id WHERE f.id = :id");
    $stmtF->execute([':id' => $filme_id]);
    $filme = $stmtF->fetch();

    if ($filme) {
        $usuario_id = null;
        $usuario_nome = 'Visitante Anônimo';
        if (usuario_autenticado()) {
            $u = obter_usuario();
            $usuario_id = $u['id'];
            $usuario_nome = $u['nome'];
        }

        $stmtDel = $pdo->prepare("DELETE FROM filmes WHERE id = :id");
        $stmtDel->execute([':id' => $filme_id]);

        registrar_log($pdo, 'EXCLUIU_FILME', "Removeu o filme '{$filme['titulo']}' da franquia '{$filme['franquia_nome']}'.", $usuario_id, $usuario_nome);
        definir_toast("Filme '{$filme['titulo']}' removido com sucesso.", 'sucesso');
        header("Location: ../franquia.php?slug=" . urlencode($filme['franquia_slug']));
        exit;
    }
} catch (Exception $e) {
    error_log("Erro ao excluir filme: " . $e->getMessage());
    definir_toast('Erro ao remover o filme: ' . $e->getMessage(), 'erro');
}

header("Location: ../franquia.php?slug=" . urlencode($slug_retorno));
exit;
