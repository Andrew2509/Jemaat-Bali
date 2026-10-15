<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Jemaat Bali Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f5f2ec] font-sans text-ink">
    <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6 py-12">
        <div class="mb-8 text-center">
            <span class="grid h-14 w-14 place-items-center rounded-full bg-primary font-display text-xl font-extrabold text-white">JB</span>
            <h1 class="mt-4 font-serif text-2xl font-bold">Jemaat Bali</h1>
            <p class="mt-1 text-sm text-muted">Area Administrasi — Panitia Pembangunan</p>
        </div>

        <div class="rounded-2xl border border-line bg-white p-6 shadow-sm sm:p-8">
            <h2 class="font-serif text-lg font-bold">Masuk ke Akun</h2>

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-error/30 bg-error/5 p-3 text-sm text-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="email" class="label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="input" required autofocus>
                </div>
                <div>
                    <label for="password" class="label">Kata Sandi</label>
                    <input type="password" id="password" name="password" class="input" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-line">
                    Ingat saya
                </label>
                <button type="submit" class="btn btn-primary w-full !py-3">Masuk</button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-muted">
            Akses khusus panitia. <a href="{{ route('home') }}" class="font-semibold text-primary hover:underline">Kembali ke website</a>
        </p>
    </div>
</body>

</html>
