<?php
$page_title = 'Criar Conta - Catálogo Fantástico';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

if ($usuario_logado) {
    header('Location: index.php');
    exit;
}
?>

<div class="flex-1 flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-[#111827] border border-white/10 rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 flex items-center justify-center mx-auto mb-3 text-xl">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Criar Conta Gratuita
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Salve suas listas, sincronize o checklist de filmes e organize suas maratonas.
            </p>
        </div>

        <form action="actions/auth_register.php" method="POST" class="space-y-4" onsubmit="return validarCadastro(this)">
            <!-- Nome Completo -->
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                    Nome Completo *
                </label>
                <input type="text" name="nome" required placeholder="Ex: Lucas Silva" 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 transition">
            </div>

            <!-- Nome de Usuário -->
            <div>
                <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                    Nome de Usuário (Username) *
                </label>
                <input type="text" name="usuario" required placeholder="Ex: lucascinefilo" 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 transition">
            </div>

            <!-- E-mail e Telefone (Conforme requisito: email ou telefone) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        E-mail
                    </label>
                    <input type="email" name="email" id="cad-email" placeholder="seu@email.com" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Telefone (WhatsApp)
                    </label>
                    <input type="tel" name="telefone" id="cad-tel" placeholder="(11) 99999-9999" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 transition">
                </div>
            </div>
            <span class="text-[11px] text-gray-400 block -mt-2">Informe ao menos um e-mail ou telefone.</span>

            <!-- Senhas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Senha (Min. 6) *
                    </label>
                    <input type="password" name="senha" id="cad-senha" required placeholder="••••••••" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Confirmar Senha *
                    </label>
                    <input type="password" name="confirmar_senha" id="cad-confirma" required placeholder="••••••••" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- Termos LGPD e Consentimento (Regra CORE de Privacidade) -->
            <div class="pt-2">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="consentimento" required checked class="mt-1 rounded bg-slate-900 border-white/20 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs text-gray-400 leading-relaxed">
                        Concordo com os <a href="privacidade.php" target="_blank" class="text-indigo-400 underline hover:text-white">Termos de Privacidade e LGPD</a>, autorizando a coleta exclusiva dos dados para autenticação e sincronização de listas.
                    </span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-xl shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                <i class="fas fa-user-check text-xs"></i>
                Cadastrar e Salvar Listas
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-white/10 text-center text-xs text-gray-400">
            Já possui uma conta? 
            <a href="login.php" class="text-indigo-400 font-bold hover:text-indigo-300 transition">
                Fazer login
            </a>
        </div>
    </div>
</div>

<script>
function validarCadastro(form) {
    const nome = form.nome.value.trim();
    const usuario = form.usuario.value.trim();
    const email = document.getElementById('cad-email').value.trim();
    const tel = document.getElementById('cad-tel').value.trim();
    const senha = document.getElementById('cad-senha').value;
    const confirma = document.getElementById('cad-confirma').value;

    if (!nome || !usuario || !senha) {
        Modal.alert('Campos Obrigatórios', 'Preencha todos os campos obrigatórios.');
        return false;
    }

    if (!email && !tel) {
        Modal.alert('Contato Necessário', 'Por favor, informe ao menos um e-mail ou telefone para cadastro.');
        return false;
    }

    if (senha.length < 6) {
        Modal.alert('Senha Curta', 'A senha de acesso deve ter pelo menos 6 caracteres.');
        return false;
    }

    if (senha !== confirma) {
        Modal.alert('Senhas Divergentes', 'A confirmação de senha não coincide com a senha digitada.');
        return false;
    }

    return true;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
