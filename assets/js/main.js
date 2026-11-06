/**
 * Catálogo Fantástico - JavaScript Principal
 * Tema: Quarteto Fantástico (Azul Cósmico & Laranja Flamejante)
 * Gerenciador de Toasts, Modais, Checklist Visual e Redirecionamento de Streaming.
 */

// ==========================================
// 1. SISTEMA DE TOASTS (Notificações)
// ==========================================
const Toast = {
    container: null,

    init() {
        this.container = document.getElementById('toast-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'sucesso', duration = 3500) {
        this.init();

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto flex items-center gap-3 p-4 rounded-2xl shadow-2xl text-sm font-semibold transition-all duration-300 transform translate-y-4 opacity-0 border backdrop-blur-xl';

        let icon = '';
        let colorClasses = '';

        switch (type) {
            case 'sucesso':
                colorClasses = 'bg-slate-900/95 text-sky-200 border-sky-400/50 shadow-sky-500/25';
                icon = '<div class="w-7 h-7 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-sm"><i class="fas fa-check"></i></div>';
                break;
            case 'erro':
                colorClasses = 'bg-slate-900/95 text-rose-200 border-rose-500/50 shadow-rose-500/25';
                icon = '<div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-sm"><i class="fas fa-exclamation-triangle"></i></div>';
                break;
            case 'aviso':
                colorClasses = 'bg-slate-900/95 text-amber-200 border-amber-500/50 shadow-amber-500/25';
                icon = '<div class="w-7 h-7 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm"><i class="fas fa-bell"></i></div>';
                break;
            default: // info
                colorClasses = 'bg-slate-900/95 text-orange-200 border-orange-500/50 shadow-orange-500/25';
                icon = '<div class="w-7 h-7 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center font-bold text-sm"><i class="fas fa-meteor"></i></div>';
        }

        toast.className += ' ' + colorClasses;
        toast.innerHTML = `
            <div class="flex-shrink-0">${icon}</div>
            <div class="flex-1 leading-snug">${message}</div>
            <button type="button" class="text-white/60 hover:text-white transition p-1" onclick="this.parentElement.remove()">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;

        this.container.appendChild(toast);

        // Animação de entrada
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        });

        // Remoção automática
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
};

// ==========================================
// 2. SISTEMA DE MODAL UNIVERSAL
// Substitui alertas e confirmações nativas
// ==========================================
const Modal = {
    modalEl: null,
    backdropEl: null,
    titleEl: null,
    bodyEl: null,
    footerEl: null,
    iconEl: null,
    currentResolve: null,

    init() {
        this.modalEl = document.getElementById('app-modal');
        this.backdropEl = document.getElementById('app-modal-backdrop');
        this.titleEl = document.getElementById('app-modal-title');
        this.bodyEl = document.getElementById('app-modal-body');
        this.footerEl = document.getElementById('app-modal-footer');
        this.iconEl = document.getElementById('app-modal-icon');

        if (this.backdropEl) {
            this.backdropEl.addEventListener('click', (e) => {
                if (e.target === this.backdropEl) {
                    this.close(false);
                }
            });
        }
    },

    open({ title = 'Aviso', content = '', icon = 'info', buttons = [] }) {
        this.init();
        if (!this.modalEl) return;

        this.titleEl.textContent = title;
        this.bodyEl.innerHTML = content;

        let iconHtml = '<div class="emblema-f4 w-10 h-10 text-base">4</div>';
        if (icon === 'danger' || icon === 'warning') {
            iconHtml = '<div class="w-10 h-10 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-lg"><i class="fas fa-exclamation-triangle"></i></div>';
        } else if (icon === 'success') {
            iconHtml = '<div class="w-10 h-10 rounded-2xl bg-sky-500/20 border border-sky-500/40 text-sky-400 flex items-center justify-center text-lg"><i class="fas fa-check"></i></div>';
        }
        if (this.iconEl) this.iconEl.innerHTML = iconHtml;

        this.footerEl.innerHTML = '';
        buttons.forEach(btn => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = btn.className || 'px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-sm transition shadow-lg shadow-sky-600/30';
            button.textContent = btn.text;
            button.onclick = () => {
                if (typeof btn.action === 'function') {
                    btn.action();
                }
                this.close(btn.value !== undefined ? btn.value : true);
            };
            this.footerEl.appendChild(button);
        });

        this.backdropEl.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    },

    close(val = false) {
        if (!this.backdropEl) return;
        this.backdropEl.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        if (this.currentResolve) {
            this.currentResolve(val);
            this.currentResolve = null;
        }
    },

    alert(title, message) {
        return new Promise(resolve => {
            this.currentResolve = resolve;
            this.open({
                title: title || 'Atenção Fantástica',
                content: `<p class="text-slate-300 text-sm leading-relaxed">${message}</p>`,
                icon: 'info',
                buttons: [
                    {
                        text: 'Entendido',
                        className: 'w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-bold text-sm transition shadow-lg shadow-sky-500/30',
                        action: () => resolve(true)
                    }
                ]
            });
        });
    },

    confirm(title, message) {
        return new Promise(resolve => {
            this.currentResolve = resolve;
            this.open({
                title: title || 'Confirmação',
                content: `<p class="text-slate-300 text-sm leading-relaxed">${message}</p>`,
                icon: 'warning',
                buttons: [
                    {
                        text: 'Cancelar',
                        className: 'px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm transition border border-white/5',
                        action: () => resolve(false)
                    },
                    {
                        text: 'Confirmar',
                        className: 'px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-rose-600 hover:from-orange-400 hover:to-rose-500 text-white font-bold text-sm transition shadow-lg shadow-orange-500/30',
                        action: () => resolve(true)
                    }
                ]
            });
        });
    }
};

window.nativeAlert = window.alert;
window.nativeConfirm = window.confirm;
window.alert = function(msg) {
    Modal.alert('Catálogo Fantástico', msg);
};
window.confirm = function(msg) {
    return window.nativeConfirm(msg);
};

// ==========================================
// 3. SINCRONIZAR PROGRESSO COM SÍMBOLO VISUAL
// ==========================================
async function toggleFilmeProgresso(checkboxEl, filmeId, franquiaId) {
    const isChecked = checkboxEl.checked;
    const cardEl = document.getElementById(`filme-card-${filmeId}`);
    
    // Atualização do Símbolo Visual do Checkbox
    const visualBox = cardEl ? cardEl.querySelector('.checkbox-visual') : null;
    const checkIcon = visualBox ? visualBox.querySelector('i') : null;

    if (visualBox && checkIcon) {
        if (isChecked) {
            visualBox.className = 'checkbox-visual w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 shadow-md bg-gradient-to-tr from-sky-500 to-cyan-400 text-slate-950 font-black shadow-cyan-500/50 scale-105 ring-2 ring-cyan-300';
            checkIcon.className = 'fas fa-check text-sm font-black opacity-100 scale-100 transition-all duration-200';
            if (cardEl) cardEl.classList.add('ring-2', 'ring-sky-500/60', 'bg-sky-950/20');
        } else {
            visualBox.className = 'checkbox-visual w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 shadow-md bg-slate-900 border-2 border-slate-700 text-transparent hover:border-sky-400';
            checkIcon.className = 'fas fa-check text-sm font-black opacity-0 scale-50 transition-all duration-200';
            if (cardEl) cardEl.classList.remove('ring-2', 'ring-sky-500/60', 'bg-sky-950/20');
        }
    }

    try {
        const formData = new FormData();
        formData.append('filme_id', filmeId);
        formData.append('franquia_id', franquiaId);
        formData.append('assistido', isChecked ? '1' : '0');

        const response = await fetch('actions/toggle_progress.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.sucesso) {
            const barra = document.getElementById('progresso-barra');
            const percentualTexto = document.getElementById('progresso-percentual');
            const contadorTexto = document.getElementById('progresso-contador');

            if (barra) barra.style.width = `${data.porcentagem}%`;
            if (percentualTexto) percentualTexto.textContent = `${data.porcentagem}%`;
            if (contadorTexto) contadorTexto.textContent = `${data.assistidos} de ${data.total} concluídos`;

            Toast.show(data.mensagem, 'sucesso', 2500);
        } else {
            // Reverter estado
            checkboxEl.checked = !isChecked;
            if (visualBox && checkIcon) {
                visualBox.className = !isChecked ? 'checkbox-visual w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 shadow-md bg-gradient-to-tr from-sky-500 to-cyan-400 text-slate-950 font-black' : 'checkbox-visual w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 shadow-md bg-slate-900 border-2 border-slate-700 text-transparent';
                checkIcon.className = !isChecked ? 'fas fa-check text-sm font-black opacity-100 scale-100' : 'fas fa-check text-sm font-black opacity-0 scale-50';
            }
            Toast.show(data.erro || 'Não foi possível atualizar o progresso.', 'erro');
        }
    } catch (err) {
        console.error(err);
        checkboxEl.checked = !isChecked;
        Toast.show('Falha na conexão ao sincronizar progresso.', 'erro');
    }
}

// ==========================================
// 4. FILTRAR FILMES POR STREAMING OU BUSCA
// ==========================================
function filtrarFilmes(termoBusca = '', streaming = 'todos') {
    const cards = document.querySelectorAll('.filme-item');
    termoBusca = termoBusca.toLowerCase().trim();

    cards.forEach(card => {
        const titulo = (card.getAttribute('data-titulo') || '').toLowerCase();
        const cardStreaming = (card.getAttribute('data-streaming') || '').toLowerCase();

        const bateuBusca = !termoBusca || titulo.includes(termoBusca);
        const bateuStreaming = streaming === 'todos' || cardStreaming.includes(streaming.toLowerCase());

        if (bateuBusca && bateuStreaming) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// ==========================================
// 5. TROCA DE ROTA (Cronológica / Lançamento / Personalizada)
// ==========================================
function trocarRota(rotaNome) {
    const tabs = document.querySelectorAll('.tab-rota');
    tabs.forEach(tab => {
        if (tab.getAttribute('data-rota') === rotaNome) {
            tab.className = 'tab-rota px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-lg shadow-sky-500/30 transition flex items-center gap-2';
        } else {
            tab.className = 'tab-rota px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2';
        }
    });

    const listaEl = document.getElementById('lista-filmes');
    if (!listaEl) return;

    const cards = Array.from(listaEl.querySelectorAll('.filme-item'));

    cards.sort((a, b) => {
        let valA, valB;
        if (rotaNome === 'cronologica') {
            valA = parseInt(a.getAttribute('data-cronologico') || '999');
            valB = parseInt(b.getAttribute('data-cronologico') || '999');
        } else if (rotaNome === 'lancamento') {
            valA = parseInt(a.getAttribute('data-lancamento') || '999');
            valB = parseInt(b.getAttribute('data-lancamento') || '999');
        } else {
            valA = parseInt(a.getAttribute('data-personalizado') || a.getAttribute('data-cronologico') || '999');
            valB = parseInt(b.getAttribute('data-personalizado') || b.getAttribute('data-cronologico') || '999');
        }
        return valA - valB;
    });

    listaEl.style.opacity = '0.5';
    setTimeout(() => {
        cards.forEach((card, index) => {
            const badgeNumero = card.querySelector('.badge-ordem-numero');
            if (badgeNumero) {
                badgeNumero.textContent = `#${index + 1}`;
            }
            listaEl.appendChild(card);
        });
        listaEl.style.opacity = '1';
        Toast.show(`Lista organizada: ${rotaNome === 'cronologica' ? 'Ordem Cronológica (Enredo)' : (rotaNome === 'lancamento' ? 'Ordem de Lançamento (Cinema)' : 'Ordem Personalizada')}`, 'info', 2000);
    }, 150);
}

// ==========================================
// 6. MOVER FILMES (Subir / Descer e Salvar)
// ==========================================
async function moverFilme(filmeId, direcao, franquiaId) {
    const card = document.getElementById(`filme-card-${filmeId}`);
    if (!card) return;

    const parent = card.parentNode;
    if (direcao === 'cima') {
        const prev = card.previousElementSibling;
        if (prev) {
            parent.insertBefore(card, prev);
            atualizarNumeracaoEIndices();
            await salvarOrdemServidor(franquiaId);
        }
    } else if (direcao === 'baixo') {
        const next = card.nextElementSibling;
        if (next) {
            parent.insertBefore(next, card);
            atualizarNumeracaoEIndices();
            await salvarOrdemServidor(franquiaId);
        }
    }
}

function atualizarNumeracaoEIndices() {
    const cards = document.querySelectorAll('.filme-item');
    cards.forEach((c, index) => {
        c.setAttribute('data-personalizado', index + 1);
        const badgeNumero = c.querySelector('.badge-ordem-numero');
        if (badgeNumero) badgeNumero.textContent = `#${index + 1}`;
    });
}

async function salvarOrdemServidor(franquiaId) {
    const cards = document.querySelectorAll('.filme-item');
    const ordem = [];
    cards.forEach((c, idx) => {
        ordem.push({
            filme_id: c.getAttribute('data-id'),
            posicao: idx + 1
        });
    });

    try {
        const res = await fetch('actions/save_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                franquia_id: franquiaId,
                ordem: ordem
            })
        });
        const data = await res.json();
        if (data.sucesso) {
            Toast.show('Nova ordem salva com sucesso!', 'sucesso', 2000);
        } else {
            Toast.show(data.erro || 'Erro ao salvar a ordem.', 'erro');
        }
    } catch (e) {
        console.error(e);
        Toast.show('Falha ao comunicar com o servidor para salvar ordem.', 'erro');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    Modal.init();
    Toast.init();

    const btnMobile = document.getElementById('mobile-menu-btn');
    const menuMobile = document.getElementById('mobile-menu');
    if (btnMobile && menuMobile) {
        btnMobile.addEventListener('click', () => {
            menuMobile.classList.toggle('hidden');
        });
    }

    const campoBusca = document.getElementById('input-busca-filmes');
    if (campoBusca) {
        campoBusca.addEventListener('input', (e) => {
            filtrarFilmes(e.target.value);
        });
    }
});
