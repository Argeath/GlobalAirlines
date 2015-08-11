<div class="well">
	<div class="page-header">
		<h1>Przesuń lot</h1>
	</div>
	
	<table class="table table-striped">
		<thead>
		<tr>
			<th>Samolot</th>
			<th>Z</th>
			<th>Do</th>
			<th>Rozpoczęcie odprawy</th>
			<th>Przewidywany przylot</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td><?= $plane->fullName() ?></td>
			<td><?= Map::getCityName($flight->from) ?></td>
			<td><?= Map::getCityName($flight->to) ?></td>
			<td><?= TimeFormat::timestampToText($flight->started) ?></td>
			<td><?= TimeFormat::timestampToText($flight->end) ?></td>
		</tr>
		</tbody>
	</table>
	
	<?= Form::open('terminarz/update', array('class' => 'form_300 well')); ?>
		<input type="hidden" name="flightId" value="<?= $flight->id; ?>"/>
		<div id="planowany_start" class="input-group date col-xs-12" style="float: none; margin: 0 auto;">
			<input id="planowany_start_input" data-format="dd/MM/yyyy hh:mm:ss" type="text" name="planowany_start" class="form-control"></input>
			<span class="input-group-addon add-on">
			  <i id="planowany_start_button" data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i>
			</span>
		</div>
		<button name="option" value="move" class="btn btn-block btn-primary">Przesuń</button>
	</form>
</div>

<script type="text/javascript">
  $(function() {
	$("#planowany_start_input").datetimepicker({
		 lang:"pl",
		 timepicker:true,
		 format:"d.m.Y H:i",
		 minDate:0,
		 step:5
		});
	$("#planowany_start_button").click(function(){
	  $("#planowany_start_input").datetimepicker("show");
	});
  });
</script>