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
			<a class="header-button" href="/register">Registrarme</a>
			<a class="header-button" href="/login">Iniciar sesión</a>
		</div>
	</div>
	
	@yield('content')

</body>
</html>