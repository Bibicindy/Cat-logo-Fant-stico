<?php
$page_title = 'Cadastrar Nova Franquia - Catálogo Fantástico';
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
        <span class="text-indigo-400 font-medium">Nova Franquia</span>
    </div>

    <div class="bg-[#111827] border border-white/10 rounded-3xl p-6 sm:p-10 shadow-2xl relative overflow-hidden">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold mb-3">
                <i class="fas fa-plus-circle"></i> Expandir Universo
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight mb-2">
                Cadastrar Nova Franquia
            </h1>
            <p class="text-sm text-gray-400 leading-relaxed mb-8">
                Inicie uma nova lista para organizar e mapear onde assistir todos os títulos de uma saga cinematográfica.
            </p>
        </div>

        <form action="actions/add_franchise.php" method="POST" class="space-y-6">
            <!-- Nome da Franquia -->
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Nome da Franquia / Saga *
                </label>
                <input type="text" name="nome" required placeholder="Ex: Velozes & Furiosos, Piratas do Caribe, Alien..." 
                       class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <!-- Descrição -->
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Descrição ou Sinopse da Saga
                </label>
                <textarea name="descricao" rows="4" placeholder="Descreva brevemente a história central da franquia e o contexto de suas sequências..." 
                          class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"></textarea>
            </div>

            <!-- URLs de Banner e Ícone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                        URL do Banner de Capa
                    </label>
                    <input type="url" name="banner_url" placeholder="https://images.unsplash.com/..." 
                           class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    <span class="text-[11px] text-gray-500 mt-1 block">Caso deixe em branco, utilizaremos uma imagem temática padrão.</span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                        URL do Ícone (Opcional)
                    </label>
                    <input type="url" name="icone_url" placeholder="https://cdn-icons-png.flaticon.com/..." 
                           class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <!-- Visibilidade -->
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Visibilidade Inicial
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-900 border border-white/10 cursor-pointer hover:border-indigo-500 transition">
                        <input type="radio" name="visibilidade" value="publica" checked class="text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <div class="text-sm font-bold text-white flex items-center gap-1.5">
                                <i class="fas fa-globe-americas text-indigo-400"></i> Pública
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">Visível para toda a comunidade no catálogo principal.</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-900 border border-white/10 cursor-pointer hover:border-indigo-500 transition">
                        <input type="radio" name="visibilidade" value="privada" class="text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <div class="text-sm font-bold text-white flex items-center gap-1.5">
                                <i class="fas fa-lock text-rose-400"></i> Privada
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">Visível apenas por você ou convidados.</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-white/10">
                <a href="index.php" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-gray-300 font-semibold text-sm transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-xl shadow-indigo-600/30 transition flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    Criar e Iniciar Lista
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
