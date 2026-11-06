<nav class="sticky top-0 z-40 w-full glass-header border-b border-sky-500/20 shadow-xl shadow-sky-950/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            
            <!-- Logo Quarteto Fantástico -->
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-3 group">
                    <div class="emblema-f4 w-10 h-10 sm:w-12 sm:h-12 text-lg sm:text-xl group-hover:scale-105 transition-transform duration-300">
                        4
                    </div>
                    <div>
                        <div class="text-lg sm:text-xl font-fantastic font-black tracking-wider bg-gradient-to-r from-sky-300 via-sky-100 to-orange-400 bg-clip-text text-transparent group-hover:from-sky-200 group-hover:to-orange-300 transition">
                            Catálogo Fantástico
                        </div>
                        <div class="hidden sm:flex items-center gap-1.5 text-[10px] tracking-widest uppercase font-bold text-orange-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-ping"></span>
                            Universos & Maratonas
                        </div>
                    </div>
                </a>
            </div>

            <!-- Links Desktop com estilo Quarteto Fantástico -->
            <div class="hidden md:flex items-center gap-1.5">
                <a href="index.php" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white hover:bg-sky-500/10 hover:border-sky-500/30 border border-transparent transition flex items-center gap-2">
                    <i class="fas fa-layer-group text-sky-400 text-xs"></i>
                    Franquias
                </a>
                <a href="nova-franquia.php" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white hover:bg-orange-500/10 hover:border-orange-500/30 border border-transparent transition flex items-center gap-2">
                    <i class="fas fa-plus-circle text-orange-400 text-xs"></i>
                    Nova Franquia
                </a>
                <a href="historico.php" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white hover:bg-sky-500/10 hover:border-sky-500/30 border border-transparent transition flex items-center gap-2">
                    <i class="fas fa-history text-sky-400 text-xs"></i>
                    Histórico & Logs
                </a>
                <a href="feedbacks.php" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white hover:bg-orange-500/10 hover:border-orange-500/30 border border-transparent transition flex items-center gap-2">
                    <i class="fas fa-star text-orange-400 text-xs"></i>
                    Avaliações
                </a>
            </div>

            <!-- Autenticação Desktop -->
            <div class="hidden md:flex items-center gap-3">
                <?php if ($usuario_logado): ?>
                    <a href="perfil.php" class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-sky-500/30 transition text-xs sm:text-sm shadow-md">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-sky-500 to-blue-600 flex items-center justify-center font-black text-xs text-white uppercase shadow-sm">
                            <?= mb_substr($usuario_logado['nome'] ?? $usuario_logado['usuario'], 0, 1) ?>
                        </div>
                        <span class="font-bold text-slate-200"><?= htmlspecialchars($usuario_logado['nome'] ?? $usuario_logado['usuario']) ?></span>
                    </a>
                    <a href="actions/auth_logout.php" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Sair do Sistema">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white hover:bg-white/5 transition">
                        Entrar
                    </a>
                    <a href="cadastro.php" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white shadow-lg shadow-sky-500/30 transition flex items-center gap-1.5">
                        <i class="fas fa-bolt text-orange-400 text-xs"></i>
                        Criar Conta
                    </a>
                <?php endif; ?>
            </div>

            <!-- Botão Mobile -->
            <div class="flex md:hidden items-center gap-2">
                <button type="button" id="mobile-menu-btn" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition" aria-label="Abrir Menu">
                    <i class="fas fa-bars text-lg text-sky-400"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Mobile Dropdown -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-sky-500/20 bg-[#070c18]/95 backdrop-blur-xl px-4 pt-3 pb-6 space-y-2">
        <a href="index.php" class="block px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-200 hover:bg-sky-500/10">
            <i class="fas fa-layer-group text-sky-400 mr-2"></i> Franquias
        </a>
        <a href="nova-franquia.php" class="block px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-200 hover:bg-orange-500/10">
            <i class="fas fa-plus-circle text-orange-400 mr-2"></i> Nova Franquia
        </a>
        <a href="historico.php" class="block px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-200 hover:bg-sky-500/10">
            <i class="fas fa-history text-sky-400 mr-2"></i> Histórico & Logs
        </a>
        <a href="feedbacks.php" class="block px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-200 hover:bg-orange-500/10">
            <i class="fas fa-star text-orange-400 mr-2"></i> Avaliar a Plataforma
        </a>
        <a href="privacidade.php" class="block px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-200 hover:bg-white/5">
            <i class="fas fa-shield-alt text-sky-400 mr-2"></i> Privacidade & LGPD
        </a>
        
        <div class="pt-4 border-t border-white/10 space-y-2">
            <?php if ($usuario_logado): ?>
                <div class="px-3.5 py-2 text-xs text-slate-400">
                    Conectado como <strong class="text-white"><?= htmlspecialchars($usuario_logado['nome']) ?></strong>
                </div>
                <a href="perfil.php" class="block px-3.5 py-2.5 rounded-xl text-sm font-bold text-sky-400 hover:bg-white/5">
                    <i class="fas fa-user-circle mr-2"></i> Meu Perfil
                </a>
                <a href="actions/auth_logout.php" class="block px-3.5 py-2.5 rounded-xl text-sm font-bold text-rose-400 hover:bg-rose-500/10">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sair
                </a>
            <?php else: ?>
                <a href="login.php" class="block text-center px-4 py-2.5 rounded-xl text-sm font-bold bg-white/5 text-white">
                    Entrar
                </a>
                <a href="cadastro.php" class="block text-center px-4 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-lg shadow-sky-500/30">
                    Criar Conta Gratuita
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
