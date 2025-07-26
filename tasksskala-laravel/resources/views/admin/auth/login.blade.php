<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login - SkalaCode</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .login-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body class="login-gradient min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 glass-effect rounded-3xl flex items-center justify-center mx-auto mb-4 group hover:scale-110 transition-transform duration-300">
                <i class="fas fa-crown text-3xl text-yellow-300"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">SkalaCode</h1>
            <p class="text-purple-200">Painel Administrativo</p>
        </div>

        <!-- Login Form -->
        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">Bem-vindo de volta!</h2>
                <p class="text-purple-200">Entre com suas credenciais para acessar o painel</p>
            </div>

            @if(session('message'))
                <div class="bg-green-500/20 border border-green-400 text-green-100 px-4 py-3 rounded-lg mb-6">
                    {{ session('message') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/20 border border-red-400 text-red-100 px-4 py-3 rounded-lg mb-6">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-purple-200 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}" 
                           required
                           placeholder="admin@skalacode.com.br"
                           class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-purple-300 input-focus transition-all duration-300 @error('email') border-red-400 @enderror">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-purple-200 mb-2">
                        <i class="fas fa-lock mr-2"></i>Senha
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-purple-300 input-focus transition-all duration-300 @error('password') border-red-400 @enderror">
                </div>

                <!-- Login Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-yellow-400 to-orange-400 text-white font-bold py-3 px-4 rounded-lg hover:from-yellow-500 hover:to-orange-500 transform hover:scale-105 transition-all duration-300 shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Entrar no Painel
                </button>
            </form>

            <!-- Credentials Info -->
            <div class="mt-8 text-center">
                <div class="glass-effect rounded-lg p-4">
                    <p class="text-sm text-purple-200 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Credenciais de Acesso:
                    </p>
                   
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-purple-300 text-sm">
                © {{ date('Y') }} SkalaCode. Todos os direitos reservados.
            </p>
        </div>
    </div>

    <script>
        // Auto-fill demo credentials (for demo purposes)
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            // Add click to fill demo credentials
           
        });
    </script>
</body>
</html>