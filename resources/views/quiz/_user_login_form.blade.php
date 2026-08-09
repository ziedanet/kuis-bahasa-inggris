{{-- Form login publik: siswa (individu, pakai NISN) atau kelompok (pakai username).
     Beda dari login admin yang sekarang punya halaman sendiri di /admin/login. --}}
<div class="login-wrapper" x-data="{ tab: '{{ $errors->has('username') ? 'kelompok' : 'siswa' }}' }">
    <div class="form-toggle">
        <button type="button" :class="{ active: tab === 'siswa' }" x-on:click="tab = 'siswa'">
            <i class="fa-solid fa-user-graduate"></i> Student Login
        </button>
        <button type="button" :class="{ active: tab === 'kelompok' }" x-on:click="tab = 'kelompok'">
            <i class="fa-solid fa-people-group"></i> Group Login
        </button>
    </div>

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('user.login.siswa') }}" x-show="tab === 'siswa'" x-cloak>
        @csrf
        <div class="form-group">
            <label for="user-login-nisn"><i class="fa-solid fa-id-card"></i> NISN</label>
            <input type="text" id="user-login-nisn" name="nisn" value="{{ old('nisn') }}"
                   required placeholder="National Student ID Number" autocomplete="username" class="input-cream">
        </div>
        <div class="form-group">
            <label for="user-login-siswa-password"><i class="fa-solid fa-lock"></i> Password</label>
            <input type="password" id="user-login-siswa-password" name="password"
                   required placeholder="••••••••" class="input-cream">
        </div>

        <div class="form-options">
            <label>
                <input type="checkbox" name="remember" value="1" checked> Remember Me
            </label>
        </div>

        <button type="submit" class="btn-login-submit">
            Sign In <i class="fa-solid fa-right-to-bracket"></i>
        </button>
    </form>

    <form method="POST" action="{{ route('user.login.kelompok') }}" x-show="tab === 'kelompok'" x-cloak>
        @csrf
        <div class="form-group">
            <label for="user-login-username"><i class="fa-solid fa-user"></i> Group Username</label>
            <input type="text" id="user-login-username" name="username" value="{{ old('username') }}"
                   required placeholder="Group username" autocomplete="username" class="input-cream">
        </div>
        <div class="form-group">
            <label for="user-login-kelompok-password"><i class="fa-solid fa-lock"></i> Password</label>
            <input type="password" id="user-login-kelompok-password" name="password"
                   required placeholder="••••••••" class="input-cream">
        </div>

        <div class="form-options">
            <label>
                <input type="checkbox" name="remember" value="1" checked> Remember Me
            </label>
        </div>

        <button type="submit" class="btn-login-submit">
            Sign In <i class="fa-solid fa-right-to-bracket"></i>
        </button>
    </form>

    <p style="text-align:center; font-size:0.78rem; color:var(--text-muted); margin-top:14px;">
        Don't have an account? Contact your teacher/admin to get registered.
    </p>
</div>
