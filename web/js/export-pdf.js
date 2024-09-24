$(document).on('change', 'input[type="checkbox"]', function() {
    let selected = $('input[type="checkbox"]:checked').length;
    $('#delete-multiple').prop('disabled', selected === 0);
    $('#export-pdf').prop('disabled', selected === 0);
});

$('#export-pdf').on('click', function() {
    let keys = [];
    
    $('input.item-checkbox:checked').each(function() {
        keys.push($(this).val());
    });

    if (keys.length > 0) {
        window.location.href = '/sale/export-pdf?ids=' + keys.join(','); // Redirecionar para a ação de exportação
    }
});
