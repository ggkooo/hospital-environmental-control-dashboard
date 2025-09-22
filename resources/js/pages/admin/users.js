function openEditUserModal(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserName').value = user.name;
    document.getElementById('editUserEmail').value = user.email;
    document.getElementById('editUserPassword').value = '';
    document.getElementById('editUserPasswordConfirmation').value = '';
    document.getElementById('editUserForm').action = '/admin/users/' + user.id;
    var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}

let deleteFormToSubmit = null;
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-edit-user-modal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const user = JSON.parse(this.getAttribute('data-user'));
            openEditUserModal(user);
        });
    });

    document.querySelectorAll('.btn-delete-user').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            deleteFormToSubmit = this.closest('form');
            var modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        });
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteFormToSubmit) {
            deleteFormToSubmit.submit();
        }
    });

    document.getElementById('editUserForm').onsubmit = function(e) {
        this.action = '/admin/users/' + document.getElementById('editUserId').value;
    };
});
