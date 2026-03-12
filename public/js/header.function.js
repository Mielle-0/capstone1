    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        document.querySelectorAll('.navbar .dropdown-item, .navbar .nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const logoutForms = document.querySelectorAll('form[action="/admin/logout"]');
        logoutForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to log out?')) {
                    e.preventDefault();
                }
            });
        });
    });