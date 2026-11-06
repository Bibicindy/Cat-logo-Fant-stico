<!-- Modal Universal do Sistema (Substitui alerts e confirms nativos) -->
<div id="app-modal-backdrop" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 hidden animate-fade-in">
    <div id="app-modal" class="bg-[#111827] border border-white/10 rounded-2xl max-w-md w-full overflow-hidden shadow-2xl transform transition-all animate-slide-down">
        <!-- Cabeçalho -->
        <div class="p-6 pb-4 flex items-start gap-4">
            <div id="app-modal-icon" class="p-3 rounded-xl bg-white/5 border border-white/5 flex-shrink-0">
                <i class="fas fa-info-circle text-indigo-400 text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 id="app-modal-title" class="text-lg font-bold text-white tracking-tight">
                    Título do Modal
                </h3>
                <div id="app-modal-body" class="mt-2 text-sm text-gray-300 leading-relaxed">
                    Mensagem informativa...
                </div>
            </div>
        </div>

        <!-- Rodapé com Ações -->
        <div id="app-modal-footer" class="p-4 bg-white/[0.02] border-t border-white/5 flex flex-wrap justify-end gap-2.5">
            <button type="button" onclick="Modal.close(false)" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-gray-300 font-medium text-sm transition">
                Fechar
            </button>
        </div>
    </div>
</div>
