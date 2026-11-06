<?php
$page_title = 'Privacidade, LGPD & Regras de Conteúdo - Catálogo Fantástico';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="index.php" class="hover:text-white transition flex items-center gap-1">
            <i class="fas fa-home"></i> Franquias
        </a>
        <span>/</span>
        <span class="text-cyan-400 font-medium">Termos & Privacidade</span>
    </div>

    <div class="bg-[#111827] border border-white/10 rounded-3xl p-8 sm:p-12 shadow-2xl space-y-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold mb-3">
                <i class="fas fa-shield-alt"></i> Conformidade Legal & Segurança
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-3">
                Privacidade, LGPD & Regras de Conteúdo
            </h1>
            <p class="text-sm text-gray-400 leading-relaxed">
                Última atualização: <?= date('d/m/Y') ?>. Transparência total sobre como tratamos suas informações e asseguramos um ambiente seguro para cinéfilos.
            </p>
        </div>

        <!-- Seção 1: LGPD e Coleta de Dados -->
        <section id="lgpd" class="pt-6 border-t border-white/5 space-y-3">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-user-lock text-indigo-400"></i>
                1. Privacidade de Dados & LGPD (Lei nº 13.709/2018)
            </h2>
            <div class="text-sm text-gray-300 space-y-3 leading-relaxed">
                <p>
                    O <strong>Catálogo Fantástico</strong> valoriza rigorosamente a privacidade dos seus usuários. Coletamos estritamente os dados necessários para a prestação do serviço:
                </p>
                <ul class="list-disc list-inside space-y-1 text-gray-400 pl-2">
                    <li><strong class="text-white">Acesso Sem Cadastro:</strong> O usuário pode navegar, consultar ordens cronológicas e usar listas temporárias sem necessidade de fornecer qualquer dado pessoal.</li>
                    <li><strong class="text-white">Cadastro Seguro:</strong> Para persistir listas personalizadas e sincronizar progresso em múltiplos dispositivos, solicitamos apenas nome de usuário, e-mail ou telefone e senha.</li>
                    <li><strong class="text-white">Criptografia:</strong> Todas as senhas são irreversivelmente criptografadas utilizando o algoritmo de hashing robusto <code>bcrypt</code>.</li>
                    <li><strong class="text-white">Consentimento para Recomendações:</strong> Com a autorização do usuário, dados anonimizados de buscas anteriores podem ser processados exclusivamente para aprimorar as sugestões cinematográficas.</li>
                </ul>
            </div>
        </section>

        <!-- Seção 2: Classificação e Conteúdo -->
        <section id="classificacao" class="pt-6 border-t border-white/5 space-y-3">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-400"></i>
                2. Regras de Conteúdo & Classificação Indicativa
            </h2>
            <div class="text-sm text-gray-300 space-y-3 leading-relaxed">
                <p>
                    Para proteger o público e respeitar as normas vigentes, estabelecemos políticas estritas de moderação:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 my-3">
                    <div class="p-3 rounded-xl bg-slate-900 border border-white/5 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-black etaria-livre mb-1">L</span>
                        <div class="text-[11px] text-gray-300">Livre para Todos</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900 border border-white/5 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-black etaria-10 mb-1">10</span>
                        <div class="text-[11px] text-gray-300">Não recomendado < 10</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900 border border-white/5 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-black etaria-12 mb-1">12</span>
                        <div class="text-[11px] text-gray-300">Não recomendado < 12</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900 border border-white/5 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-black etaria-14 mb-1">14</span>
                        <div class="text-[11px] text-gray-300">Não recomendado < 14</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900 border border-white/5 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-black etaria-16 mb-1">16</span>
                        <div class="text-[11px] text-gray-300">Não recomendado < 16</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900 border border-white/5 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-black etaria-18 mb-1">18</span>
                        <div class="text-[11px] text-gray-300">Exclusivo Adulto 18+</div>
                    </div>
                </div>
                <p>
                    <strong class="text-rose-400">Conteúdos e Anúncios Proibidos:</strong> É expressamente proibida a publicação de conteúdos ou anúncios que incitem qualquer tipo de crime, violência, preconceito, exploração ou apologia a substâncias ilegais ou vícios. Toda e qualquer publicação fora das diretrizes é removida imediatamente com registro na auditoria.
                </p>
            </div>
        </section>

        <!-- Seção 3: Auditoria & Rastreabilidade -->
        <section class="pt-6 border-t border-white/5 space-y-3">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-history text-cyan-400"></i>
                3. Auditoria & Histórico de Modificações
            </h2>
            <p class="text-sm text-gray-300 leading-relaxed">
                Todas as operações críticas do sistema (criação de franquias, alteração da ordem cronológica, exclusão de filmes e permissões concedidas) geram um registro imutável com data, hora, IP de origem e identificação do autor na página pública de <a href="historico.php" class="text-indigo-400 underline hover:text-white font-medium">Histórico & Logs</a>.
            </p>
        </section>

        <!-- Seção 4: Disponibilidade e Tratamento de Erros -->
        <section class="pt-6 border-t border-white/5 space-y-3">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-server text-emerald-400"></i>
                4. Disponibilidade & Resiliência Técnica
            </h2>
            <p class="text-sm text-gray-300 leading-relaxed">
                O Catálogo Fantástico foi projetado para alta resiliência, utilizando conexões seguras PDO com prepared statements contra SQL Injections e armazenamento desacoplado. Em caso de indisponibilidade momentânea ou instabilidade de rede, mensagens claras via notificações Toasts orientam o usuário sem perda de dados na interface.
            </p>
        </section>

        <div class="pt-6 border-t border-white/10 flex justify-end">
            <a href="index.php" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition">
                Voltar ao Catálogo
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
