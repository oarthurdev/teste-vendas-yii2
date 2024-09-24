var deleteUrl;
var userName;

function confirmDelete(url, name) {
    deleteUrl = url;
    userName = name;
    $('#confirmDeleteModal .user-name').text(userName);
    $('#confirmDeleteModal').modal('show');
}

$('#confirmDeleteBtn').on('click', function() {
    $.post(deleteUrl, function(response) {
        $.pjax.reload({container: '#user-grid'});
        $('#confirmDeleteModal').modal('hide');
    });
});
