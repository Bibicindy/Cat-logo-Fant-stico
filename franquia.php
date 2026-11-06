<?php
require_once __DIR__ . '/bd.php';

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    definir_toast('Franquia não selecionada.', 'erro');
    header('Location: index.php');
    exit;
}

// Buscar Franquia
$stmt = $pdo->prepare("SELECT * FROM franquias WHERE slug = :slug LIMIT 1");
$stmt->execute([':slug' => $slug]);
$franquia = $stmt->fetch();

if (!$franquia) {
    definir_toast('Franquia não encontrada.', 'erro');
    header('Location: index.php');
    exit;
}

$page_title = htmlspecialchars($franquia['nome']) . ' - Catálogo Fantástico';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$franquia_id = (int)$franquia['id'];
$usuario_id = $usuario_logado['id'] ?? null;

// Buscar Filmes da Franquia
// Ordem padrão inicial: Cronológica
$stmtFilmes = $pdo->prepare("
    SELECT f.*, 
           COALESCE(p.assistido, 0) as assistido_bd,
           COALESCE(op.posicao, f.ordem_cronologica) as posicao_custom
    FROM filmes f
    LEFT JOIN progresso_usuario p ON p.filme_id = f.id AND p.usuario_id = :uid
    LEFT JOIN ordem_personalizada op ON op.filme_id = f.id AND op.usuario_id = :uid2
    WHERE f.franquia_id = :frid
    ORDER BY f.ordem_cronologica ASC
");
$stmtFilmes->execute([
    ':uid' => $usuario_id ?: 0,
    ':uid2' => $usuario_id ?: 0,
    ':frid' => $franquia_id
]);
$filmes = $stmtFilmes->fetchAll();

// Processar filmes assistidos (suporte a sessão temporária para visitantes)
$total_filmes = count($filmes);
$assistidos_count = 0;

foreach ($filmes as &$filme) {
    if ($usuario_id) {
        $filme['assistido'] = (int)$filme['assistido_bd'] === 1;
    } else {
        $filme['assistido'] = !empty($_SESSION['progresso_temp'][$filme['id']]);
    }
    if ($filme['assistido']) {
        $assistidos_count++;
    }
}
unset($filme);

$porcentagem_inicial = $total_filmes > 0 ? round(($assistidos_count / $total_filmes) * 100) : 0;
?>

<!-- Header / Banner da Franquia -->
<div class="relative bg-slate-950 border-b border-white/10 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= htmlspecialchars($franquia['banner_url'] ?: 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=1600') ?>" 
             alt="<?= htmlspecialchars($franquia['nome']) ?>" 
             class="w-full h-full object-cover opacity-25 filter blur-sm scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b0f19] via-[#0b0f19]/80 to-transparent"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 relative z-10">
        <!-- Navegação Breadcrumb -->
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <a href="index.php" class="hover:text-white transition flex items-center gap-1">
                <i class="fas fa-home"></i> Franquias
            </a>
            <span>/</span>
            <span class="text-indigo-400 font-medium truncate"><?= htmlspecialchars($franquia['nome']) ?></span>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold mb-3">
                    <i class="fas fa-satellite-dish"></i> Mapeamento de Universo
                </div>
                <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                    <?= htmlspecialchars($franquia['nome']) ?>
                </h1>
                <p class="mt-3 text-sm sm:text-base text-gray-300 leading-relaxed">
                    <?= nl2br(htmlspecialchars($franquia['descricao'])) ?>
                </p>
            </div>

            <!-- Botões de Ação da Franquia -->
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="abrirModalAdicionarFilme()" 
                        class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    Acrescentar Filme
                </button>
                <button type="button" onclick="abrirModalCompartilhar()" 
                        class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-white/5 hover:bg-white/10 text-gray-200 border border-white/10 transition flex items-center gap-2">
                    <i class="fas fa-share-alt text-indigo-400"></i>
                    Compartilhar
                </button>
            </div>
        </div>

        <!-- Barra Fixa de Progresso -->
        <div class="mt-8 p-5 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-300">Seu Progresso no Universo</span>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span id="progresso-contador" class="text-gray-400">
                        <?= $assistidos_count ?> de <?= $total_filmes ?> concluídos
                    </span>
                    <span id="progresso-percentual" class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400 font-extrabold text-sm border border-emerald-500/30">
                        <?= $porcentagem_inicial ?>%
                    </span>
                </div>
            </div>
            
            <div class="w-full h-3 rounded-full bg-slate-800 overflow-hidden border border-white/5">
                <div id="progresso-barra" 
                     class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-400 transition-all duration-500 rounded-full" 
                     style="width: <?= $porcentagem_inicial ?>%;"></div>
            </div>

            <?php if (!$usuario_logado): ?>
                <div class="mt-2 text-[11px] text-amber-300/80 flex items-center gap-1.5">
                    <i class="fas fa-info-circle"></i>
                    <span>Você está navegando como visitante. <a href="cadastro.php" class="underline hover:text-white font-semibold">Crie sua conta</a> para sincronizar e salvar definitivamente seu progresso.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Painel de Controles: Rotas, Streaming e Busca -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-white/10">
        
        <!-- Definir Rota (Objetivo 2 do Requisito) -->
        <div>
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                <i class="fas fa-route text-indigo-400 mr-1"></i> Definir Rota de Exibição
            </div>
            <div class="flex flex-wrap items-center gap-2 bg-slate-900/80 p-1.5 rounded-2xl border border-white/5">
                <button type="button" 
                        data-rota="cronologica"
                        onclick="trocarRota('cronologica')" 
                        class="tab-rota px-4 py-2 rounded-xl font-bold text-xs sm:text-sm bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                    <i class="fas fa-history text-xs"></i>
                    <span>Ordem Cronológica (Enredo)</span>
                </button>
                <button type="button" 
                        data-rota="lancamento"
                        onclick="trocarRota('lancamento')" 
                        class="tab-rota px-4 py-2 rounded-xl font-medium text-xs sm:text-sm text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <i class="fas fa-film text-xs"></i>
                    <span>Ordem de Lançamento (Cinema)</span>
                </button>
                <button type="button" 
                        data-rota="personalizada"
                        onclick="trocarRota('personalizada')" 
                        class="tab-rota px-4 py-2 rounded-xl font-medium text-xs sm:text-sm text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <i class="fas fa-arrows-alt text-xs text-amber-400"></i>
                    <span>Minha Ordem Personalizada</span>
                </button>
            </div>
        </div>

        <!-- Filtros Rápidos de Streaming & Busca (Objetivo 1 do Requisito) -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <!-- Busca por Título -->
            <div class="relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="input-busca-filmes" placeholder="Buscar filme no universo..." 
                       class="w-full sm:w-64 pl-9 pr-4 py-2 rounded-xl bg-slate-900/80 border border-white/10 text-white placeholder-gray-500 text-xs sm:text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <!-- Filtro de Streaming -->
            <select id="filtro-streaming" onchange="filtrarFilmes(document.getElementById('input-busca-filmes').value, this.value)"
                    class="py-2 px-3 rounded-xl bg-slate-900/80 border border-white/10 text-white text-xs sm:text-sm focus:outline-none focus:border-indigo-500">
                <option value="todos">📺 Todos os Streamings</option>
                <option value="Disney+">Disney+</option>
                <option value="Netflix">Netflix</option>
                <option value="Max">Max (HBO)</option>
                <option value="Prime Video">Prime Video</option>
                <option value="Apple TV">Apple TV</option>
            </select>
        </div>
    </div>

    <!-- Lista de Filmes Reordenável -->
    <div id="lista-filmes" class="mt-8 space-y-4">
        <?php if (empty($filmes)): ?>
            <div class="p-12 text-center rounded-2xl bg-white/[0.02] border border-white/5 text-gray-400">
                <i class="fas fa-film text-4xl mb-3 text-gray-500"></i>
                <p class="text-base font-semibold text-white">Nenhum filme cadastrado nesta franquia ainda.</p>
                <p class="text-xs text-gray-400 mt-1">Clique no botão "Acrescentar Filme" acima para iniciar a lista em ordem.</p>
            </div>
        <?php else: ?>
            <?php foreach ($filmes as $index => $f): 
                $isChecked = $f['assistido'];
                $streamingClass = 'badge-disney';
                $sLower = mb_strtolower($f['streaming']);
                if (str_contains($sLower, 'netflix')) $streamingClass = 'badge-netflix';
                elseif (str_contains($sLower, 'max')) $streamingClass = 'badge-max';
                elseif (str_contains($sLower, 'prime')) $streamingClass = 'badge-prime';
                elseif (str_contains($sLower, 'apple')) $streamingClass = 'badge-apple';
            ?>
                <div id="filme-card-<?= $f['id'] ?>" 
                     data-id="<?= $f['id'] ?>"
                     data-titulo="<?= htmlspecialchars($f['titulo']) ?>"
                     data-streaming="<?= htmlspecialchars($f['streaming']) ?>"
                     data-cronologico="<?= $f['ordem_cronologica'] ?>"
                     data-lancamento="<?= $f['ordem_lancamento'] ?>"
                     data-personalizado="<?= $f['posicao_custom'] ?>"
                     class="filme-item sortable-item group relative p-4 sm:p-5 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-indigo-500/40 transition-all duration-200 <?= $isChecked ? 'ring-2 ring-emerald-500/40 bg-emerald-950/20' : '' ?>">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        
                        <!-- Lado Esquerdo: Checkbox + Posição + Pôster + Info -->
                        <div class="flex items-start sm:items-center gap-4 flex-1">
                            <!-- Checklist Checkbox (Objetivo 3 do Requisito) -->
                            <label class="cursor-pointer relative flex items-center mt-1 sm:mt-0 flex-shrink-0" title="Marcar como visto">
                                <input type="checkbox" 
                                       class="sr-only peer"
                                       <?= $isChecked ? 'checked' : '' ?>
                                       onchange="toggleFilmeProgresso(this, <?= $f['id'] ?>, <?= $franquia_id ?>)">
                                <div class="w-7 h-7 rounded-lg bg-slate-800 border-2 border-white/20 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 flex items-center justify-center text-white transition-all shadow-md group-hover:scale-105">
                                    <i class="fas fa-check text-xs <?= $isChecked ? '' : 'hidden' ?> peer-checked:block"></i>
                                </div>
                            </label>

                            <!-- Badge de Número de Posição -->
                            <div class="badge-ordem-numero w-8 text-center text-xs font-black text-gray-400 group-hover:text-indigo-400 flex-shrink-0">
                                #<?= $index + 1 ?>
                            </div>

                            <!-- Pôster Thumbnail -->
                            <div class="w-14 h-20 rounded-lg overflow-hidden bg-slate-800 flex-shrink-0 border border-white/10">
                                <img src="<?= htmlspecialchars($f['poster_url'] ?: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=200') ?>" 
                                     alt="<?= htmlspecialchars($f['titulo']) ?>" 
                                     class="w-full h-full object-cover">
                            </div>

                            <!-- Informações do Filme -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h4 class="text-base sm:text-lg font-bold text-white tracking-tight truncate">
                                        <?= htmlspecialchars($f['titulo']) ?>
                                    </h4>
                                    
                                    <!-- Badge Faixa Etária -->
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black <?= 'etaria-' . mb_strtolower(str_replace('+', '', $f['classificacao_etaria'])) ?>">
                                        <?= htmlspecialchars($f['classificacao_etaria']) ?>
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400 mb-1.5">
                                    <span><i class="far fa-calendar-alt text-indigo-400 mr-1"></i> Ano: <?= $f['ano_lancamento'] ?></span>
                                    <span><i class="far fa-clock text-amber-400 mr-1"></i> <?= htmlspecialchars($f['duracao'] ?: '2h') ?></span>
                                    <span><i class="fas fa-sitemap text-purple-400 mr-1"></i> Cronologia: #<?= $f['ordem_cronologica'] ?></span>
                                    <span><i class="fas fa-ticket-alt text-cyan-400 mr-1"></i> Lançamento: #<?= $f['ordem_lancamento'] ?></span>
                                </div>

                                <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed">
                                    <?= htmlspecialchars($f['sinopse'] ?: 'Sem sinopse disponível.') ?>
                                </p>
                            </div>
                        </div>

                        <!-- Lado Direito: Onde Assistir (Streaming) + Botões de Troca de Ordem (Objetivo 1 e 4 do Requisito) -->
                        <div class="flex items-center justify-between sm:justify-end gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-white/5">
                            
                            <!-- Badge Onde Assistir (Streaming) -->
                            <div class="flex items-center gap-1.5">
                                <span class="px-3 py-1.5 rounded-xl text-xs font-bold <?= $streamingClass ?> shadow flex items-center gap-1.5">
                                    <i class="fas fa-play-circle text-[11px]"></i>
                                    <?= htmlspecialchars($f['streaming']) ?>
                                </span>
                            </div>

                            <!-- Controles de Reordenação (Subir / Descer) -->
                            <div class="flex items-center gap-1 bg-white/5 p-1 rounded-xl border border-white/10" title="Trocar local na lista">
                                <button type="button" 
                                        onclick="moverFilme(<?= $f['id'] ?>, 'cima', <?= $franquia_id ?>)" 
                                        class="p-1.5 rounded-lg hover:bg-white/10 text-gray-400 hover:text-white transition" 
                                        title="Subir de posição">
                                    <i class="fas fa-chevron-up text-xs"></i>
                                </button>
                                <button type="button" 
                                        onclick="moverFilme(<?= $f['id'] ?>, 'baixo', <?= $franquia_id ?>)" 
                                        class="p-1.5 rounded-lg hover:bg-white/10 text-gray-400 hover:text-white transition" 
                                        title="Descer de posição">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                            </div>

                            <!-- Botão Excluir Filme -->
                            <button type="button" 
                                    onclick="confirmarExclusaoFilme(<?= $f['id'] ?>, <?= json_encode($f['titulo']) ?>, <?= json_encode($franquia['slug']) ?>)"
                                    class="p-2 rounded-xl text-gray-400 hover:text-rose-400 hover:bg-rose-500/10 transition"
                                    title="Remover filme">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- MODAL: Acrescentar Filme da Franquia (Objetivo 4) -->
<div id="modal-adicionar-filme" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-[#111827] border border-white/10 rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl animate-slide-down">
        <div class="p-6 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600/20 text-emerald-400 flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-plus"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Acrescentar Filme à Franquia</h3>
            </div>
            <button type="button" onclick="fecharModalAdicionarFilme()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="actions/add_movie.php" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="franquia_id" value="<?= $franquia_id ?>">
            <input type="hidden" name="slug_retorno" value="<?= htmlspecialchars($franquia['slug']) ?>">

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                    Título do Filme *
                </label>
                <input type="text" name="titulo" required placeholder="Ex: Deadpool & Wolverine" 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Ano de Lançamento *
                    </label>
                    <input type="number" name="ano_lancamento" required min="1900" max="2100" value="<?= date('Y') ?>" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Duração
                    </label>
                    <input type="text" name="duracao" placeholder="Ex: 2h 08m" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Ordem Cronológica (Posição)
                    </label>
                    <input type="number" name="ordem_cronologica" min="1" placeholder="Auto" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Ordem Lançamento
                    </label>
                    <input type="number" name="ordem_lancamento" min="1" placeholder="Auto" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Onde Assistir (Streaming) *
                    </label>
                    <input type="text" name="streaming" required placeholder="Ex: Disney+, Netflix, Max" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Classificação Etária
                    </label>
                    <select name="classificacao_etaria" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                        <option value="Livre">Livre (Todas as Idades)</option>
                        <option value="10">10 Anos</option>
                        <option value="12" selected>12 Anos</option>
                        <option value="14">14 Anos</option>
                        <option value="16">16 Anos</option>
                        <option value="18">18 Anos (Adulto)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                    URL do Pôster / Imagem
                </label>
                <input type="url" name="poster_url" placeholder="https://..." 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                    Sinopse Curta
                </label>
                <textarea name="sinopse" rows="2" placeholder="Descreva brevemente o enredo do filme..." 
                          class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <button type="button" onclick="fecharModalAdicionarFilme()" 
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-gray-300 font-medium text-sm transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm transition shadow-lg shadow-emerald-600/30">
                    Cadastrar Filme
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Compartilhar & Permissões de Acesso (CORE Permissões) -->
<div id="modal-compartilhar" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-[#111827] border border-white/10 rounded-2xl max-w-md w-full overflow-hidden shadow-2xl animate-slide-down">
        <div class="p-6 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600/20 text-indigo-400 flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-share-alt"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Compartilhar Lista</h3>
            </div>
            <button type="button" onclick="fecharModalCompartilhar()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <p class="text-xs text-gray-300 leading-relaxed">
                Envie o link para amigos maratonarem juntos ou defina as regras de visualização e edição.
            </p>

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                    Link Direto desta Franquia
                </label>
                <div class="flex gap-2">
                    <input type="text" id="input-link-compartilhar" readonly value="<?= 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $_SERVER['REQUEST_URI'] ?>" 
                           class="flex-1 px-3 py-2 rounded-xl bg-slate-900 border border-white/10 text-gray-300 text-xs focus:outline-none">
                    <button type="button" onclick="copiarLinkCompartilhar()" 
                            class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition">
                        Copiar
                    </button>
                </div>
            </div>

            <?php if ($usuario_logado): ?>
                <div class="pt-4 border-t border-white/10">
                    <a href="perfil.php" class="w-full block text-center py-2.5 px-4 rounded-xl text-xs font-semibold bg-white/5 hover:bg-white/10 text-indigo-300 border border-white/10 transition">
                        <i class="fas fa-cog mr-1.5"></i> Gerenciar Permissões Detalhadas no Perfil
                    </a>
                </div>
            <?php else: ?>
                <div class="p-3 rounded-xl bg-indigo-950/30 border border-indigo-500/20 text-xs text-indigo-300">
                    <i class="fas fa-user-lock mr-1"></i> Para restringir visualizações ou conceder permissões de edição para amigos, <a href="login.php" class="underline font-bold text-white">faça login</a>.
                </div>
            <?php endif; ?>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="fecharModalCompartilhar()" class="px-4 py-2 rounded-xl bg-slate-800 text-gray-300 text-xs">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalAdicionarFilme() {
    document.getElementById('modal-adicionar-filme').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function fecharModalAdicionarFilme() {
    document.getElementById('modal-adicionar-filme').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function abrirModalCompartilhar() {
    document.getElementById('modal-compartilhar').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function fecharModalCompartilhar() {
    document.getElementById('modal-compartilhar').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function copiarLinkCompartilhar() {
    const input = document.getElementById('input-link-compartilhar');
    input.select();
    navigator.clipboard.writeText(input.value);
    Toast.show('Link da franquia copiado para a área de transferência!', 'sucesso');
}

async function confirmarExclusaoFilme(filmeId, titulo, slugRetorno) {
    const confirmou = await Modal.confirm(
        'Confirmar Remoção',
        `Deseja realmente remover o filme "${titulo}" desta franquia? Esta ação será registrada no histórico de auditoria.`
    );

    if (confirmou) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'actions/delete_movie.php';

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'filme_id';
        inputId.value = filmeId;
        form.appendChild(inputId);

        const inputSlug = document.createElement('input');
        inputSlug.type = 'hidden';
        inputSlug.name = 'slug_retorno';
        inputSlug.value = slugRetorno;
        form.appendChild(inputSlug);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
