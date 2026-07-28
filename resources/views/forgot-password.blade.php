<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Staff Portal</title>
    
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
                    <img src="{{ asset('images/um_logo.webp') }}" alt="Portal Logo" class="img-fluid mb-3" style="max-height: 80px;">
                    <h4 class="fw-bold text-dark mb-1">Forgot Password</h4>
                </div>

                <form action="{{ route('password.send-email') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        {{-- 💡 Changed name and id to usr_email to match your database --}}
                        <label for="usr_email" class="form-label small fw-bold text-secondary">Email Address</label>
                        <input 
                            type="email" 
                            id="usr_email" 
                            name="usr_email" 
                            value="{{ old('usr_email') }}"
                            required
                            class="form-control form-control-lg fs-6 bg-light"
                            placeholder="user@example.com"
                        >
                    </div>

                    {{-- 💡 Success Message --}}
                    @if(session('success'))
                    <div class="alert alert-success small py-2 px-3 mb-4 rounded text-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    {{-- Error Message --}}
                    @if(session('error'))
                    <div class="alert alert-danger small py-2 px-3 mb-4 rounded text-danger" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-maroon btn-lg fw-bold shadow-sm py-2">
                            Send Email
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="small text-decoration-none text-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>