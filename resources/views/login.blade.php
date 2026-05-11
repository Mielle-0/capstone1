<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Portal</title>
    
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        .bg-maroon { background-color: #be0002 !important; }
        .text-maroon { color: #be0002 !important; }
        
        .btn-maroon {
            background-color: #be0002;
            color: #fff;
            border-color: #be0002;
        }
        .btn-maroon:hover {
            background-color: #9a0002;
            color: #fff;
            border-color: #9a0002;
        }
        
        /* Apply custom glow to focused inputs */
        .form-control:focus {
            border-color: #be0002;
            box-shadow: 0 0 0 0.25rem rgba(190, 0, 2, 0.25);
        }
        
        /* Apply custom color to checked checkbox */
        .form-check-input:checked {
            background-color: #be0002;
            border-color: #be0002;
        }
    </style>
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center p-3">
    
    <div class="w-100" style="max-width: 30vw;">
        <div class="card border-0 shadow-lg rounded-3">
            <div class="card-body p-4 p-md-5">
                
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-maroon rounded-circle mb-3" style="width: 5rem; height: 5rem;">
                        <svg class="text-white" style="width: 3rem; height: 3rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Staff Portal</h4>
                </div>

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="usr_code" class="form-label small fw-bold text-secondary">User Code</label>
                        <input 
                            type="text" 
                            id="usr_code" 
                            name="usr_code" 
                            value="{{ old('usr_code') }}"
                            required
                            class="form-control form-control-lg fs-6 bg-light"
                            placeholder="Enter your user code"
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold text-secondary">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="form-control form-control-lg fs-6 bg-light"
                            placeholder="Enter your password"
                        >
                    </div>

                    @if(session('error'))
                    <div class="alert alert-danger small py-2 px-3 mb-4 rounded text-danger" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-maroon btn-lg fw-bold shadow-sm py-2">
                            Sign In
                        </button>
                    </div>
                </form>

                <div class="mt-2 pt-2 text-center text-muted small">
                    <p class="mb-0">Need help? Contact your system administrator</p>
                </div>
                
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>