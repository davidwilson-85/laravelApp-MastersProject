@extends('layout-reg-user')

@section('title', 'Area personal')

@section('content')

	<div class="content">

		<div class="info-personal">
			
			<div class="avatar">
				
				<img id="avatar-img" src="images/{{ $imagen_perfil }}">

				<form enctype="multipart/form-data" method="post" action="/areapersonal/imagen">
			   	{{ csrf_field() }}
			   	<input class="input-file" name="imagen" id="imagen" type="file" onchange="this.form.submit()">
			   	<label for="imagen">Cambiar</label>
				</form>
			
			</div>

			<div class="datos">
				
				<p class="nombre"> {{ auth()->user()->name }} </p>

				@if (auth()->user()->role == 'prof')
					Profesor
				@else
					Alumno
				@endif

				<p> {{ auth()->user()->email }} </p>
				<p> {{ auth()->user()->whatsapp }} </p>
			
			</div>
			
			<div class="estadisticas">
			
				<p><b>Mis estadísticas</b></p>

				@if (auth()->user()->role == 'prof')
					<p>Alumnos: {{ $lista_interactores }} </p>
				@else
					<p>Profesores: {{ $lista_interactores }} </p>
				@endif
				
				<p>Clases solicitadas pendientes de respuesta: {{ $clases_solicitadas }} </p>
				
				<p>Clases confirmadas o impartidas: {{ $clases_confirmadas }} </p>
				
				<p>Longitud media de tus lecciones: {{ $clases_longitud_promedio }} minutos</p>
				
				<p>Registrado desde: {{ substr(auth()->user()->created_at, 0, 10) }}</p>
			
			</div>
			
			<div class="presentacion">
				
				<p><b>Mi carta de presentación</b> <i>(pincha para modificar)</i></p>
				
				<form method="post" action="/areapersonal" id="carta-presentacion">
	         		{{ csrf_field() }}
	         		<textarea class="presentacion-textarea" name="textopresentacion" rows="19" cols="46" spellcheck="false">{{ $textopresentacion }}</textarea>
	         		<button class="presentacion-button" type="submit" name="enviar">Guardar</button>
	         	</form>

			</div>

		</div>

	</div>

@endsection


