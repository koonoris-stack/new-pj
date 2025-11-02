<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
</head>
<body>
    <form class="product-form" action="{{ route('register.store') }}" method="post">
        @csrf

        {{-- ปุ่มปิดมุมบน (กลับไปหน้า login) --}}
        <button type="button" class="modal-close" aria-label="close" onclick="location.href='{{ route('login') }}'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>

        <h1>สมัครสมาชิก</h1>

        {{-- แสดงข้อความจาก controller ถ้ามี --}}
        @if(session('status'))
            <div class="app-cmp-notifications">
                {{ session('status') }}
            </div>
        @endif

        {{-- email --}}
        <div class="inputbox">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="อีเมล" required value="{{ old('email') }}" />
        </div>

        {{-- password --}}
        <div class="inputbox">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="รหัสผ่าน (ขั้นต่ำ 4 ตัวอักษร)" required minlength="4" />
            <button type="button" class="eye-btn" onclick="togglePassword('password')" aria-label="Toggle password visibility">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
            </button>
        </div>

        {{-- confirm password --}}
        <div class="inputbox">
            <label for="password_confirmation">Confirm</label>
            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="ยืนยันรหัสผ่าน" required minlength="4" />
            <button type="button" class="eye-btn" onclick="togglePassword('password_confirmation')" aria-label="Toggle confirm password visibility">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
            </button>
        </div>

        <button class="primary-btn" type="submit">สมัครสมาชิก</button>

        <div class="helper">
            <small>มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></small>
        </div>

        <div class="app-cmp-notifications">
            @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </form>

    <script>
        function togglePassword(id){
            const el = document.getElementById(id);
            if (!el) return;
            el.type = el.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
