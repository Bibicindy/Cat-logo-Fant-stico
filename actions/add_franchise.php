<?php
require_once __DIR__ . '/../bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../nova-franquia.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$banner_url = trim($_POST['banner_url'] ?? '');
$icone_url = trim($_POST['icone_url'] ?? '');
$visibilidade = trim($_POST['visibilidade'] ?? 'publica');

if (empty($nome)) {
    definir_toast('O nome da franquia é obrigatório.', 'erro');
    header('Location: ../nova-franquia.php');
    exit;
}

// Gerar Slug amigável
function gerar_slug($texto) {
    $texto = preg_replace('~[^\pL\d]+~u', '-', $texto);
    $texto = iconv('utf-8', 'us-ascii//TRANSLIT', $texto);
    $texto = preg_replace('~[^-\w]+~', '', $texto);
    $texto = trim($texto, '-');
    $texto = preg_replace('~-+~', '-', $texto);
    $texto = strtolower($texto);
    return empty($texto) ? 'franquia-' . time() : $texto;
}

$slug_base = gerar_slug($nome);
$slug = $slug_base;
$contador = 1;

// Garantir unicidade do slug
while (true) {
    $stmtC = $pdo->prepare("SELECT id FROM franquias WHERE slug = :slug LIMIT 1");
    $stmtC->execute([':slug' => $slug]);
    if (!$stmtC->fetch()) {
        break;
    }
    $slug = $slug_base . '-' . $contador;
    $contador++;
}

if (empty($banner_url)) {
    $banner_url = 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=1200&auto=format&fit=crop&q=80';
}
if (empty($icone_url)) {
    $icone_url = 'https://cdn-icons-png.flaticon.com/512/3171/3171927.png';
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
        INSERT INTO franquias (nome, slug, descricao, banner_url, icone_url, criado_por, visibilidade, criado_em)
        VALUES (:nome, :slug, :descricao, :banner_url, :icone_url, :criado_por, :visibilidade, :criado_em)
    ");

    $stmtInsert->execute([
        ':nome' => $nome,
        ':slug' => $slug,
        ':descricao' => $descricao ?: 'Franquia criada pela comunidade de cinéfilos.',
        ':banner_url' => $banner_url,
        ':icone_url' => $icone_url,
        ':criado_por' => $usuario_id,
        ':visibilidade' => in_array($visibilidade, ['publica', 'privada', 'compartilhada']) ? $visibilidade : 'publica',
        ':criado_em' => date('Y-m-d H:i:s')
    ]);

    $novo_id = $pdo->lastInsertId();

    registrar_log($pdo, 'CRIOU_FRANQUIA', "Criou a nova franquia '{$nome}' (slug: {$slug}).", $usuario_id, $usuario_nome);
    definir_toast("Franquia '{$nome}' criada com sucesso! Agora você pode adicionar os filmes dela.", 'sucesso');
    header("Location: ../franquia.php?slug=" . urlencode($slug));
    exit;

} catch (Exception $e) {
    error_log("Erro ao criar franquia: " . $e->getMessage());
    definir_toast('Erro no servidor ao criar franquia: ' . $e->getMessage(), 'erro');
    header('Location: ../nova-franquia.php');
    exit;
}
