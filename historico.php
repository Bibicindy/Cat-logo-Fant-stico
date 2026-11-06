<?php
$page_title = 'Histórico e Auditoria - Catálogo Fantástico';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Filtros
$termo = trim($_GET['termo'] ?? '');
$tipo_acao = trim($_GET['acao'] ?? '');

$sql = "SELECT * FROM logs_auditoria WHERE 1=1";
$params = [];

if (!empty($termo)) {
    $sql .= " AND (usuario_nome LIKE :termo OR detalhes LIKE :termo2 OR ip LIKE :termo3)";
    $params[':termo'] = "%{$termo}%";
    $params[':termo2'] = "%{$termo}%";
    $params[':termo3'] = "%{$termo}%";
}

if (!empty($tipo_acao)) {
    $sql .= " AND acao = :tipo_acao";
    $params[':tipo_acao'] = $tipo_acao;
}

$sql .= " ORDER BY id DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Obter tipos de ações únicos para o select
$acoes_unicas = $pdo->query("SELECT DISTINCT acao FROM logs_auditoria ORDER BY acao ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
    <!-- Breadcrumb & Título -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
        <a href="index.php" class="hover:text-white transition flex items-center gap-1">
            <i class="fas fa-home"></i> Franquias
        </a>
        <span>/</span>
        <span class="text-amber-400 font-medium">Auditoria do Sistema</span>
    </div>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold mb-2">
                <i class="fas fa-shield-alt"></i> Transparência & Rastreabilidade
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                Histórico & Logs de Auditoria
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1 max-w-2xl leading-relaxed">
                Registro permanente de todas as modificações, reordenações, cadastros e atualizações realizadas por usuários e visitantes no Catálogo Fantástico.
            </p>
        </div>

        <!-- Estatística de Logs -->
        <div class="px-4 py-2.5 rounded-2xl bg-white/[0.03] border border-white/10 text-xs text-gray-300 flex items-center gap-3">
            <i class="fas fa-file-invoice text-amber-400 text-base"></i>
            <div>
                <span class="font-bold text-white"><?= count($logs) ?></span> registros exibidos
            </div>
        </div>
    </div>

    <!-- Filtros de Busca -->
    <form method="GET" action="historico.php" class="mb-8 p-4 rounded-2xl bg-slate-900/80 border border-white/10 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" name="termo" value="<?= htmlspecialchars($termo) ?>" placeholder="Buscar por usuário, detalhes da alteração ou IP..." 
                   class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-800 border border-white/10 text-white placeholder-gray-500 text-xs sm:text-sm focus:outline-none focus:border-indigo-500">
        </div>

        <select name="acao" class="py-2 px-3 rounded-xl bg-slate-800 border border-white/10 text-white text-xs sm:text-sm focus:outline-none focus:border-indigo-500">
            <option value="">Todas as Ações</option>
            <?php foreach ($acoes_unicas as $ac): ?>
                <option value="<?= htmlspecialchars($ac) ?>" <?= $tipo_acao === $ac ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ac) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2">
            <i class="fas fa-filter text-xs"></i>
            Filtrar
        </button>

        <?php if (!empty($termo) || !empty($tipo_acao)): ?>
            <a href="historico.php" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-gray-300 text-xs sm:text-sm font-medium transition flex items-center justify-center">
                Limpar
            </a>
        <?php endif; ?>
    </form>

    <!-- Tabela Responsiva de Logs -->
    <div class="rounded-2xl border border-white/10 overflow-hidden bg-slate-900/60 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-gray-300">
                <thead class="bg-slate-950/80 border-b border-white/10 text-[11px] uppercase tracking-wider text-gray-400 font-bold">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Data / Hora</th>
                        <th class="py-3.5 px-4">Usuário</th>
                        <th class="py-3.5 px-4">Ação</th>
                        <th class="py-3.5 px-4 sm:px-6">Detalhes</th>
                        <th class="py-3.5 px-4">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">
                                <i class="fas fa-history text-3xl mb-2"></i>
                                <p>Nenhum registro de log encontrado com os filtros aplicados.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): 
                            $acaoBadgeColor = 'bg-slate-800 text-gray-300 border-white/10';
                            $acaoNome = $log['acao'];
                            if (str_contains($acaoNome, 'CADASTRO') || str_contains($acaoNome, 'CRIOU') || str_contains($acaoNome, 'SUCESSO')) {
                                $acaoBadgeColor = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                            } elseif (str_contains($acaoNome, 'EXCLUIU') || str_contains($acaoNome, 'FALHA')) {
                                $acaoBadgeColor = 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                            } elseif (str_contains($acaoNome, 'ALTEROU') || str_contains($acaoNome, 'REORDENOU')) {
                                $acaoBadgeColor = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                            } elseif (str_contains($acaoNome, 'MARCOU') || str_contains($acaoNome, 'DESMARCOU')) {
                                $acaoBadgeColor = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                            }
                        ?>
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="py-3 px-4 sm:px-6 whitespace-nowrap text-gray-400 font-mono text-xs">
                                    <?= date('d/m/Y H:i:s', strtotime($log['criado_em'])) ?>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-medium text-white">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-white/5 flex items-center justify-center text-[10px] text-indigo-400 uppercase font-bold">
                                            <?= mb_substr($log['usuario_nome'], 0, 1) ?>
                                        </div>
                                        <span><?= htmlspecialchars($log['usuario_nome']) ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-md text-[10px] sm:text-xs font-bold border <?= $acaoBadgeColor ?>">
                                        <?= htmlspecialchars($log['acao']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 sm:px-6 text-gray-200">
                                    <?= htmlspecialchars($log['detalhes']) ?>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px] text-gray-500">
                                    <?= htmlspecialchars($log['ip']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
