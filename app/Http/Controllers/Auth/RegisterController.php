<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    // แสดงฟอร์มสมัคร
    public function show()
    {
        return view('register');
    }

    // ประมวลผลการสมัคร
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'password' => ['required','confirmed','min:4'],
        ]);

        $user = User::create([
            'name' => '',
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'USER',
        ]);

        // ไม่ล็อกอินอัตโนมัติ ให้กลับไปหน้า login แทน พร้อมข้อความแจ้ง
        session()->flash('status', 'Registration successful. Please login.');

        return redirect()->route('login');
    }
}
