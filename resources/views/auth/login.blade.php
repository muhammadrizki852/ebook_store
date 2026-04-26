@extends('layouts.app')

@section('title', 'Login')
@section('hide_newsletter', true)
@section('hide_navbar', true)

@section('content')
<div style="min-height: calc(100vh - 200px); background: #f9fafb; display: flex; align-items: center; padding: 48px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div style="background:#fff; border-radius:24px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.1); min-height:520px;">
                    <div class="col-12" style="padding:48px 44px;">
                        <div class="text-center mb-5">
                            <div style="width:60px; height:60px; background:#fff7ed; border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                                <i class="bi bi-person-circle" style="font-size:1.8rem; color:#f97316;"></i>
                            </div>
                            <h3 style="font-family:'Playfair Display',serif; font-weight:700; margin-bottom:4px; color:#1a1a2e;">Sign In</h3>
                            <p style="color:#6b7280; font-size:0.9rem; margin-bottom:0;">Enter your credentials to continue</p>
                        </div>

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="role" style="font-weight:700; font-size:0.85rem; color:#374151; display:block; margin-bottom:8px;">Login Sebagai</label>
                                <div style="position:relative;">
                                    <i class="bi bi-shield-lock" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#9ca3af;"></i>
                                    <select id="role" name="role"
                                        class="@error('role') is-invalid @enderror"
                                        style="width:100%; padding:13px 16px 13px 44px; border:1.5px solid #e5e7eb; border-radius:12px; font-size:0.9rem; outline:none; transition:border .2s; background:#f9fafb; appearance:none;"
                                        onfocus="this.style.borderColor='#f97316';this.style.background='#fff'"
                                        onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                                        <option value="user" @selected(old('role', 'user') === 'user')>User</option>
                                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" style="font-weight:700; font-size:0.85rem; color:#374151; display:block; margin-bottom:8px;">Email Address</label>
                                <div style="position:relative;">
                                    <i class="bi bi-envelope" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#9ca3af;"></i>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        placeholder="you@example.com" autofocus required
                                        class="@error('email') is-invalid @enderror"
                                        style="width:100%; padding:13px 16px 13px 44px; border:1.5px solid #e5e7eb; border-radius:12px; font-size:0.9rem; outline:none; transition:border .2s; background:#f9fafb;"
                                        onfocus="this.style.borderColor='#f97316';this.style.background='#fff'"
                                        onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" style="font-weight:700; font-size:0.85rem; color:#374151; display:block; margin-bottom:8px;">Password</label>
                                <div style="position:relative;">
                                    <i class="bi bi-lock" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#9ca3af;"></i>
                                    <input type="password" id="password" name="password"
                                        placeholder="••••••••" required
                                        class="@error('password') is-invalid @enderror"
                                        style="width:100%; padding:13px 16px 13px 44px; border:1.5px solid #e5e7eb; border-radius:12px; font-size:0.9rem; outline:none; transition:border .2s; background:#f9fafb;"
                                        onfocus="this.style.borderColor='#f97316';this.style.background='#fff'"
                                        onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-5">
                                <input type="checkbox" id="remember" name="remember" style="accent-color:#f97316; width:16px; height:16px;">
                                <label for="remember" style="margin-left:8px; font-size:0.85rem; color:#6b7280; cursor:pointer;">Remember me</label>
                            </div>

                            <button type="submit"
                                style="width:100%; background:#f97316; color:#fff; border:none; border-radius:40px; padding:14px; font-weight:700; font-size:1rem; transition:background .2s; cursor:pointer;"
                                onmouseover="this.style.background='#ea6a0a'"
                                onmouseout="this.style.background='#f97316'">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
                        </form>
                        <!-- TOMBOL LOGIN GOOGLE -->
                        <div style="margin-top: 24px;">
                            <div style="position: relative; text-align: center; margin-bottom: 16px;">
                                <div style="position: absolute; inset: 0; display: flex; align-items: center;">
                                    <div style="width: 100%; border-top: 1.5px solid #e5e7eb;"></div>
                                </div>
                                <div style="position: relative; display: inline-block; background: #fff; padding: 0 12px;">
                                    <span style="font-size: 0.85rem; color: #6b7280;">Or continue with</span>
                                </div>
                            </div>

                            <a href="{{ route('auth.google') }}"
                               style="width:100%; display:inline-flex; justify-content:center; align-items:center; padding:12px 16px; border:1.5px solid #e5e7eb; border-radius:12px; background:#fff; color:#374151; font-weight:600; font-size:0.9rem; text-decoration:none; transition:all .2s;"
                               onmouseover="this.style.background='#f9fafb'"
                               onmouseout="this.style.background='#fff'">
                                <svg style="width:20px; height:20px; margin-right:8px;" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Login with Google
                            </a>
                        </div>
                        <!-- END TOMBOL GOOGLE -->
                        <div style="background:#fff7ed; border-radius:12px; padding:14px 16px; margin-top:18px; font-size:0.82rem; color:#9a3412; border:1px solid #fed7aa;">
                            <strong style="color:#7c2d12;">Login sekarang memakai role + verifikasi 2 langkah.</strong><br>
                            Pilih portal login yang sesuai, lalu setelah kredensial benar sistem akan mengirim kode OTP 6 digit ke email Anda.
                        </div>

                        <div style="text-align:center; margin-top:28px;">
                            <p style="color:#6b7280; font-size:0.88rem; margin-bottom:0;">
                                Don't have an account?
                                <a href="{{ route('register') }}" style="color:#f97316; font-weight:700; text-decoration:none;">Create one free</a>
                            </p>
                        </div>

                        <div style="background:#f9fafb; border-radius:12px; padding:16px; margin-top:24px; font-size:0.78rem; color:#9ca3af; border:1px solid #e5e7eb;">
                            <strong style="color:#374151;">Demo accounts:</strong><br>
                            Admin: admin@ebook.com / admin123<br>
                            User: user@ebook.com / user1234
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
