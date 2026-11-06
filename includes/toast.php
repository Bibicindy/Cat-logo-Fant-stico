<?php
/**
 * Container de Toasts e Renderizador de Mensagens de Sessão
 */
$toast_pendente = obter_toast();
?>
<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4"></div>

<?php if ($toast_pendente): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Toast.show(
            <?= json_encode($toast_pendente['mensagem']) ?>, 
            <?= json_encode($toast_pendente['tipo']) ?>
        );
    });
</script>
<?php endif; ?>
