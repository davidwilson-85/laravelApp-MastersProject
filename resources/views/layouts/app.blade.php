<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Plataforma profes-alumnos</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- NUEVO -->
    <link href="https://fonts.googleapis.com/css?family=Karla&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/estilo.css') }}">
</head>

<body>

    <div class="header" style="height: 85px">
        <div class="header-contenido" id="header-titulo">
            <a href="/" id="titulo-link">Plataforma profes-alumnos</a>
        </div>
        <div class="header-contenido" id="header-autenticacion">
            <a class="header-button" style="font-color: white;" href="/register">Registrarme</a>
            <a class="header-button" href="/login">Iniciar sesión</a>
        </div>
    </div>

    <main class="py-4">
        @yield('content')
    </main>

</body>
</html>
