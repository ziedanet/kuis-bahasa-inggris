{{-- Partial form login admin, dipakai di halaman /admin/login maupun section login di homepage --}}
<div class="login-wrapper">
    <div class="form-toggle">
        <button class="active" type="button">Login Admin</button>
    </div>

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <div class="form-group">
            <label for="{{ $idPrefix ?? '' }}username"><i class="fa-solid fa-user"></i> Username</label>
            <input type="text" id="{{ $idPrefix ?? '' }}username" name="username" value="{{ old('username') }}"
                   required placeholder="admin" autocapitalize="off" autocomplete="username" class="input-cream">
        </div>
        <div class="form-group">
            <label for="{{ $idPrefix ?? '' }}password"><i class="fa-solid fa-lock"></i> Password</label>
            <input type="password" id="{{ $idPrefix ?? '' }}password" name="password"
                   required placeholder="••••••••" class="input-cream">
        </div>

        <div class="form-options">
            <label>
                <input type="checkbox" name="remember" value="1" checked> Ingat Saya
            </label>
        </div>

        <button type="submit" class="btn-login-submit">
            Gas Masuk <i class="fa-solid fa-right-to-bracket"></i>
        </button>
    </form>
</div>
