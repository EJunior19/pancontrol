<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>PanControl · Iniciar sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-amber-100 via-orange-100 to-yellow-50">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">

    <!-- LOGO / TITULO -->
    <div class="text-center mb-6">
      <div class="text-4xl mb-2">🥖</div>
      <h1 class="text-2xl font-extrabold text-gray-800">PanControl</h1>
      <p class="text-sm text-gray-500">Sistema de gestión de panadería</p>
    </div>

    <!-- ERROR -->
    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 text-sm p-3 text-center">
        {{ $errors->first() }}
      </div>
    @endif

    <!-- FORM -->
    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
      @csrf

      <div>
        <label class="text-sm font-medium text-gray-700">Correo electrónico</label>
        <input
          type="email"
          name="email"
          value="{{ old('email') }}"
          required
          autofocus
          class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2
                 focus:outline-none focus:ring-2 focus:ring-amber-400"
          placeholder="admin@pancontrol.local"
        >
      </div>

      <div>
        <label class="text-sm font-medium text-gray-700">Contraseña</label>
        <input
          type="password"
          name="password"
          required
          class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2
                 focus:outline-none focus:ring-2 focus:ring-amber-400"
          placeholder="••••••••"
        >
      </div>

      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-gray-600">
          <input type="checkbox" name="remember" class="rounded border-gray-300">
          Recordarme
        </label>
      </div>

      <button
        type="submit"
        class="w-full rounded-xl bg-amber-500 hover:bg-amber-600
               text-white font-semibold py-2 transition">
        Entrar al sistema
      </button>
    </form>

    <!-- FOOTER -->
    <div class="mt-6 text-center text-xs text-gray-400">
      © {{ date('Y') }} PanControl · Uso interno
    </div>

  </div>

</body>
</html>
