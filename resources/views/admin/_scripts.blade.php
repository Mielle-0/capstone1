<script>
// Logic for User Editing
function editUser(user) {
    const form = document.getElementById('editUserForm');
    form.action = "{{ url('admin/manage-users') }}/" + user.usr_id;
    
    document.getElementById('edit_usr_name').value = user.usr_name;
    document.getElementById('edit_usr_code').value = user.usr_code;
    
    document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);
    
    if(user.roles) {
        user.roles.forEach(role => {
            let checkbox = document.getElementById('edit_role_' + role.rol_id);
            if(checkbox) checkbox.checked = true;
        });
    }

    let modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}

// Logic for Department Editing
function editDepartment(dept) {
    const form = document.getElementById('editDeptForm');
    form.action = "{{ url('admin/manage-departments') }}/" + dept.dep_id;
    
    document.getElementById('edit_dep_name').value = dept.dep_name;
    document.getElementById('edit_dep_full_name').value = dept.dep_full_name;
    document.getElementById('edit_dep_active').value = dept.dep_active;
    
    let modal = new bootstrap.Modal(document.getElementById('editDeptModal'));
    modal.show();
}

// Logic for Assigning Users to Departments
function openAssignModal(id, name) {
    document.getElementById('inputDeptId').value = id;
    document.getElementById('displayDeptName').innerText = name;
    let modal = new bootstrap.Modal(document.getElementById('assignUserModal'));
    modal.show();
}
</script>