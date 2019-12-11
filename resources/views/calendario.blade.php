@extends('layout-reg-user')

@section('title', 'Mi calendario')

@section('content')

	<div class="content">

		@if ($rol == 'prof')

			<div class="modificar-calendario">

				<form class="calendario-form" method="post" action="/calendario/slots" id="nueva-disp">

					{{ csrf_field() }}
					{{ method_field('PATCH') }}

					<input name="semana" type="hidden" value="{{ $week }}">

					<span style="margin:0 50px 0 20px">Modificar disponibilidad.</span> Día:

					<select class="calendario-selector-diayhora" name="dia_semana" form="nueva-disp">
						<option value="1">Lunes</option>
						<option value="2">Martes</option>
						<option value="3">Miercoles</option>
						<option value="4">Jueves</option>
						<option value="5">Viernes</option>
						<option value="6">Sábado</option>
						<option value="7">Domingo</option>
					</select>

					Disponibilidad:

					<input class="calendario-selector-diayhora" type="text" name="disp" placeholder="ej.: 08:00-10:30,16:00-17:50" value="{{ old('disp') }}"></input>

					<button class="header-button" type="submit" name="enviar_slot">OK</button>

				</form>

				<div id="calendario-info">

					
					@if($errors->any())
						@if ($errors->all()[0] == '1')
							<span style="background-color: red">¡ERROR!</span>
						@else
							<span style="background-color: rgb(51, 204, 51)">¡BIEN!</span>
						@endif
						 - {{$errors->all()[1]}}					
					@else
						 Para eliminar la disponibilidad de un día, deja la hora en blanco. Modificar la disponibilidad de un día elimina cualquier lección.
					@endif
					

				</div>

			</div>

		@endif

		@if ($rol == 'alum')

			<div class="modificar-calendario">

				<form class="calendario-form" method="post" action="/calendario/cambiarprofesor" id="selec-prof">

					{{ csrf_field() }}

					Profesor:
					<select class="select-user" name="profesor" form="selec-prof" onchange="this.form.submit()">
						
						<option value="p">Selecciona</option>

						@foreach ($profs as $prof)
							<option value="{{ $prof->id }}" @if ($prof->id == $prof_activo) selected="selected" @endif>
								{{ $prof->name }}
							</option>	
						@endforeach
					
					</select>

					<input name="semana" type="hidden" value="{{ $week }}">

				</form>

				<!-- Alternativa mas sencilla desde el punto de vista del routing. El problema es que habría que hacer un drop-down con CSS...
				@foreach ($profs as $prof)

					<a href="/calendario/{{ $prof->id }}/{{ $week }}">{{ $prof->name }}</a>

				@endforeach
				-->
					
				<form class="calendario-form" method="post" action="/calendario/lecciones" id="nueva-leccion">

					{{ csrf_field() }}

					<input name="semana" type="hidden" value="{{ $week }}">
					<input name="prof" type="hidden" value="{{ $prof_activo }}">

					Nueva lección el 

					<select class="calendario-selector-diayhora" name="dia_semana" form="nueva-leccion">
						<option value="1">Lunes</option>
						<option value="2">Martes</option>
						<option value="3">Miercoles</option>
						<option value="4">Jueves</option>
						<option value="5">Viernes</option>
						<option value="6">Sábado</option>
						<option value="7">Domingo</option>
					</select>

					 a las

					<input class="calendario-selector-diayhora" type="text" name="hora" placeholder="ej.: 10:30-12:00" value="{{ old('disp') }}"></input>

					<button class="header-button" type="submit" name="enviar_leccion">OK</button>

				</form>

				<div id="calendario-info">

					@if($errors->any())
						@if ($errors->all()[0] == '1')
							<span style="background-color: red">ERROR</span>
						@else
							<span style="background-color: rgb(51, 204, 51)">BIEN!</span>
						@endif
						 - {{$errors->all()[1]}}					
					@else
						 Máximo una lección por día. Para eliminar una lección, deja la hora en blanco.
					@endif

				</div>

			</div>

		@endif




		@if ($prof_activo == 'p')

			<div id="mensaje-grande">Por favor, selecciona un profesor. . .</div>

		@else

			<div class="calendar">
				<div class="cal-head">
					<div class="cal-head-contenido">
						<a class="cal-button" href="/calendario/{{ $prof_activo }}/{{ $week-1 }}"> < </a>
						<a class="cal-button" href="/calendario/{{ $prof_activo }}"> v </a>
						<a class="cal-button" href="/calendario/{{ $prof_activo }}/{{ $week+1 }}"> > </a>
					</div>
					<div class="cal-head-contenido" id="cal-mes">
						{{ $mesActual }}
					</div>
				</div>
				<div class="day1">Lunes<br>{{ $weekData[0]->dia }}</div>
				<div class="day2">Martes<br>{{ $weekData[1]->dia }}</div>
				<div class="day3">Miércoles<br>{{ $weekData[2]->dia }}</div>
				<div class="day4">Jueves<br>{{ $weekData[3]->dia }}</div>
				<div class="day5">Viernes<br>{{ $weekData[4]->dia }}</div>
				<div class="day6">Sábado<br>{{ $weekData[5]->dia }}</div>
				<div class="day7">Domingo<br>{{ $weekData[6]->dia }}</div>
				<div class="esp-horiz"></div>
				<div class="hora1">6:00</div>
				<div class="hora2">8:00</div>
				<div class="hora3">10:00</div>
				<div class="hora4">12:00</div>
				<div class="hora5">14:00</div>
				<div class="hora6">16:00</div>
				<div class="hora7">18:00</div>
				<div class="hora8">20:00</div>
				<div class="hora9">22:00</div>
				<div class="main">
					<!--
					<div class="cal-slot" style="height:100px; left:120px; top:0px;">
						6:00<br>9:30<br>Editar
					</div>
					-->
					@foreach ($processedSpans as $slot)
						
						<div class="cal-slot" style="height:{{ $slot['height'] - 4 }}px; left:{{ $slot['left_coord'] }}px; top:{{ $slot['top'] }}px;">
							<!-- Nota: El padding incrementa el temaño del div (!?) por lo que compenso restando unas pocas unidades al valor de height -->
							
							{{ $slot['ini'] }} <br> {{ $slot['fin'] }}
							
						</div>
					
					@endforeach

					@foreach ($lecciones as $leccion)
						
						<div class="cal-leccion"
							style="
								height:{{ $leccion['altura'] - 4 }}px;
								left:{{ $leccion['coord_izq'] }}px;
								top:{{ $leccion['coord_arriba'] }}px;

								@if ($rol == 'prof')
									@if ($leccion['estado'] == 'solicitada')
										background-color: rgb(230, 92, 0);
									@elseif ($leccion['estado'] == 'confirmada')
										background-color: green;
									@endif
								@endif

								@if ($rol == 'alum')
									@if ($leccion['alum_id'] == auth()->user()->id AND $leccion['estado'] == 'solicitada')
										background-color: rgb(230, 92, 0);
									@elseif ($leccion['alum_id'] == auth()->user()->id AND $leccion['estado'] == 'confirmada')
										background-color: green;
									@else
										background-color: blue;
									@endif
								@endif
						">
							
							{{ $leccion['ini'] }}-
							{{ $leccion['fin'] }}

							@if ($rol == 'prof' AND $leccion['estado'] == 'solicitada')
								<a style="color:white" href="/calendario/lecciones/confirmar/{{$leccion['id']}}">Confirmar</a>
							@elseif ($leccion['estado'] == 'confirmada')
								(Conf.)
							@endif
							<br>
							
							{{ $leccion['alum_nombre'] }}<br>
							<!-- {{ $leccion['estado'] }} -->
							
						</div>
					
					@endforeach

				</div>

			</div>

		@endif

	</div>

@endsection


