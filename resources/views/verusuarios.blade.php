@extends('layout-reg-user')

@section('title', 'Ver usuarios registrados')

@section('content')

	<div class="content">

		@if (count($usuarios) == 0)
			
			<div id="mensaje-grande">
				
				@if (auth()->user()->role == 'alum')
					Todavía no hay ningún profesor registrado. Cuando los haya, aquí podrás ver sus fotos y su descripción . . .
				@elseif (auth()->user()->role == 'prof')
					Todavía no hay ningún alumno registrado. Cuando los haya, aquí podrás ver sus fotos y su descripción . . .
				@endif

			</div>
		
		@else
		
			@foreach ($usuarios as $usuario)

				<div class="usuario-tarjeta">

					<div id="imagen">
						@if ($usuario->imagen_perfil == null)
							<img id="usuario-avatar-img" src="images/imagen_perfil_generica.png">
						@else
							<img id="usuario-avatar-img" src="images/{{ $usuario->imagen_perfil }}">
						@endif
					</div>
					
					<div id="info-personal">
						<p>{{ $usuario->name }} </p>
						{{ $usuario->email }} <br>
						{{ $usuario->whatsapp }} <br>
						{{ $usuario->created_at }} <br><br>

						<a href="/correo/{{ $usuario->id }}">Chatear</a>
					</div>
					
					<div id="texto-presentación">
						@if ($usuario->textopresentacion == null)
							{{ $usuario->name }} todavía no ha escrito una carta de presentación. 
						@else
							{{ $usuario->textopresentacion }}
						@endif
					</div>

				</div>

			@endforeach

		@endif

	</div>

@endsection


