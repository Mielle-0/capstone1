<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-[#be0002] rounded-full mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Admin Portal</h1>
                <p class="text-gray-500 mt-1">Sign in to your account</p>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <!-- Username/Code Field -->
                <div>
                    <label for="usr_code" class="block text-sm font-medium text-gray-700 mb-2">
                        User Code
                    </label>
                    <input 
                        type="text" 
                        id="usr_code" 
                        name="usr_code" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#be0002] focus:border-transparent transition duration-200"
                        placeholder="Enter your user code"
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#be0002] focus:border-transparent transition duration-200"
                        placeholder="Enter your password"
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                        class="w-4 h-4 text-[#be0002] border-gray-300 rounded focus:ring-[#be0002]"
                    >
                    <label for="remember" class="ml-2 text-sm text-gray-600">
                        Remember me
                    </label>
                </div>

                <!-- Error Message (if any) -->
                @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
                @endif

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-[#be0002] text-white py-3 rounded-lg font-semibold hover:bg-[#9a0002] transition duration-200 shadow-md"
                >
                    Sign In
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Need help? Contact your system administrator</p>
            </div>
        </div>
    </div>
</body>
</html>