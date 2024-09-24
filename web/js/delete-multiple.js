$(document).ready(function() {
    $(document).on('change', 'input.select-on-check-all', function() {
        var checked = $(this).is(':checked');
        $('input.item-checkbox').prop('checked', checked);
        $('#delete-multiple').prop('disabled', $('input.item-checkbox:checked').length === 0);
    });

    $('#delete-multiple').on('click', function() {
        let keys = [];

        $('input.item-checkbox:checked').each(function() {
            keys.push($(this).val());
        });

        if (keys.length > 0) {
            if (confirm('Você tem certeza que deseja excluir os itens selecionados?')) {
                $.post({
                    url: '/sale/delete-multiple',
                    dataType: 'json',
                    data: { keylist: keys },
                    success: function(response) {
                        if (response.success) {
                            $.pjax.reload({ container: '#w0-pjax' });
                        } else {
                            alert('Erro ao deletar os itens selecionados.');
                        }
                    }
                });
            }
        } else {
            alert('Nenhum item selecionado para exclusão.');
        }
    });
});
