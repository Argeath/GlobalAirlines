<div id="dialog-confirm" title="Wziąć to zlecenie?" zlid="0" style="display: none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin: 15px 7px 10px 0;"></span>Czy jesteś pewien, że chcesz wziąć to zlecenie? Niewywiązanie się z umowy grozi grzywną pieniężną.</p>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Dostępne zlecenia</h1>
			</div>
			<div class="tablica">
				<div class="col-xs-12 col-md-2 col-md-offset-3">
					<select id="sort_plane" data-placeholder="Wybierz samolot" style="width: 100%;">
						<option></option>
						<?
							foreach($planes as $p) {
								$plane = $p->getUpgradedModel();
								if( ! $plane->loaded())
									continue;
								$poz = " (".$p->city->name.")";
								$busy = $p->isBusy();
								$busyT = ($busy == Helper_Busy::NotBusy) ? $poz : " (".Helper_Busy::getText($busy).")";
								echo '<option id="plane_'.$p->id.'" value="'.$p->id.'" spalanie="'.$plane->spalanie.'" zasieg="'.$plane->zasieg.'" miejsc="'.$plane->miejsc.'" mnoznik="'.$p->getMultiplier().'" predkosc="'.$plane->predkosc.'">'.$p->rejestracja.''.$busyT.'</option>';
							}
						?>
					</select>
				</div>
				<div class="col-xs-12 col-md-2">
					<select id="sort_z" data-placeholder="Lot z" style="width: 100%;">
						<option></option>
						<optgroup label="Aktualne">
							<?
								$printed = [];
								foreach($citiess as $city) {
									echo '<option value="' . $city->id . '" data-flag="' . strtolower($city->getCountry()) . '"">' . $city->name . '</option>';
									$printed[] = $city->name;
								}
								$cities = ORM::factory('City')->order_by('region', 'asc')->order_by('name', 'asc')->find_all();
								$lastRegion = "";
								foreach($cities as $city) {
									if($city->region != $lastRegion) {
										if($lastRegion != "")
											echo "</optgroup>";
										echo "<optgroup label='".$city->getCountry()."' data-flag='".strtolower($city->getCountry())."'>";
										$lastRegion = $city->region;
									}
									if(!in_array($city->name, $printed))
										echo '<option value="' . $city->id . '">' . $city->name . '</option>';
								}
							?>
						</optgroup>
					</select>
				</div>
				<div class="col-xs-12 col-md-2">
					<select id="sort_do" data-placeholder="Lot do" style="width: 100%;">
						<option></option>
						<?
							$cities = ORM::factory('City')->order_by('region', 'asc')->order_by('name', 'asc')->find_all();
							$lastRegion = "";
							foreach($cities as $city) {
								if($city->region != $lastRegion) {
									if($lastRegion != "")
										echo "</optgroup>";
									echo "<optgroup label='".$city->getCountry()."' data-flag='".strtolower($city->getCountry())."'>";
									$lastRegion = $city->region;
								}
								echo '<option value="' . $city->id . '">' . $city->name . '</option>';
							}
						?>
						</optgroup>
					</select>
				</div>
				<div class="clearfix"></div>
				<br />
				<div class="alert alert-danger">
					<strong>Uwaga!</strong> Za niewykonanie podjętego zlecenia grozi <b>kara pieniężna</b>. Bierz zlecenia odpowiedzialnie!
				</div>
				<div style="margin: 10px auto;">
					<table style="width: 100%;" class="board">
						<thead>
							<th width="10%"><span id="tutorial_from">Skad</span></th>
							<th width="10%"><span id="tutorial_to">Dokad</span></th>
							<th width="10%">Dystans</th>
							<th width="10%"><span id="tutorial_cash">Zaplata</span></th>
							<th width="5%"><span id="tutorial_people">Osob</span></th>
							<th width="20%"><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="top" title="Termin po którym zostanie naliczona kara">Deadline</span></th>
							<th width="15%"><span id="tutorial_efficiency" class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="top" title="Przewidywany zysk na locie dla danego samolotu. (Uwaga: nie uwzględnia opłat dodatkowych oraz cen paliwa)">Oplacalnosc</span></th>
						</thead>
						<tbody id="ordersBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(function() {
	var sort_plane 		= 0;
	var sort_spalanie 	= 0;
	var sort_miejsc 	= 0;
	var sort_zasieg 	= 0;
	var sort_mnoznik 	= 0;
	var sort_predkosc	= 0;
	function findOrders() {
		var planeId = parseInt($("#sort_plane").val());
		if(planeId == 0) {
			$('#ordersBody tr').remove();
			return true;
		}
		var from = (parseInt($("#sort_z").val()) > 0) ? "/"+$("#sort_z").val() : "";
		var to = (parseInt($("#sort_to").val()) > 0) ? "/"+$("#sort_to").val() : "";
		$.getJSON("/ajax/findOrders/" + planeId + from + to, function(data) {
			$('#ordersBody tr').remove();
			$.each(data, function(key, val) {
				var tr = $("<tr>").addClass('elem').attr('zlid', val['id']).attr('sort_z', val['from']).attr('sort_do', val['to'])
					.attr('dystans', val['dist']).attr('pasazerow', val['count']).attr('zaplata', val['cash']);
				var tds = $.parseHTML(val['tds']);
				tr.append(tds);

				tr.click(function(ev) {
					ev.preventDefault();
					var zlid = $(this).attr('zlid');
					$( "#dialog-confirm" ).attr('zlid', zlid).dialog('open');
				});

				tr.appendTo('#ordersBody');
			});
			sortZlec();
		});
		sort_plane = $("#sort_plane").val();
		var option = $("#sort_plane").find("#plane_" + sort_plane);
		sort_spalanie = parseFloat(option.attr('spalanie'));
		sort_zasieg = parseInt(option.attr('zasieg'));
		sort_miejsc = parseInt(option.attr('miejsc'));
		sort_mnoznik = parseInt(option.attr('mnoznik'));
		sort_predkosc = parseInt(option.attr('predkosc'));
		sortZlec();
	}

	$('#sort_plane').on('change.fs', findOrders);
	$('#sort_z').on('change.fs', findOrders);
	$('#sort_do').on('change.fs', findOrders);

	var sort_z = 0;
	var sort_do = 0;
	$('#sort_z').on('change.fs', function() {
		sort_z = $(this).val();
		sortZlec();
	});
	$('#sort_do').on('change.fs', function() {
		sort_do = $(this).val();
		sortZlec();
	});

	function sortZlec() {
		$('.elem').each(function() {
			var show = true;
			if(sort_z > 0)
			{
				if( parseInt($(this).attr('sort_z')) != sort_z)
					show = false;
			}
			if(sort_do > 0)
			{
				if( parseInt($(this).attr('sort_do')) != sort_do)
					show = false;
			}
			var dystans = parseInt($(this).attr('dystans'));
			var pasazerow = parseInt($(this).attr('pasazerow'));
			if(sort_plane > 0)
			{
				if( dystans > sort_zasieg )
					show = false;

				if( pasazerow > sort_miejsc )
					show = false;
			}
			if(show) {
                var halfstars = 0;
				$(this).find('.stars .star').remove();
				var zaplata = parseInt($(this).attr('zaplata'));
				var czas = (dystans / sort_predkosc) + 0.5;
				var kara = Math.round((sort_miejsc - pasazerow)*0.5) * 500;
				var halfstars = Math.round((zaplata - ((dystans * sort_spalanie * 3) + kara)) / (500 * sort_mnoznik * czas));
				var stars = Math.floor(halfstars / 2);
				var starsCont = $(this).find('.stars');
				for(var i = 1; i <= stars; i++)
					$("<i/>").addClass('star').addClass('fullstar').appendTo(starsCont);
				if(halfstars % 2)
					$("<i/>").addClass('star').addClass('halfstar').appendTo(starsCont);
                $(this).show();
			} else
				$(this).hide();
		});
	}
    $( "#dialog-confirm" ).show().dialog({
		dialogClass: "no-close",
		resizable: false,
		height: 250,
		modal: true,
		autoOpen: false,
		buttons: {
			"Akceptuj": function() {
				location.href = url_base() + 'zlecenia/0/0/take/' + $( "#dialog-confirm" ).attr('zlid');
				$( this ).dialog( "close" );
			},
			"Odrzuć": function() {
				$( this ).dialog( "close" );
			}
		}
    });

	var n1 = new tutorialElement($('#tutorial_from'), "Hard_Bottom_Left", "Site_Top", "Lotnisko, z którego samolot musi zabrać ludzi", "inner", false);
	var n2 = new tutorialElement($('#tutorial_to'), "Site_Top", "Site_Top", "Lotnisko, na które samolot musi przetransportować ludzi", "inner", false);
	var n3 = new tutorialElement($('#tutorial_cash'), "Site_Bottom", "Site_Bottom", "Zapłata za zlecenie", "inner", false);
	var n4 = new tutorialElement($('#tutorial_people'), "Hard_Bottom_Left", "Site_Bottom", "Liczba osób, które trzeba przetransportować", "inner", false);
	var n5 = new tutorialElement($('#tutorial_efficiency'), "Hard_Top_Right", "Site_Top", "Im większa ilość gwiazdek, tym zlecenie jest bardziej opłacalne (większy zysk)", "inner", false);
	window.tutorialElements.push(n1, n2, n3, n4, n5);
});
</script>