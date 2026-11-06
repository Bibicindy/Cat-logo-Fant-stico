<?php
$page_title = 'Catálogo Fantástico - Universos & Cronologia Cinematográfica';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Buscar todas as franquias
$stmt = $pdo->query("SELECT * FROM franquias ORDER BY id ASC");
$franquias = $stmt->fetchAll();

// Dados de progresso por franquia para o usuário atual
$progresso_por_franquia = [];
$usuario_id = $usuario_logado['id'] ?? null;

foreach ($franquias as $fr) {
    $frid = $fr['id'];
    
    // Total de filmes da franquia
    $stT = $pdo->prepare("SELECT COUNT(*) as total FROM filmes WHERE franquia_id = :frid");
    $stT->execute([':frid' => $frid]);
    $total = (int)$stT->fetch()['total'];

    $assistidos = 0;
    if ($usuario_id) {
        $stA = $pdo->prepare("
            SELECT COUNT(*) as assistidos 
            FROM progresso_usuario p 
            INNER JOIN filmes f ON f.id = p.filme_id 
            WHERE f.franquia_id = :frid AND p.usuario_id = :uid AND p.assistido = 1
        ");
        $stA->execute([':frid' => $frid, ':uid' => $usuario_id]);
        $assistidos = (int)$stA->fetch()['assistidos'];
    } else {
        if (!empty($_SESSION['progresso_temp'])) {
            $stF = $pdo->prepare("SELECT id FROM filmes WHERE franquia_id = :frid");
            $stF->execute([':frid' => $frid]);
            $fids = $stF->fetchAll(PDO::FETCH_COLUMN);
            foreach ($fids as $fid) {
                if (!empty($_SESSION['progresso_temp'][$fid])) {
                    $assistidos++;
                }
            }
        }
    }

    $pct = $total > 0 ? round(($assistidos / $total) * 100) : 0;
    $progresso_por_franquia[$frid] = [
        'total' => $total,
        'assistidos' => $assistidos,
        'porcentagem' => $pct
    ];
}

// Estatísticas Globais
$total_filmes_global = (int)$pdo->query("SELECT COUNT(*) FROM filmes")->fetchColumn();
$total_franquias_global = count($franquias);
$total_logs_global = (int)$pdo->query("SELECT COUNT(*) FROM logs_auditoria")->fetchColumn();
?>

<!-- Hero Banner Cinematográfico -->
<div class="relative overflow-hidden bg-gradient-to-b from-indigo-950/40 via-[#0b0f19] to-[#0b0f19] border-b border-white/5 py-16 sm:py-24">
    <div class="absolute inset-0 z-0 opacity-20 pointer-events-none bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:24px_24px]"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-semibold mb-6 animate-slide-down">
            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
            Mapeamento de Universos & Cronologias
        </div>
        
        <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white mb-6 max-w-4xl mx-auto leading-tight">
            Assista suas franquias favoritas na <span class="bg-gradient-to-r from-indigo-400 via-purple-300 to-pink-400 bg-clip-text text-transparent">ordem perfeita</span>
        </h1>
        
        <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto mb-10 leading-relaxed">
            Elimine a perda de tempo procurando onde assistir cada título. Alterne entre a <strong>Ordem Cronológica</strong> e de <strong>Lançamento</strong>, marque seu progresso e personalize suas listas.
        </p>

        <!-- Ações Principais -->
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="#catalogo" class="px-6 py-3.5 rounded-xl font-bold text-sm bg-indigo-600 hover:bg-indigo-500 text-white shadow-xl shadow-indigo-600/30 transition flex items-center gap-2">
                <i class="fas fa-play text-xs"></i>
                Explorar Franquias
            </a>
            <a href="nova-franquia.php" class="px-6 py-3.5 rounded-xl font-bold text-sm bg-white/5 hover:bg-white/10 text-gray-200 border border-white/10 transition flex items-center gap-2">
                <i class="fas fa-plus text-xs text-emerald-400"></i>
                Adicionar Nova Franquia
            </a>
        </div>

        <!-- Barra de Estatísticas Rápidas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-4xl mx-auto mt-16 pt-10 border-t border-white/5 text-left">
            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                <div class="text-2xl font-black text-white"><?= $total_franquias_global ?></div>
                <div class="text-xs text-gray-400 mt-1">Universos Mapeados</div>
            </div>
            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                <div class="text-2xl font-black text-indigo-400"><?= $total_filmes_global ?></div>
                <div class="text-xs text-gray-400 mt-1">Filmes Catalogados</div>
            </div>
            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                <div class="text-2xl font-black text-emerald-400">100%</div>
                <div class="text-xs text-gray-400 mt-1">Onde Assistir Mapeado</div>
            </div>
            <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                <div class="text-2xl font-black text-amber-400"><?= $total_logs_global ?></div>
                <div class="text-xs text-gray-400 mt-1">Auditorias Registradas</div>
            </div>
        </div>
    </div>
</div>

<!-- Seção do Catálogo -->
<section id="catalogo" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">Explore os Universos</div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Franquias Disponíveis</h2>
        </div>
        <div>
            <span class="text-xs text-gray-400">
                Selecione uma franquia para reorganizar ordens e marcar filmes vistos.
            </span>
        </div>
    </div>

    <!-- Grid de Franquias -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($franquias as $fr): 
            $prog = $progresso_por_franquia[$fr['id']] ?? ['total' => 0, 'assistidos' => 0, 'porcentagem' => 0];
        ?>
            <div class="group relative rounded-2xl overflow-hidden bg-slate-900/60 border border-white/10 hover:border-indigo-500/50 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/10 flex flex-col">
                <!-- Imagem Banner -->
                <div class="relative h-48 overflow-hidden bg-slate-800">
                    <img src="<?= htmlspecialchars($fr['banner_url'] ?: 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800') ?>" 
                         alt="<?= htmlspecialchars($fr['nome']) ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0b0f19] via-[#0b0f19]/40 to-transparent"></div>
                    
                    <!-- Badge de Total de Filmes -->
                    <div class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-black/60 backdrop-blur-md border border-white/10 text-xs font-semibold text-white flex items-center gap-1.5">
                        <i class="fas fa-film text-indigo-400"></i>
                        <?= $prog['total'] ?> filmes
                    </div>

                    <?php if (!empty($fr['visibilidade']) && $fr['visibilidade'] === 'privada'): ?>
                        <div class="absolute top-3 left-3 px-2 py-0.5 rounded bg-rose-500/80 text-[10px] font-bold text-white uppercase">
                            Privada
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Conteúdo do Card -->
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white group-hover:text-indigo-300 transition mb-2">
                            <?= htmlspecialchars($fr['nome']) ?>
                        </h3>
                        <p class="text-sm text-gray-400 line-clamp-3 leading-relaxed mb-6">
                            <?= htmlspecialchars($fr['descricao'] ?: 'Explore todos os filmes desta franquia em ordem de lançamento ou cronológica.') ?>
                        </p>
                    </div>

                    <!-- Progresso do Usuário -->
                    <div class="pt-4 border-t border-white/5 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-400">
                                <i class="fas fa-check-circle text-emerald-400 mr-1"></i> Progresso
                            </span>
                            <span class="font-bold text-white">
                                <?= $prog['assistidos'] ?> de <?= $prog['total'] ?> (<?= $prog['porcentagem'] ?>%)
                            </span>
                        </div>
                        
                        <!-- Barra de Progresso -->
                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden border border-white/5">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-400 transition-all duration-500 rounded-full" 
                                 style="width: <?= $prog['porcentagem'] ?>%;"></div>
                        </div>

                        <!-- Botão Acessar -->
                        <div class="pt-2">
                            <a href="franquia.php?slug=<?= urlencode($fr['slug']) ?>" class="w-full py-2.5 px-4 rounded-xl text-center font-semibold text-sm bg-white/5 hover:bg-indigo-600 text-white border border-white/10 hover:border-indigo-500 transition-all duration-200 flex items-center justify-center gap-2">
                                <span>Mapear Universo & Rota</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Bloco de Adicionar Nova Franquia -->
    <div class="mt-12 p-8 rounded-2xl bg-gradient-to-r from-indigo-950/30 via-slate-900/60 to-purple-950/30 border border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h3 class="text-xl font-bold text-white mb-1">Falta alguma franquia ou saga?</h3>
            <p class="text-sm text-gray-400">
                Você pode cadastrar novas franquias a qualquer momento e iniciar uma nova lista com ordens cronológicas e de lançamento.
            </p>
        </div>
        <a href="nova-franquia.php" class="px-5 py-3 rounded-xl font-bold text-sm bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition whitespace-nowrap flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Cadastrar Franquia
        </a>
    </div>
</section>

<!-- Banner de Convite para Salvar Listas -->
<?php if (!$usuario_logado): ?>
<section class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full mb-12">
    <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 border border-indigo-500/20 text-center relative overflow-hidden">
        <div class="max-w-2xl mx-auto relative z-10">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600/20 border border-indigo-500/40 text-indigo-400 flex items-center justify-center mx-auto mb-4 text-xl">
                <i class="fas fa-user-lock"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">
                Quer salvar seu progresso e sincronizar entre aparelhos?
            </h3>
            <p class="text-sm text-gray-300 mb-6 leading-relaxed">
                Você não precisa de conta para navegar ou testar as listas, mas ao criar sua conta gratuita, todas as alterações, notas e filmes marcados como assistidos ficam salvos com segurança.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="cadastro.php" class="px-6 py-3 rounded-xl font-bold text-sm bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition">
                    Criar Conta Gratuita
                </a>
                <a href="login.php" class="px-6 py-3 rounded-xl font-medium text-sm bg-white/5 hover:bg-white/10 text-gray-200 border border-white/10 transition">
                    Já possuo login
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
