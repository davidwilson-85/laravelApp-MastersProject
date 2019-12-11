<html>

<head>
<style>

	body {
		margin: 0;
	}
	.header {
		background-color: blue;
		height: 50px;
	}
	.subheader {
		background-color: rgb(210, 210, 210);
		height: 50px;
	}

	.calendar {
		display: grid;
		grid-template-columns: 50px 50px 50px 50px 50px;
		grid-template-rows: 50px 50px 50px 50px 50px 50px 50px;
		grid-template-areas: 
			". calHead calHead calHead calHead"
			". day1 day2 day3 day4"
			"hora1 main main main main"
			"hora2 main main main main"
			"hora3 main main main main"
			"hora4 main main main main";
	}

	.cal-head {
		grid-area: calHead;
		background-color: rgb(128, 128, 128);
	}
	.day1, .day2, .day3, .day4 { background-color: rgb(100, 100, 100); }
	.day1 { grid-area: day1; }
	.day2 { grid-area: day2; }
	.day3 {	grid-area: day3; }
	.day4 {	grid-area: day4; }
	.hora1 { grid-area: hora1; }
	.hora2 { grid-area: hora2; }
	.hora3 { grid-area: hora3; }
	.hora4 { grid-area: hora4; }
	.main {
		grid-area: main;
		background-color: rgb(210, 210, 210);
	}

	


</style>
</head>



<body>

	<form enctype="multipart/form-data" method="post" action="/areapersonal/imagen">
	    {{ csrf_field() }}
	    <div class="form-group">
	        <label for="imageInput">File input</label>
	        <input data-preview="#preview" name="imagen" type="file" id="imageInput">
	        <img class="col-sm-6" id="preview"  src="">
	        <p class="help-block">Example block-level help text here.</p>
	    </div>
	    <div class="form-group">
	        <label for="">submit</label>
	        <input class="form-control" type="submit">
	    </div>
	</form>

	<div class="header">
		First
	</div>

	<div class="subheader">
		Second
	</div>



	<div class="calendar">
		<div class="cal-head">Agosto</div>
		<div class="day1">1<br>Lun</div>
		<div class="day2">2<br>Mar</div>
		<div class="day3">3<br>Mier</div>
		<div class="day4">4<br>Jue</div>
		<div class="hora1">8:00</div>
		<div class="hora2">10:00</div>
		<div class="hora3">12:00</div>
		<div class="hora4">14:00</div>
		<div class="main">
			
			<div style="background-color:red; width:50px; height:75px; position:relative; left:50px; top:0px;">
				Slot1
			</div>
			
			<div style="background-color:green; width:50px; height:50px; position:relative; left:100px; top:0px;">
				Slot2
			</div>
			
			<div style="background-color:green; width:50px; height:50px; position:relative; left:150px; top:0px;">
				Slot2
			</div>

		</div>
	</div>
























</body>


</html>