<div class="well">
	<div class="page-header">
		<h1>Lotnisko <small><?=$lotnisko->getName();?> (<?=$lotnisko->code?>)</small></h1>
	</div>
	<ul class="pagination pagination-xm">
		<li <?=($action == 'index') ? "class='active'" : "";?>><?=HTML::anchor('airport/index/' . $city_id, 'Lotnisko');?></li>
		<li <?=($action == 'offices') ? "class='active'" : "";?>><?=HTML::anchor('airport/offices/' . $city_id, 'Biura');?></li>
		<li <?=($action == 'stats') ? "class='active'" : "";?>><?=HTML::anchor('airport/stats/' . $city_id, 'Statystyki');?></li>
		<? if($office_id > 0) { ?> <li <?=($action == 'office') ? "class='active'" : "";?>><?=HTML::anchor('airport/office/' . $office_id, 'Twoje biuro');?></li>
		<? } else { ?> <li <?=($action == 'newOffice') ? "class='active'" : "";?>><?=HTML::anchor('airport/newOffice/' . $city_id, 'Wykup biuro');?></li><? } ?>
	</ul>
	<div class="clearfix"></div>
	<div class="col-lg-6 col-md-12 board" style="border-right: 2px #FFF solid;">
		<h4 class="title">Odloty</h4>
		<table style="width: 100%;">
			<thead>
				<th width="10%">Typ</th>
				<th width="20%">Samolot</th>
				<th width="15%">Do</th>
				<th width="25%">Czas wylotu</th>
				<th width="15%">POZOSTALO</th>
			</thead>
			<tbody>
				<?
					$departures = $lotnisko->getDepartures();
					if(count($departures) > 0)
					{
						foreach($departures as $departure)
						{
							$us = $departure->user;
							if( ! $us->loaded())
								continue;

							$plane = $departure->UserPlane;
							if( ! $plane->loaded())
								continue;

							$typ = $plane->getUpgradedModel();

							$planeHTML = "<img src='".URL::base(TRUE)."assets/samoloty/".$plane->plane_id.".jpg' class='img-rounded hidden-xs' style='width: 100%;'/><br />".$us->drawButton()."<br />".$typ->producent." ".$typ->model."<br />Miejsc: ".$typ->miejsc."<br />Spalanie: ".$typ->spalanie."kg/km<br />Prędkość przelotowa: ".$typ->predkosc."km/h<br />Stan: ".round($plane->stan, 2)."%<br />Pokonana trasa: ".formatCash($plane->km)."km<br />Czasu w powietrzu: ".Helper_TimeFormat::secondsToText($plane->hours)."";

							$event = DB::select()->from('events')->where('id', '=', $departure->event)->execute()->as_array();
							if(empty($event))
								continue;
							$event = $event[0];
							echo "<tr>";
								echo '<td><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="'.eventTypeToName($event['type']).'">['.eventTypeToShort($event['type']).']</span></td>';
								echo '<td><span class="Jpopover" data-container=".main" data-toggle="popover" data-placement="bottom" data-html="true" data-content="'.$planeHTML.'">'.$plane->rejestracja.'</span></td>';
								echo "<td>".HTML::anchor('airport/index/'.$departure->to, cleanString(Helper_Map::getCityName($departure->to), false, " "))."</td>";
								echo "<td>".Helper_TimeFormat::timestampToText($departure->odprawa)."</td>";
								echo "<td class='zegarCountdown' czas='".$departure->odprawa."' now='".time()."'>". Helper_TimeFormat::secondsToText($departure->odprawa - time()) ."</td>";
							echo "</tr>";
						}

					} else {
						echo "<tr><td colspan='5' style='text-align: center;'>Nie ma zadnych lotow</td></tr>";
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-lg-6 col-md-12 board" style="border-left: 2px #FFF solid;">
		<h4 class="title">Przyloty</h4>
		<table style="width: 100%;">
			<thead>
				<th width="10%">Typ</th>
				<th width="20%">Samolot</th>
				<th width="15%">Z</th>
				<th width="25%">Czas przylotu</th>
				<th width="15%">Pozostalo</th>
			</thead>
			<tbody>
				<?
					if(count($flights) > 0)
					{
						foreach($flights as $flight)
						{
							$us = $flight->user;
							$plane = $flight->UserPlane;
							if( ! $plane->loaded())
								continue;

							$typ = $plane->getUpgradedModel();

							$planeHTML = "<img src='".URL::base(TRUE)."assets/samoloty/".$plane->plane_id.".jpg' class='img-rounded hidden-xs' style='width: 150px;'/><br />".$typ->producent." ".$typ->model."<br />".$us->drawButton()."<br />Miejsc: ".$typ->miejsc."<br />Spalanie: ".$typ->spalanie."kg/km<br />Prędkość przelotowa: ".$typ->predkosc."km/h";

							$event = DB::select()->from('events')->where('id', '=', $flight->event)->execute()->as_array();
							if(empty($event))
								continue;
							$event = $event[0];
							echo "<tr>";
								echo '<td><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="'.eventTypeToName($event['type']).'">['.eventTypeToShort($event['type']).']</span></td>';
								echo '<td><span class="Jpopover" data-container=".main" data-toggle="popover" data-placement="bottom" data-html="true" data-content="'.$planeHTML.'">'.$plane->rejestracja.'</span></td>';
								echo "<td>".HTML::anchor('airport/index/'.$flight->from, cleanString(Helper_Map::getCityName($flight->from), false, " "))."</a></td>";
								echo "<td>".Helper_TimeFormat::timestampToText($flight->end)."</td>";
								echo "<td class='zegarCountdown' czas='".$flight->end."' now='".time()."'>". Helper_TimeFormat::secondsToText($flight->end - time()) ."</td>";
							echo "</tr>";
						}

					} else {
						echo "<tr><td colspan='5' style='text-align: center;'>Nie ma zadnych lotow</td></tr>";
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="clearfix"></div>
</div>