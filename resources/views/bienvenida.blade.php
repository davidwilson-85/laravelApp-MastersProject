@extends('layout-guest')

@section('title', 'Plataforma profes-alumnos')

@section('content')

	<div id="panel-superior-texto">

		La plataforma profes-alumnos es una aplicación web que pone en contacto a personas que desean impartir o recibir clases particulares a distancia. Esto es posible gracias a un calendario interactivo donde los profesores pueden actualizar su disponibilidad y los alumnos solicitar lecciones...
		
	</div>

	<div class="content">

		<div id="paneles">

			<div id="arriba-izquierda">
				
				<p class="titulo-bienvenida-paneles">Área personal</p>

				<div>
					<img class="img-bienvenida-paneles" src="images/bienv-img-pers.png">
				</div>

				<p class="texto-bienvenida-paneles">
					Puedes registrarte como profesor o como alumno y crear una descripción que detalle lo que ofreces (si eres profesor) o buscas (alumno). Una vez empieces a interaccionar con otros usuarios, tu área personal te mostrará información tal como las lecciones que tienes o algunas estadisticas.
				</p>

			</div>

			<div id="arriba-derecha">
				
				<p class="titulo-bienvenida-paneles">Calendario</p>

				<div>
					<img class="img-bienvenida-paneles" src="images/bienv-img-cal.png">
				</div>

				<p class="texto-bienvenida-paneles">
					El calendario permite a los profesores marcar su disponibilidad para cada día. Los alumnos pueden verla y solicitar una lección, que el profesor finalmente debe confirmar. En caso de cambio de planes, se pueden hacer modificaciones.
				</p>
				
			</div>

			<div id="abajo-izquierda">
				
				<p class="titulo-bienvenida-paneles">Buscar alumnos y profes</p>

				<div>
					<img class="img-bienvenida-paneles" src="images/bienv-img-listas.png">
				</div>

				<p class="texto-bienvenida-paneles">
					Cuando te registras en la aplicación y rellenas tu información personal, el siguiente paso es ver qué otros usuarios hay registrados. Por ejemplo, si eres un alumno, puedes ver qué profesores imparten la materia que necesitas, y comparar entre ellos.
				</p>
				
			</div>

			<div id ="abajo-derecha">
				
				<p class="titulo-bienvenida-paneles">Correo</p>

				<div>
					<img class="img-bienvenida-paneles" src="images/bienv-img-correo.png">
				</div>

				<p class="texto-bienvenida-paneles">
					El chat de correo sirve a los alumos para preguntar dudas a los profesores, y a los profesores para enviar recomendaciones a los alumnos entre el periodo entre dos lecciones sucesivas. 
				</p>
				
			</div>

		</div>

	</div>

@endsection


