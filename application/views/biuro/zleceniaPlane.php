<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Zlecenie <small>Wybór samolotu</small></h1>
			</div>

			<table class="table table-striped">
				<thead>
				<tr>
					<th>Z</th>
					<th>Do</th>
					<th>Zapłata</th>
					<th>Pasażerów</th>
					<th class="hidden-xs">Deadline</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?=Helper_Map::getCityName($order->from)?></td>
					<td><?=Helper_Map::getCityName($order->to)?></td>
					<td><?=formatCash($order->cash) . " " . WAL?></td>
					<td><?=$order->count?></td>
					<td class='hidden-xs'><?=Helper_TimeFormat::timestampToText($order->deadline)?></td>
				</tr>
				</tbody>
			</table>

			<table style="width: 100%;" class="table table-striped">
			<thead>
				<tr>
					<th><span id="tutorial_plane">Samolot</span></th>
					<th>Załoga</th>
					<th><span id="tutorial_fuel">Korzystaj z paliwa w bazie</span></th>
					<th><span id="tutorial_time">Wybierz</span></th>
				</tr>
			</thead>
			<tbody>
			<?
				foreach($planes as $plane)
				{
					$model = $plane->getUpgradedModel();
					if($model->miejsc < $order->count)
						continue;
					$distance = Helper_Map::getDistanceBetween($order->from, $order->to);
					$czas = ($distance / ($model->predkosc*0.85)) + (ceil($model->miejsc/75)/4); //Dodatkowy czas na lądowanie i startowanie
					$czas = $czas * 3600;
					$placeInQueue = $plane->getPlaceInQueue($czas);
					$placeT = "";
					if($placeInQueue) {
						if($placeInQueue < time() + 5)
							$placeT = Helper_Prints::colorBgText('WOLNY', 'green');
						else
							$placeT = Helper_Prints::colorBgText('WOLNY OD '.Helper_TimeFormat::timestampToText($placeInQueue), 'green');
					} else
						$placeT = Helper_Prints::colorBgText('ZAJĘTY', 'red');

					?>

					<tr><?= Form::open('zlecenie/checkin')?>
						<td width='15%'><?=$plane->rejestracja?> (<?=$plane->city->name?>)<br /><br /><?=$placeT?></td>
						<td><?=$plane->printStaff()?></td>
						<td width='10%'><input type='checkbox' name='bazaPaliwo' title='Wykorzystaj paliwo z bazy, jeżeli to możliwe'/></td>
						<td width='20%'>
							<input type='hidden' name='zlecenie' value='<?=$zlecenie->id?>'/>
							<input type='hidden' name='plane' value='<?=$plane->id?>'/>
							Rozpoczęcie odprawy: <span class="startTime">Teraz <a href="#" class="changeTime">(Zmień)</a></span>
								<div class="input-group date col-xs-12 planowany_start" style="display: none;">
									<input data-format="dd/MM/yyyy hh:mm:ss" type="text" name="planowany_start" class="form-control planowany_start_input"/>
									<span class="input-group-addon add-on">
									  <i data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar planowany_start_button"></i>
									</span>
								</div>
							<?=Form::submit('send', 'Wybierz', array( 'class' => "btn btn-primary btn-block"))?>
						</td>
					</form></tr>
			<?  } ?>
			</tbody>
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
    $(function() {
		$(".changeTime").on('click', function(event) {
			event.preventDefault();

			$(this).parent().hide();
			$(this).parent().parent().find(".planowany_start").show();
		});
        $(".planowany_start_input").datetimepicker({
            lang:"pl",
            timepicker:true,
            format:"d.m.Y H:i",
            minDate:0,
            step:5
        });
        $(".planowany_start_button").click(function(){
            $(this).parent().parent().find(".planowany_start_input").datetimepicker("show");
        });

		var n1 = new tutorialElement($('#tutorial_plane'), "Hard_Bottom_Left", "Site_Bottom", "Wybierz samolot, który ma wykonać zlecenie", "inner", false);
		var n2 = new tutorialElement($('#tutorial_fuel'), "Hard_Bottom_Right", "Site_Top", "Jeżeli posiadasz zbiornik paliwa i paliwo na tym lotnisku, możesz użyć paliwa, które już wczesniej zakupiłeś", "inner", false);
		var n3 = new tutorialElement($('#tutorial_time'), "Site_Bottom", "Site_Bottom", "Możesz zmienić godzinę, o której samolot ma wystartować z tym zleceniem.", "inner", false);
		window.tutorialElements.push(n1, n2, n3);
    });
</script>