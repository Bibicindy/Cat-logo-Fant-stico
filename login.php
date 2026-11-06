<?php
$page_title = 'Acessar Conta - Catálogo Fantástico';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

if ($usuario_logado) {
    header('Location: index.php');
    exit;
}
?>

<div class="flex-1 flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-[#111827] border border-white/10 rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center mx-auto mb-3 text-xl">
                <i class="fas fa-lock"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Entrar no Catálogo
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Acesse suas listas salvas e sincronize seu progresso.
            </p>
        </div>

        <form action="actions/auth_login.php" method="POST" class="space-y-5" onsubmit="return validarLogin(this)">
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Usuário, E-mail ou Telefone *
                </label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="login" required placeholder="Digite seu usuário, email ou telefone" 
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider">
                        Senha de Acesso *
                    </label>
                </div>
                <div class="relative">
                    <i class="fas fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="password" name="senha" id="input-senha-login" required placeholder="••••••••" 
                           class="w-full pl-10 pr-10 py-3 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    <button type="button" onclick="alternarVisibilidadeSenha('input-senha-login')" 
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition">
                        <i class="far fa-eye text-xs"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-xl shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt text-xs"></i>
                Entrar na Conta
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-white/10 text-center text-xs text-gray-400">
            Ainda não tem conta? 
            <a href="cadastro.php" class="text-indigo-400 font-bold hover:text-indigo-300 transition">
                Cadastre-se gratuitamente
            </a>
        </div>
    </div>
</div>

<script>
function alternarVisibilidadeSenha(id) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

function validarLogin(form) {
    if (!form.login.value.trim() || !form.senha.value.trim()) {
        Modal.alert('Campos Obrigatórios', 'Por favor, preencha o campo de usuário e senha.');
        return false;
    }
    return true;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
