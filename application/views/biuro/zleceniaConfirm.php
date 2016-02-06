<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Zlecenie <small>Potwierdzenie</small></h1>
			</div>
			<?=Form::open('zlecenie/send');?>
			<table style="margin: 0 auto;" class="table table-striped">
				<tr><td style="width: 30%">Z:</td> <td style="min-width: 150px;"><?=$z;?></td></tr>
				<tr><td>Do:</td> <td><?=$do;?></td></tr>
				<tr><td>Dystans:</td> <td style="position: relative;"><?=(($zasieg < $dystans) ? "<span style='color: red;'>" : "")?> <?=$dystans;?> km<?=(($zasieg < $dystans) ? '</span><div class="table-right-element text-red"><i class="glyphicon glyphicon-remove"></i></div>' : '<div class="table-right-element text-green"><i class="glyphicon glyphicon-ok"></i></div>')?></td></tr>
				<tr><td>Pasażerów:</td> <td style="position: relative;"><?=(($miejsc < $pasazerow) ? "<span style='color: red;'>" : "")?> <?=$pasazerow;?><?=(($miejsc < $pasazerow) ? '</span><div class="table-right-element text-red"><i class="glyphicon glyphicon-remove"></i></div>' : '<div class="table-right-element text-green"><i class="glyphicon glyphicon-ok"></i></div>')?></td></tr>
				<tr><td>Przewidywany czas odprawy:</td> <td><?=$odprawaT?></td></tr>
				<tr><td>Przewidywany czas lotu:</td> <td><?=$czasT?></td></tr>
				<tr><td>Przewidywany czas rozpoczęcia odprawy:</td> <td><?=Helper_TimeFormat::timestampToText($start);?></td></tr>
				<tr><td>Przewidywany czas startu:</td> <td><?=Helper_TimeFormat::timestampToText($start + $odprawa);?></td></tr>
				<tr><td>Przewidywany czas lądowania:</td> <td><?=Helper_TimeFormat::timestampToText($start + $odprawa + $czas);?></td></tr>
				<tr><td>Potrzebne paliwo:</td> <td><?=formatCash($paliwo);?> kg</td></tr>
				<? if($bazaB) { ?>
					<tr><td>Paliwo w bazie:</td> <td><?=$bazaOil;?> kg</td></tr>
					<tr><td>Wykorzystane paliwo z bazy:</td> <td><?=$paliwoZBazy;?> kg</td></tr>
				<? } ?>
				<tr  style="border-top: 3px #C9C9C9 double;"><td>Stan samolotu:</td> <td><?=$stan;?>%</td></tr>
				<tr style="border-bottom: 3px #C9C9C9 double;"><td colspan="2" data-html="true" data-toggle="tooltip" data-container="body" data-placement="bottom" title="<?=$zalogaT;?>">
					Załoga:
					<?=Helper_Prints::colorBgText("Pilotów: " . $juzPilotow . "/" . $pilotow, "#428bca");?>
					<?=Helper_Prints::colorBgText("Załogi dodatkowej: " . $juzDodatkowej . "/" . $dodatkowej, "#428bca");?>
				</td></tr>
				<tr><td>Honorarium za zlecenie:</td> <td style="color: green;"><?=formatCash($zaplata);?> <?=WAL?></td></tr>
				<tr data-toggle="collapse" data-target=".costs" class="collapse-zoom"><td>Opłaty:</td> <td style="color: red;">-<?=formatCash($kosztP + $kosztO + $kosztZ + $oplaty);?> <?=WAL?></td></tr>

				<tr class="costs collapse out"><td>Cena paliwa:</td> <td><?=formatCash(Helper_Oil::getOilCost(), 2);?> <?=WAL?></td></tr>
				<tr class="costs collapse out"><td>Opłata za paliwo:</td> <td style="color: red;">-<?=formatCash($kosztP);?> <?=WAL?></td></tr>
				<tr class="costs collapse out"><td>Opłata za punkt odpraw:</td> <td style="color: red;">-<?=formatCash($kosztO);?> <?=WAL?></td></tr>
				<tr class="costs collapse out"><td>Honorarium dla załogi:</td> <td style="color: red;">-<?=formatCash($kosztZ);?> <?=WAL?></td></tr>
				<tr class="costs collapse out"><td>Opłaty dodatkowe(Lotniskowe, Trasowe, Catering):</td> <td style="color: red;">-<?=formatCash($oplaty);?> <?=WAL?></td></tr>
				<tr><td>Zniżka:</td> <td><?=formatCash((1 - $znizka) * 100);?> %</td></tr>
				<tr><td>Razem:</td> <td style="border-top: 3px #C9C9C9 double;";><span class="text-rounded" style="background: <? if($razem>0) echo 'rgb(15, 163, 15)'; else echo 'red'; ?>"> <?=formatCash($razem);?> <?=WAL?></span> </td></tr>
			</table>

			<input type='hidden' name='zlecenie' value='<?=$zlecenieId;?>'/>
			<input type='hidden' name='bazaPaliwo' value='<?=(int) $bazaB;?>'/>
			<input type='hidden' name='plane' value='<?=$planeId;?>'/>
			<input type='hidden' name='checkin' value='<?=$checkinId;?>'/>
			<input type='hidden' name='planowany_start' value='<?=$planowany_start;?>'/>
			<br />
			<?=Helper_Prints::rusureButton("Wyślij", "send", "send_plane", ['btn-primary']);?>

			</form>
		</div>
	</div>
</div>

<script>
	$(function() {
		$(".collapse-zoom").css('cursor', 'zoom-in');
		$(".costs").on('shown.bs.collapse', function() {
			$(".collapse-zoom").css('cursor', 'zoom-out');
		}).on('hidden.bs.collapse', function() {
			$(".collapse-zoom").css('cursor', 'zoom-in');
		});
	})
</script>