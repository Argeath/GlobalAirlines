<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Terminarz</h1>
			</div>
			<div class="clearfix"></div>
			<div class="hidden-xs clearfix">
				<div class="well col-md-12 col-lg-9">
					<div class="suwakDiv">
						<div id="terminarzDate" class="input-group date col-xs-12 col-sm-6 col-md-4" style="float: none; margin: 20px auto; padding: 0 15px;">
							<input id="terminarzDateInput" data-format="dd.MM.yyyy" type="text" name="terminarzDate" class="form-control"/>
							<span class="input-group-addon add-on">
								<i id="terminarzDateButton" data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i>
							</span>
						</div>
					</div>
					<? for($i=1; $i <= 7; $i++) { ?>
					<div class="suwakDiv">
						<div class="suwakDzien col-xs-2"><?=$dni[$i]['name'];?><br /><?=$dni[$i]['date'];?></div>
						<div class="suwakBlock col-xs-10">
							<?=$dni[$i]['zlecenia'];?>
							<div class="suwak"></div>
							<?=$textT;?>
							<? if($dni[$i]['today'])
								echo "<div class='suwakGodzina' style='left: ".$suwakGodzinaPoz."px;'></div>"; ?>
						</div>
					</div>
					<? } ?>
					<div class="clearfix"></div>
				</div>
				<div class="col-md-12 col-lg-3" style="display: table-cell;">
					<div class="well">
						<ul class="nav nav-tabs" id="terminarzTab">
						  <li class="active"><a href="#samoloty" id="terminarzPodglad">Samoloty</a></li>
						  <li class="disabled"><a href="#zlecenie" id="terminarzZlecA">Zlecenie</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
						  <div class="tab-pane active" id="samoloty">
							<div style="width: 100%; padding: 10px;">
								<? foreach($samoloty as $samolot)
								{
									echo $samolot['buttonCode'];
								} ?>
								<div class="clearfix"></div>
							</div>

						  </div>
						  <div class="tab-pane" id="zlecenie">
							<div class="tab-pane-loading"></div>
		<?=Form::open('terminarz/update');?>
							<input type="hidden" name="flightId" id="flightId"/>
							<table class="table table-striped table-bg-dark">
								<tr><td width="50%">Lot z: </td><td id="zlecenie_from"></td></tr>
								<tr><td>Lot do: </td><td id="zlecenie_to"></td></tr>
								<tr><td>Zapłata: </td><td id="zlecenie_cash"></td></tr>
								<tr><td>Kara: </td><td id="zlecenie_punish"></td></tr>
								<tr><td>Il. pasażerów: </td><td id="zlecenie_count"></td></tr>
								<tr><td>Deadline: </td><td id="zlecenie_deadline"></td></tr>
								<tr><td>Początek odprawy: </td><td id="zlecenie_flight_odprawa"></td></tr>
								<tr><td>Początek lotu: </td><td id="zlecenie_flight_started"></td></tr>
								<tr><td>Koniec lotu: </td><td id="zlecenie_flight_end"></td></tr>
								<tr><td>Zlecenie zaliczone: </td><td id="zlecenie_done"></td></tr>
								<tr><td><button id="moveButton" name="option" value="move" class="btn btn-block btn-warning"><i class="glyphicon glyphicon-backward"></i> Przesuń <i class="glyphicon glyphicon-forward"></i></button></td>
								<td>
								<?=Helper_Prints::rusureButton('<i class="glyphicon glyphicon-remove"></i> Odwołaj', 'option', 'cancel', ['btn-danger']);?>
								</td></tr>
							</table>
							</form>
						  </div>
						</div>
					</div>
				</div>
			</div>
			<div class="visible-xs">
				Terminarz nie jest dostępny w tej rozdzielczości ekranu.
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="<?=URL::base(TRUE);?>bower_components/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" />
<script type="text/javascript" src="<?=URL::base(TRUE);?>bower_components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
<script type='text/javascript'>

function getContrastYIQ(hexcolor){
	var r = parseInt(hexcolor.substr(0,2),16);
	var g = parseInt(hexcolor.substr(2,2),16);
	var b = parseInt(hexcolor.substr(4,2),16);
	var yiq = ((r*299)+(g*587)+(b*114))/1000;
	return (yiq >= 128) ? 'black' : 'white';
}

$(function() {
    var n1 = new tutorialElement($('#terminarzDate'), "Hard_Bottom_Left", "Site_Bottom", "Wybierz datę aby zobaczyć inny okres czasu", "inner", false);
    var n2 = new tutorialElement($('#samoloty'), "Site_Right", "Site_Right", "Wybierz samoloty, które chcesz zobaczyć na terminarzu.", "inner", false);
    var n3 = new tutorialElement($('#terminarzZlecA'), "Hard_Bottom_Right", "Site_Right", "Kliknij na lot, aby zobaczyć informacje o zleceniu.", "inner", false);
    window.tutorialElements.push(n1, n2, n3);

	$(".terminarzBtn").click(function(event) {
		if(event.target.nodeName != 'INPUT')
			$(this).children('input').click();
	});
	$("#terminarzDateInput").datetimepicker({
		value:"<?=$dateText;?>",
		lang:"pl",
		timepicker:false,
		format:"d.m.Y",
		onChangeDateTime:function(dp,$input){
			var parts = $input.val().split(".");
			var date = new Date(parts[2], parseInt(parts[1], 10) - 1, parts[0], 0, 0);
			location.href = url_base() + "terminarz/index/" + date.getTime()/1000;
		}
	});

	$("#terminarzDateButton").click(function(){
	  $("#terminarzDateInput").datetimepicker("show");
	});

	$('.suwakZlecenie').hide();

    $('.colorpick').colorpicker({
        color: $(this).css('background-color')
    }).on('changeColor.colorpicker', function(event) {
        var hex = event.color.toHex();

        var planeId = $(el).parent().parent().parent().attr('planeId');
        $(el).parent().parent().children().first().children().css('background-color', '#'+hex);
        var textcolor = getContrastYIQ(hex);
        $(el).parent().parent().children().first().children().css('color', textcolor);
        $(el).css('background-color', '#'+hex);
        $('.suwakZlecenie').each(function() {
            if($(this).attr('planeId') == planeId)
            {
                $(this).css('background-color', '#'+hex);
            }
        });

        var url = url_base()+'ajax/planeColor/' + planeId + '/' + hex;

        $.ajax(url);

    });

	$('.plane_sort').click(function() {
		var planeId = $(this).parent().parent().parent().parent().attr('planeId');
		var thisCheck = $(this).is(':checked');
		$('.suwakZlecenie').each(function() {
			if($(this).attr('planeId') == planeId)
			{
				if(thisCheck == true)
					$(this).show();
				else
					$(this).hide();

			}
		});
	});
	$('#terminarzPodglad').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});
	$('.suwakZlecenie').click(function() {
		$('.tab-pane-loading').show();
		$('#moveButton').prop('disabled', true);
		$('#cancelCheckbox').prop('disabled', true);
		var url = url_base()+'index.php/ajax/zlecenie/' + $(this).attr('flightId');
		$.ajax(url).done(function( msg ) {
			var obj = $.parseJSON(msg);
			if( ! obj)
				return false;
			try {
				$('#flightId').val(obj['flightId']);
				$('#zlecenie_from').text(obj['from']);
				$('#zlecenie_to').text(obj['to']);
				$('#zlecenie_cash').html(obj['cash']);
				$('#zlecenie_punish').html(obj['punish']);
				$('#zlecenie_deadline').text(obj['deadline']);
				$('#zlecenie_flight_odprawa').text(obj['odprawa']);
				$('#zlecenie_flight_started').text(obj['started']);
				$('#zlecenie_flight_end').text(obj['end']);
				$('#zlecenie_done').text(obj['done']);
				$('#zlecenie_count').text(obj['count']);
				if(obj['movecancel'])
				{
					$('#moveButton').prop(	'disabled', false);
					$('#cancelCheckbox').prop('disabled', false);
				}
			}
			catch(err)
			{
				alert(err.message);
			}

			$('.tab-pane-loading').hide();
			$('#terminarzTab a:last').tab('show');
		});

	});
});
</script>