<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Presensi | RS Permata Hati')</title>
    <link rel="icon" type="image/png" href="{{ asset('public/logo/logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="login-card">
        <!-- Logo Compact Layout -->
        <div class="logo-compact">
            <img src="{{ asset('public/logo/logo.png') }}" alt="Logo RS Permata Hati" class="logo-img">
            <div class="divider"></div>
            <div class="text-group">
                <span class="app-name">Presensi</span>
                <span class="rs-name">Rumah Sakit Permata Hati</span>
            </div>
        </div>

        {{-- Alert Error --}}
        @if(Session::get('login_error_message'))
        <div class="alert-error">
            <i class="bi bi-exclamation-circle"></i>
            <span>{{ Session::pull('login_error_message') }}</span>
        </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ url('process-login') }}">
            @csrf
            
            <div class="form-group">
                <label for="username">ID Pegawai</label>
                <div class="input-field">
                    <i class="bi bi-person"></i>
                    <input 
                        type="text" 
                        id="username" 
                        name="id_user" 
                        placeholder="Masukkan ID Pegawai"
                        autocomplete="off"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-field">
                    <i class="bi bi-lock"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Masukkan Password"
                        required
                    >
                    <button type="button" class="toggle-pass" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} RS Permata Hati</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>
</html>