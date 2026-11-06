<?php
require_once __DIR__ . '/../bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$franquia_id = (int)($_POST['franquia_id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$ano_lancamento = (int)($_POST['ano_lancamento'] ?? 0);
$ordem_cronologica = (int)($_POST['ordem_cronologica'] ?? 0);
$ordem_lancamento = (int)($_POST['ordem_lancamento'] ?? 0);
$streaming = trim($_POST['streaming'] ?? '');
$duracao = trim($_POST['duracao'] ?? '');
$classificacao_etaria = trim($_POST['classificacao_etaria'] ?? '12');
$sinopse = trim($_POST['sinopse'] ?? '');
$poster_url = trim($_POST['poster_url'] ?? '');
$slug_retorno = trim($_POST['slug_retorno'] ?? '');

// Buscar a franquia para validar e redirecionar
$stmtFr = $pdo->prepare("SELECT * FROM franquias WHERE id = :id");
$stmtFr->execute([':id' => $franquia_id]);
$franquia = $stmtFr->fetch();

if (!$franquia) {
    definir_toast('Franquia não encontrada.', 'erro');
    header('Location: ../index.php');
    exit;
}

// Validações
if (empty($titulo)) {
    definir_toast('O título do filme é obrigatório.', 'erro');
    header("Location: ../franquia.php?slug=" . urlencode($franquia['slug']));
    exit;
}

if ($ano_lancamento < 1888 || $ano_lancamento > 2100) {
    definir_toast('Informe um ano de lançamento válido.', 'erro');
    header("Location: ../franquia.php?slug=" . urlencode($franquia['slug']));
    exit;
}

if (empty($streaming)) {
    definir_toast('Informe onde o filme está disponível (ex: Disney+, Netflix, Max, etc.).', 'erro');
    header("Location: ../franquia.php?slug=" . urlencode($franquia['slug']));
    exit;
}

// Se não preencheu ordens, define automáticas com base no maior valor
if ($ordem_cronologica <= 0) {
    $stmtMaxC = $pdo->prepare("SELECT MAX(ordem_cronologica) as max_c FROM filmes WHERE franquia_id = :id");
    $stmtMaxC->execute([':id' => $franquia_id]);
    $ordem_cronologica = ((int)$stmtMaxC->fetch()['max_c']) + 1;
}

if ($ordem_lancamento <= 0) {
    $stmtMaxL = $pdo->prepare("SELECT MAX(ordem_lancamento) as max_l FROM filmes WHERE franquia_id = :id");
    $stmtMaxL->execute([':id' => $franquia_id]);
    $ordem_lancamento = ((int)$stmtMaxL->fetch()['max_l']) + 1;
}

if (empty($poster_url)) {
    $poster_url = 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=500&auto=format&fit=crop&q=60';
}

try {
    $usuario_id = null;
    $usuario_nome = 'Visitante Anônimo';
    if (usuario_autenticado()) {
        $u = obter_usuario();
        $usuario_id = $u['id'];
        $usuario_nome = $u['nome'];
    }

    $stmtInsert = $pdo->prepare("
        INSERT INTO filmes (franquia_id, titulo, ano_lancamento, ordem_cronologica, ordem_lancamento, streaming, duracao, classificacao_etaria, sinopse, poster_url, criado_por, criado_em)
        VALUES (:franquia_id, :titulo, :ano_lancamento, :ordem_cronologica, :ordem_lancamento, :streaming, :duracao, :classificacao_etaria, :sinopse, :poster_url, :criado_por, :criado_em)
    ");

    $stmtInsert->execute([
        ':franquia_id' => $franquia_id,
        ':titulo' => $titulo,
        ':ano_lancamento' => $ano_lancamento,
        ':ordem_cronologica' => $ordem_cronologica,
        ':ordem_lancamento' => $ordem_lancamento,
        ':streaming' => $streaming,
        ':duracao' => $duracao ?: '2h 00m',
        ':classificacao_etaria' => $classificacao_etaria,
        ':sinopse' => $sinopse ?: 'Sinopse a ser detalhada.',
        ':poster_url' => $poster_url,
        ':criado_por' => $usuario_id,
        ':criado_em' => date('Y-m-d H:i:s')
    ]);

    registrar_log($pdo, 'CADASTROU_FILME', "Acrescentou o filme '{$titulo}' ({$ano_lancamento}) na franquia '{$franquia['nome']}'.", $usuario_id, $usuario_nome);
    definir_toast("Filme '{$titulo}' adicionado com sucesso à franquia!", 'sucesso');
    header("Location: ../franquia.php?slug=" . urlencode($franquia['slug']));
    exit;

} catch (Exception $e) {
    error_log("Erro ao adicionar filme: " . $e->getMessage());
    definir_toast('Erro ao cadastrar o filme: ' . $e->getMessage(), 'erro');
    header("Location: ../franquia.php?slug=" . urlencode($franquia['slug']));
    exit;
}
