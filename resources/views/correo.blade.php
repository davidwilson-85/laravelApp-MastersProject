@extends('layout-reg-user')

@section('title', 'Correo')

@section('content')

    <div class="content">

    	<form method="post" action="/correo/cambiar/dest" id="selec-dest">

			{{ csrf_field() }}

			<select class="select-user" name="destinatario" form="selec-dest" onchange="this.form.submit()">
				
                <option value="">Selecciona un destinatario</option> 

                @foreach ($posibles_destinatarios as $pd)
                    <option value="{{ $pd->id }}" @if ($pd->id == request('destinatario')) selected="selected" @endif>{{ $pd->name }}</option>
                @endforeach
			</select>

		</form>

        @if (!empty(request('destinatario')))
            <form method="post" action="/correo/destinatario" id="nuevo-mensaje">

                {{ csrf_field() }}

                <textarea class="textarea-mensaje" name="contenido" rows="8" cols="80">Escribe un mensaje...</textarea>

                <input name="prof" type="hidden" value="{{ $prof }}">
                <input name="alum" type="hidden" value="{{ $alum }}">
                <br>
                <button class="header-button" type="submit" name="enviar_mensaje">Enviar ></button>

            </form>
        @endif

        <div id="mensajes-espaciador"></div>

        <div id="contenedor-mensajes">

            <p>Mensajes ordenados de más reciente a más antiguo:</p>

            @foreach ($mensajes as $mensaje)

                @if ($mensaje->id_remitente == auth()->user()->id)
                    <div class="mensaje mensaje-primero">
                        <div class="mensaje-primero-encabezado">
                         {{ $mensaje->created_at }} > Tú dices:
                         <br>
                         <a href="/correo/delete/{{ $mensaje->id }}" class="link-eliminar-mensaje">Eliminar</a>
                        </div>
                        <div class="mensaje-primero-cuerpo">
                            {{ $mensaje->contenido }}
                        </div>
                    </div>

                @else
                    <div class="mensaje mensaje-segundo">
                        <div class="mensaje-segundo-encabezado">
                            {{ $mensaje->created_at }} > {{ $mensaje->name }} dice:
                        </div>
                        <div class="mensaje-segundo-cuerpo">
                            {{ $mensaje->contenido }}
                        </div>
                    </div>

                @endif

            @endforeach

        </div>

    </div>

@endsection


