    <!-- Rodapé -->
    <footer class="mt-auto border-t border-white/10 bg-[#070a11] text-gray-400 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Coluna 1: Sobre -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-sm font-bold">
                        <i class="fas fa-film"></i>
                    </div>
                    <span class="font-bold text-white text-base">Catálogo Fantástico</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed max-w-md">
                    Sua plataforma definitiva para mapear universos cinematográficos, organizar maratonas em ordem cronológica ou de lançamento e saber exatamente onde assistir cada título sem perder tempo.
                </p>
                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded bg-white/5 border border-white/5 text-gray-300">
                        <i class="fas fa-shield-alt text-emerald-400 mr-1"></i> LGPD Conforme
                    </span>
                    <span class="px-2.5 py-1 rounded bg-white/5 border border-white/5 text-gray-300">
                        <i class="fas fa-lock text-indigo-400 mr-1"></i> Senhas Bcrypt
                    </span>
                    <span class="px-2.5 py-1 rounded bg-white/5 border border-white/5 text-gray-300">
                        <i class="fas fa-database text-amber-400 mr-1"></i> MySQL + PDO Seguro
                    </span>
                </div>
            </div>

            <!-- Coluna 2: Navegação -->
            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-3">Navegação</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="index.php" class="hover:text-white transition">Todas as Franquias</a></li>
                    <li><a href="nova-franquia.php" class="hover:text-white transition">Cadastrar Nova Franquia</a></li>
                    <li><a href="historico.php" class="hover:text-white transition">Auditoria e Logs</a></li>
                    <li><a href="feedbacks.php" class="hover:text-white transition">Avaliar a Plataforma</a></li>
                </ul>
            </div>

            <!-- Coluna 3: Regras e Privacidade -->
            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-3">Transparência</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="privacidade.php" class="hover:text-white transition">Políticas de Privacidade</a></li>
                    <li><a href="privacidade.php#lgpd" class="hover:text-white transition">Tratamento de Dados (LGPD)</a></li>
                    <li><a href="privacidade.php#classificacao" class="hover:text-white transition">Regras de Conteúdo & Idade</a></li>
                    <li><a href="feedbacks.php" class="hover:text-white transition">Canal de Sugestões</a></li>
                </ul>
            </div>
        </div>

        <!-- Aviso Regulatório & Direitos -->
        <div class="max-w-7xl mx-auto pt-6 border-t border-white/5 text-xs text-gray-400 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>
                &copy; <?= date('Y') ?> Catálogo Fantástico. Desenvolvido para amantes da sétima arte. Todos os direitos reservados.
            </p>
            <p class="text-gray-400 text-center sm:text-right">
                Classificação indicativa conforme diretrizes oficiais. Conteúdos moderados contra incitação à violência ou ilícitos.
            </p>
        </div>
    </footer>

    <!-- Inclusão do Modal e Toasts -->
    <?php require_once __DIR__ . '/modal.php'; ?>
    <?php require_once __DIR__ . '/toast.php'; ?>

    <!-- Scripts Globais -->
    <script src="assets/js/main.js"></script>
</body>
</html>
