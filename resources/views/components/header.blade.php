<!-- Header -->
<nav class="navbar navbar-expand bg-header fixed-top d-flex flex-row justify-content-between align-items-center px-5">
    <div class="mx-5 d-flex align-items-center">
        <img src="{{ asset('images/um_logo.webp') }}" alt="Logo" height="40" class="me-1">
        <a class="navbar-brand text-white" href="#">| Feedback Portal</a>
    </div>

    <div class="px-3">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <strong><i class="fa-regular fa-circle-user"></i> {{ auth()->user()->usr_name }}</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownUser">
                <li><a class="dropdown-item" href="/settings">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
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
</script>
