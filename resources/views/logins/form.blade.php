<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
</head>

<body>
    <form class="product-form" action="{{ route('authenticate') }}" method="post">
        @csrf

        {{-- ปุ่มปิดมุมบน --}}
        <button type="button" class="modal-close" aria-label="close" onclick="location.href='/'">
            <!-- simple X icon -->
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>

        <h1>เข้าสู่ระบบ</h1>

        {{-- email --}}
        <div class="inputbox">
            {{-- label hidden for accessibility --}}
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" placeholder="อีเมล" required value="{{ old('email') }}" />
        </div>

        {{-- password with eye --}}
        <div class="inputbox">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="รหัสผ่าน" required />
            <button type="button" class="eye-btn" onclick="togglePassword()"
                aria-label="Toggle password visibility">
                <!-- eye icon (will not change SVG here, JS toggles type only) -->
                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
            </button>
        </div>

        <button class="primary-btn" type="submit">เข้าสู่ระบบ</button>

        <div class="helper">
            <small>ยังไม่มีบัญชี? <a href="{{ route('auth.register') }}">สมัครสมาชิก</a></small>
        </div>

        <div class="app-cmp-notifications">
            @error('credentials')
                <div role="alert">{{ $message }}</div>
            @enderror
            @if(session('status'))
                <div>{{ session('status') }}</div>
            @endif
        </div>
    </form>

    {{-- JS เล็กๆ สำหรับสลับ password visibility --}}
    <script>
        function togglePassword(){
            const pwd = document.getElementById('password');
            if (!pwd) return;
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>

</html>

