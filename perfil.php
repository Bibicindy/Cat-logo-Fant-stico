<?php
$page_title = 'Meu Perfil - Catálogo Fantástico';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

if (!$usuario_logado) {
    definir_toast('Você precisa estar conectado para acessar o perfil.', 'aviso');
    header('Location: login.php');
    exit;
}

$uid = $usuario_logado['id'];

// Estatísticas do Usuário
$stmtTotalAssistidos = $pdo->prepare("SELECT COUNT(*) FROM progresso_usuario WHERE usuario_id = :uid AND assistido = 1");
$stmtTotalAssistidos->execute([':uid' => $uid]);
$total_assistidos = (int)$stmtTotalAssistidos->fetchColumn();

$total_filmes_global = (int)$pdo->query("SELECT COUNT(*) FROM filmes")->fetchColumn();
$pct_geral = $total_filmes_global > 0 ? round(($total_assistidos / $total_filmes_global) * 100) : 0;

// Franquias e Progresso Individual
$franquias = $pdo->query("SELECT * FROM franquias ORDER BY nome ASC")->fetchAll();

// Permissões de Listas do Usuário
$stmtPerm = $pdo->prepare("SELECT * FROM permissoes_listas WHERE dono_id = :uid");
$stmtPerm->execute([':uid' => $uid]);
$permissoes_raw = $stmtPerm->fetchAll();
$permissoes = [];
foreach ($permissoes_raw as $p) {
    $permissoes[$p['franquia_id']] = $p;
}
?>

<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="index.php" class="hover:text-white transition flex items-center gap-1">
            <i class="fas fa-home"></i> Franquias
        </a>
        <span>/</span>
        <span class="text-indigo-400 font-medium">Meu Perfil</span>
    </div>

    <!-- Cabeçalho do Perfil -->
    <div class="p-8 rounded-3xl bg-[#111827] border border-white/10 shadow-2xl mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white text-2xl sm:text-3xl font-black uppercase shadow-xl shadow-indigo-600/30">
                <?= mb_substr($usuario_logado['nome'] ?? $usuario_logado['usuario'], 0, 1) ?>
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                        <?= htmlspecialchars($usuario_logado['nome']) ?>
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 uppercase">
                        <?= htmlspecialchars($usuario_logado['papel']) ?>
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 mt-2">
                    <span><i class="fas fa-at text-indigo-400 mr-1"></i> @<?= htmlspecialchars($usuario_logado['usuario']) ?></span>
                    <?php if (!empty($usuario_logado['email'])): ?>
                        <span><i class="far fa-envelope text-indigo-400 mr-1"></i> <?= htmlspecialchars($usuario_logado['email']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($usuario_logado['telefone'])): ?>
                        <span><i class="fas fa-phone-alt text-emerald-400 mr-1"></i> <?= htmlspecialchars($usuario_logado['telefone']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Indicador Geral de Maratonas -->
        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center gap-4">
            <div>
                <div class="text-3xl font-black text-emerald-400"><?= $total_assistidos ?></div>
                <div class="text-xs text-gray-400">Filmes Assistidos</div>
            </div>
            <div class="w-px h-10 bg-white/10"></div>
            <div>
                <div class="text-3xl font-black text-indigo-400"><?= $pct_geral ?>%</div>
                <div class="text-xs text-gray-400">Do Acervo Geral</div>
            </div>
        </div>
    </div>

    <!-- Abas / Seções: Progresso e Gerenciamento de Permissões -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Coluna 1: Progresso por Franquia -->
        <div class="lg:col-span-1 space-y-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-tasks text-emerald-400"></i>
                Progresso por Franquia
            </h2>

            <div class="space-y-3">
                <?php foreach ($franquias as $fr): 
                    $frid = $fr['id'];
                    $stT = $pdo->prepare("SELECT COUNT(*) FROM filmes WHERE franquia_id = :frid");
                    $stT->execute([':frid' => $frid]);
                    $tot = (int)$stT->fetchColumn();

                    $stA = $pdo->prepare("SELECT COUNT(*) FROM progresso_usuario p INNER JOIN filmes f ON f.id = p.filme_id WHERE f.franquia_id = :frid AND p.usuario_id = :uid AND p.assistido = 1");
                    $stA->execute([':frid' => $frid, ':uid' => $uid]);
                    $ast = (int)$stA->fetchColumn();

                    $pct = $tot > 0 ? round(($ast / $tot) * 100) : 0;
                ?>
                    <div class="p-4 rounded-2xl bg-[#111827] border border-white/10">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <a href="franquia.php?slug=<?= urlencode($fr['slug']) ?>" class="font-bold text-white hover:text-indigo-300 transition truncate max-w-[200px]">
                                <?= htmlspecialchars($fr['nome']) ?>
                            </a>
                            <span class="font-bold text-emerald-400"><?= $pct ?>%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden mb-1">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-400 rounded-full" style="width: <?= $pct ?>%;"></div>
                        </div>
                        <div class="text-[11px] text-gray-400">
                            <?= $ast ?> de <?= $tot ?> filmes concluídos
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Coluna 2 e 3: Gerenciamento de Permissões e Compartilhamento (Regra CORE de Permissões) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-user-shield text-indigo-400"></i>
                        Permissões de Acesso e Convites
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Defina se outros usuários podem visualizar, editar, excluir ou administrar suas listas.
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <?php foreach ($franquias as $fr): 
                    $perm = $permissoes[$fr['id']] ?? null;
                    $tipoAcesso = $perm ? $perm['tipo_acesso'] : 'publico';
                    $podeEditar = $perm ? (bool)$perm['pode_editar'] : false;
                    $podeExcluir = $perm ? (bool)$perm['pode_excluir'] : false;
                    $podeAdmin = $perm ? (bool)$perm['pode_administrar'] : false;
                    $codigoConvite = $perm ? $perm['codigo_convite'] : '';
                ?>
                    <div class="p-6 rounded-2xl bg-[#111827] border border-white/10">
                        <form action="actions/update_permissions.php" method="POST" class="space-y-4">
                            <input type="hidden" name="franquia_id" value="<?= $fr['id'] ?>">

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-white/5">
                                <h3 class="font-bold text-white text-base">
                                    <?= htmlspecialchars($fr['nome']) ?>
                                </h3>
                                <div>
                                    <select name="tipo_acesso" class="py-1 px-3 rounded-lg bg-slate-900 border border-white/10 text-xs font-semibold text-white focus:outline-none focus:border-indigo-500">
                                        <option value="publico" <?= $tipoAcesso === 'publico' ? 'selected' : '' ?>>🌐 Público (Todos)</option>
                                        <option value="privado" <?= $tipoAcesso === 'privado' ? 'selected' : '' ?>>🔒 Privado (Apenas Eu)</option>
                                        <option value="compartilhado" <?= $tipoAcesso === 'compartilhado' ? 'selected' : '' ?>>👥 Compartilhado via Convite</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Permissões Concedidas para Outros Usuários -->
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                O que outros usuários podem fazer nesta sua lista:
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-900/60 border border-white/5 cursor-pointer">
                                    <input type="checkbox" name="pode_editar" value="1" <?= $podeEditar ? 'checked' : '' ?> class="rounded bg-slate-800 border-white/20 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-300">Pode Editar Ordem</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-900/60 border border-white/5 cursor-pointer">
                                    <input type="checkbox" name="pode_excluir" value="1" <?= $podeExcluir ? 'checked' : '' ?> class="rounded bg-slate-800 border-white/20 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-300">Pode Excluir Filmes</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-900/60 border border-white/5 cursor-pointer">
                                    <input type="checkbox" name="pode_administrar" value="1" <?= $podeAdmin ? 'checked' : '' ?> class="rounded bg-slate-800 border-white/20 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-300">Administrador Total</span>
                                </label>
                            </div>

                            <?php if ($codigoConvite): ?>
                                <div class="p-3 rounded-xl bg-indigo-950/20 border border-indigo-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="text-gray-300 truncate">
                                        <span class="font-bold text-indigo-400">Código de Convite:</span> <?= htmlspecialchars($codigoConvite) ?>
                                    </div>
                                    <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($codigoConvite) ?>'); Toast.show('Código copiado!', 'sucesso');" 
                                            class="text-indigo-400 hover:text-white font-bold whitespace-nowrap">
                                        <i class="fas fa-copy"></i> Copiar Código
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow transition">
                                    Salvar Permissões
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
