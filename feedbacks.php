<?php
$page_title = 'Avaliações e Sugestões - Catálogo Fantástico';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Buscar avaliações recentes
$feedbacks = $pdo->query("SELECT * FROM avaliacoes_feedbacks ORDER BY id DESC LIMIT 50")->fetchAll();

// Média de Avaliação
$stats = $pdo->query("SELECT COUNT(*) as total, AVG(nota) as media FROM avaliacoes_feedbacks")->fetch();
$total_avaliacoes = (int)($stats['total'] ?? 0);
$media_nota = $total_avaliacoes > 0 ? round((float)$stats['media'], 1) : 5.0;
?>

<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="index.php" class="hover:text-white transition flex items-center gap-1">
            <i class="fas fa-home"></i> Franquias
        </a>
        <span>/</span>
        <span class="text-yellow-400 font-medium">Avaliações do Público</span>
    </div>

    <!-- Cabeçalho -->
    <div class="max-w-3xl mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 text-xs font-semibold mb-3">
            <i class="fas fa-star"></i> Opinião da Comunidade
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-3">
            Avaliações & Espaço de Melhorias
        </h1>
        <p class="text-sm sm:text-base text-gray-300 leading-relaxed">
            Sua opinião é fundamental para evoluirmos a plataforma. Conte o que você achou, sugira novas franquias, aponte melhorias e ajude outros cinéfilos.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Formulário de Envio -->
        <div class="lg:col-span-1 bg-[#111827] border border-white/10 rounded-3xl p-6 sm:p-8 shadow-2xl h-fit">
            <h2 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                <i class="fas fa-pen-nib text-indigo-400 text-base"></i>
                Deixe sua Avaliação
            </h2>
            <p class="text-xs text-gray-400 mb-6">
                Compartilhe o que achou da usabilidade, catálogo ou sugira recursos.
            </p>

            <form action="actions/submit_feedback.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Seu Nome *
                    </label>
                    <input type="text" name="nome" required value="<?= htmlspecialchars($usuario_logado['nome'] ?? '') ?>" placeholder="Seu nome ou apelido" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        E-mail (Opcional)
                    </label>
                    <input type="email" name="email" value="<?= htmlspecialchars($usuario_logado['email'] ?? '') ?>" placeholder="seu@email.com" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Nota Geral (Estrelas)
                    </label>
                    <div class="flex items-center gap-2 text-2xl text-yellow-400" id="estrelas-container">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="estrela-btn hover:scale-125 transition" data-nota="<?= $i ?>" onclick="selecionarNota(<?= $i ?>)">
                                <i class="fas fa-star" id="estrela-<?= $i ?>"></i>
                            </button>
                        <?php endfor; ?>
                        <input type="hidden" name="nota" id="input-nota" value="5">
                        <span id="label-nota" class="text-xs font-bold text-gray-300 ml-2">5 de 5 estrelas</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Tipo de Mensagem
                    </label>
                    <select name="tipo" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white text-sm focus:outline-none focus:border-indigo-500">
                        <option value="sugestao">💡 Sugestão de Melhoria</option>
                        <option value="elogio">⭐ Elogio / Depoimento</option>
                        <option value="critica">🔍 Crítica Construtiva</option>
                        <option value="problema">⚠️ Relato de Erro / Bug</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Mensagem / Comentário *
                    </label>
                    <textarea name="mensagem" required rows="4" placeholder="Escreva aqui como podemos melhorar sua experiência..." 
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-xl shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane text-xs"></i>
                    Enviar Avaliação
                </button>
            </form>
        </div>

        <!-- Lista de Avaliações Recebidas -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Card de Média Geral -->
            <div class="p-6 rounded-3xl bg-slate-900/60 border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="text-4xl sm:text-5xl font-black text-yellow-400">
                        <?= $media_nota ?>
                    </div>
                    <div>
                        <div class="flex text-yellow-400 text-sm">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i class="fas fa-star <?= $s <= round($media_nota) ? '' : 'text-gray-600' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            Média baseada em <?= $total_avaliacoes ?> avaliações da comunidade
                        </div>
                    </div>
                </div>
                <div class="text-xs text-emerald-400 font-semibold bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-full">
                    <i class="fas fa-heart text-xs mr-1"></i> 98% Satisfação dos Cinéfilos
                </div>
            </div>

            <!-- Depoimentos -->
            <div class="space-y-4">
                <?php if (empty($feedbacks)): ?>
                    <div class="p-12 text-center rounded-3xl bg-white/[0.02] border border-white/5 text-gray-400">
                        <i class="fas fa-comments text-4xl mb-3 text-gray-600"></i>
                        <p class="font-semibold text-white">Nenhuma avaliação cadastrada ainda.</p>
                        <p class="text-xs mt-1">Seja o primeiro a deixar uma sugestão ou elogio usando o formulário ao lado!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($feedbacks as $fb): 
                        $tipoBadge = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                        if ($fb['tipo'] === 'elogio') $tipoBadge = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                        if ($fb['tipo'] === 'critica') $tipoBadge = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                        if ($fb['tipo'] === 'problema') $tipoBadge = 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                    ?>
                        <div class="p-6 rounded-2xl bg-slate-900/60 border border-white/10 hover:border-white/20 transition">
                            <div class="flex items-center justify-between gap-4 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center font-bold text-xs text-indigo-400 uppercase">
                                        <?= mb_substr($fb['nome'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-white"><?= htmlspecialchars($fb['nome']) ?></div>
                                        <div class="text-[11px] text-gray-500"><?= date('d/m/Y H:i', strtotime($fb['criado_em'])) ?></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase <?= $tipoBadge ?>">
                                        <?= htmlspecialchars($fb['tipo']) ?>
                                    </span>
                                    <div class="flex text-yellow-400 text-xs">
                                        <?php for ($st = 1; $st <= 5; $st++): ?>
                                            <i class="fas fa-star <?= $st <= $fb['nota'] ? '' : 'text-gray-700' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>

                            <p class="text-sm text-gray-300 leading-relaxed">
                                <?= nl2br(htmlspecialchars($fb['mensagem'])) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
function selecionarNota(nota) {
    document.getElementById('input-nota').value = nota;
    document.getElementById('label-nota').textContent = `${nota} de 5 estrelas`;
    
    for (let i = 1; i <= 5; i++) {
        const estrela = document.getElementById(`estrela-${i}`);
        if (i <= nota) {
            estrela.className = 'fas fa-star text-yellow-400';
        } else {
            estrela.className = 'fas fa-star text-gray-700';
        }
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
