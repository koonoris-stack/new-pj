<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request; // ✅ เพิ่มบรรทัดนี้
use Illuminate\Support\Facades\Hash; // ✅ เพิ่มบรรทัดนี้
use App\Models\User; // ✅ เพิ่มบรรทัดนี้
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends Controller
{
    function showLoginForm(): View
    {
        return view('logins.form');
    }

    function authenticate(ServerRequestInterface $request): RedirectResponse
    {
        // Get credentials from user
        $validator = Validator::make(
            $request->getParsedBody(),
            [
                'email' => 'required',
                'password' => 'required',
            ],
        );

        // Authenticate by using method attempt()
        if (
            $validator->passes() &&
            Auth::attempt(
                $validator->safe()->only(['email', 'password']),
            )
        ) {
            // Regenerate the new session ID
            session()->regenerate();

            // Redirect to the requested URL or
            // to route products.list if does not specify
            return redirect()->intended(route('products.list'));
        }

        // If cannot authenticate redirect back to loginForm with error message
        $validator
            ->errors()
            ->add(
                'credentials',
                'The provided credentials do not match our records.',
            );

        return redirect()
            ->back()
            ->withErrors($validator);
    }

    function logout(): RedirectResponse
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    // ✅✅ เพิ่มส่วนนี้ต่อท้ายเลย ✅✅

    // แสดงหน้า Register
    public function showRegisterForm(): View
    {
        return view('register'); // ต้องมีไฟล์ resources/views/register.blade.php
    }

    // จัดการข้อมูลสมัครสมาชิก
    public function register(Request $request): RedirectResponse
    {
        // ตรวจสอบข้อมูลที่ผู้ใช้กรอก
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // สร้างผู้ใช้ใหม่
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // เข้าสู่ระบบอัตโนมัติ
        Auth::login($user);

        // กลับไปยังหน้า products.list
        return redirect()->route('products.list');
    }
}
