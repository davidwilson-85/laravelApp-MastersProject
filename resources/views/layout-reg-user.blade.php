<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<link href="https://fonts.googleapis.com/css?family=Karla&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/estilo.css') }}">
</head>
<body>

	<div class="header">
		<div class="header-contenido" id="header-titulo">
			<a href="/" id="titulo-link">Plataforma profes-alumnos</a>
		</div>
		<div class="header-contenido" id="header-autenticacion">

			<p class="header-text">Bienvenid@, {{ auth()->user()->name }}</p>

			<a class="header-button" href="{{ route('logout') }}"
               onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                {{ __('Cerrar sesión') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
		</div>
	</div>

	<div class="subheader">

		<a class="header-button" href="/areapersonal">Mi área personal</a>
		<a class="header-button" href="/calendario/p">Mi calendario</a>
		
		@if (auth()->user()->role == 'prof')
			<a class="header-button" href="/ver-usuarios">Ver alumnos registrados</a>
		@else
			<a class="header-button" href="/ver-usuarios">Ver profesores registrados</a>
		@endif
		
		<a class="header-button" href="/correo">Correo</a>
	
	</div>
	
	@yield('content')

</body>
</html>