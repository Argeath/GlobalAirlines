<div class="well">
	<div class="page-header">
		<h1>Lotnisko <small><?=$lotnisko->getName();?> (<?=$lotnisko->city->code?>)</small></h1>
	</div>
	<ul class="pagination pagination-xm">
		<li <?=($action == 'index') ? "class='active'" : "";?>><?=HTML::anchor('airport/index/' . $city_id, 'Lotnisko');?></li>
		<li <?=($action == 'offices') ? "class='active'" : "";?>><?=HTML::anchor('airport/offices/' . $city_id, 'Biura');?></li>
		<li <?=($action == 'stats') ? "class='active'" : "";?>><?=HTML::anchor('airport/stats/' . $city_id, 'Statystyki');?></li>
		<? if($office_id > 0) { ?> <li <?=($action == 'office') ? "class='active'" : "";?>><?=HTML::anchor('airport/office/' . $office_id, 'Twoje biuro');?></li>
		<? } else { ?> <li <?=($action == 'newOffice') ? "class='active'" : "";?>><?=HTML::anchor('airport/newOffice/' . $city_id, 'Wykup biuro');?></li><? } ?>
	</ul>
	<div class="clearfix"></div>
	<div class="thumbnail col-xs-6 col-md-4 col-lg-3">
		<div class="caption">
			<h3>Zbiorniki na paliwo lotnicze</h3>
			<h4>Posiadanych: <?=$lotnisko->cysterny;?></h4>
			<p>Pojemność zbiornika: <?=$tankVolume;?>l<br />
			Pojemność razem: <?=$lotnisko->cysterny * $tankVolume;?>l</p>
			<form method="post">
                <input type="hidden" name="module" value="kupZbiornik"/>
				<button class="btn btn-small btn-primary" name="typ" value="wal">Kup(<?=formatCash($zbiornik_cena_wal) . ' ' . WAL;?>)</button>
				<button class="btn btn-small btn-primary" name="typ" value="pkt">Kup(<?=formatCash($zbiornik_cena_pkt);?> pkt.)</button>
			</form>
		</div>
	</div>
    <? foreach($checkins as $ch)
    {
        echo '<div class="thumbnail col-xs-6 col-md-4 col-lg-3">
		            <div class="caption">';
        echo '<h3>Punkt odpraw</h3>';
        echo '<h4>'.(($ch->public == 1) ? 'Publiczny' : 'Prywatny').'</h4><br />';
        echo "Skrócony czas odpraw: ".($ch->level * $punktodpraw_perLevel)."%<br />";
        if($ch->public == 1)
        {
            echo "Cena (za 1 min odprawy): ". formatCash($ch->cost)." ".WAL."<br />";
            echo "Maksymalny czas rezerwacji: ". TimeFormat::secondsToText($ch->reservations, true, true)."<br />";
            echo "Maksymalny czas odprawy: ". TimeFormat::secondsToText($ch->maxCheckin, true, true)."<br />";
            echo "Minimalny czas odprawy: ". TimeFormat::secondsToText($ch->minCheckin, true, true)."<br />";
            echo "Pieniądze do zebrania: ". formatCash($ch->cash)." ".WAL."<br />";
            echo "Pieniądze zebrane: ". formatCash($ch->earned)." ".WAL."<br />";
        }
            $upgradeCost = $airport_costs[$ch->level] * $punktodpraw_upgrade;
            echo '<br /><form method="post">
                <input type="hidden" name="module" value="punktOdpraw"/>
                <input type="hidden" name="checkin" value="'.$ch->id.'"/>
                <button class="btn btn-small btn-success" name="typ" value="cash">Zbierz pieniądze</button>
                <button class="btn btn-small btn-primary" name="typ" value="activity">Pokaż aktywność</button>
                <button class="btn btn-small btn-primary" name="typ" value="public">'.(($ch->public == 1) ? "Zmień na: prywatny" : "Zmień na: publiczny").'</button>
                <button class="btn btn-small btn-primary" name="typ" value="settings">Zmień ustawienia</button>';
            if($ch->level < 5)
                echo '<button class="btn btn-small btn-primary" name="typ" value="upgrade">Ulepsz('.formatCash($upgradeCost).' '.WAL.')</button>';
            echo '</form>
            </div>
        </div>';
    } ?>
    <div class="thumbnail col-xs-6 col-md-4 col-lg-3">
        <div class="caption">
            <h3>Kup punkt odpraw</h3>
            <form method="post">
                <input type="hidden" name="module" value="kupPunktOdpraw"/>
                <button class="btn btn-small btn-primary" name="typ" value="wal">Kup(<?=formatCash($punktodpraw_cena_wal) . ' ' . WAL;?>)</button>
                <button class="btn btn-small btn-primary" name="typ" value="pkt">Kup(<?=formatCash($punktodpraw_cena_pkt);?> pkt.)</button>
            </form>
        </div>
    </div>
	<div class="clearfix"></div>
	<? if( ! isset($mainBase) || ! $mainBase) {
		echo '<div class="button-group">
				  <span class="button-group-addon">
					<input type="checkbox">
				  </span>';
		echo HTML::anchor('airport/leaveOffice/'.$office_id, '<button class="btn btn-small btn-danger" disabled="disabled"><i class="glyphicon glyphicon-remove"></i> Opuść biuro</button>');
		echo '</div><div class="clearfix"></div>';
	} ?>
</div>

<script>
	$(function() {
		$('.button-group input').click(function() {
			var btn = $(this).parent().parent().find('button');
			if($(this).is(':checked'))
				btn.removeAttr('disabled');
			else
				btn.attr('disabled', 'disabled');
		});
	});
</script>