<div class="well">
	<div class="page-header">
		<h1>Podgląd</h1>
	</div>
	<div class="col-lg-6 board" style="border: 2px #FFF solid;">
		<h4 class="title"><span id="tutorial_odloty">Odloty</span></h4>
		<table style="width: 100%;">
			<thead>
				<th style="width: 50px;">Typ</th>
				<th width="15%">Samolot</th>
				<th width="15%">Z</th>
				<th width="15%">Do</th>
				<th width="25%">Czas przylotu</th>
				<th>POZOSTALO</th>
			</thead>
			<tbody>
<?=$odlotyText;?>
</tbody>
		</table>
	</div>
	<div class="col-lg-6 board" style="border: 2px #FFF solid;">
		<h4 class="title">W powietrzu</h4>
		<table style="width: 100%;">
			<thead>
				<th style="width: 50px;">Typ</th>
				<th width="15%">Samolot</th>
				<th width="15%">Z</th>
				<th width="15%" id="tutorial_loty">Do</th>
				<th width="25%">Czas przylotu</th>
				<th>Pozostalo</th>
			</thead>
			<tbody>
<?=$flightsText;?>
</tbody>
		</table>
	</div>
	<div class="clearfix"></div>

	<? if( ! isset($otherEmpty) || $otherEmpty == false) { ?>
	<div class="col-md-12 board" style="border: 2px #FFF solid;">
		<h4 class="title">Inne wydarzenia</h4>
		<table style="width: 100%;">
			<thead>
				<th>Typ</th>
				<th width="30%">Pozostalo</th>
			</thead>
			<tbody style="color: black;">
<?=$otherText;?>
</tbody>
		</table>
	</div>
	<div class="clearfix"></div>
	<? } ?>
</div>

<div class="col-lg-6 well">
	<div class="page-header">
		<h3><span id="tutorial_dziennik">Dziennik finansowy</span></h3>
	</div>
	<table class="table table-striped" id="journal">
		<thead>
		<tr>
			<th width="10%">Dzień</th>
			<th width="5%">Godzina</th>
			<th>Opis</th>
			<th width="15%">Zmiana</th>
			<th width="15%">Stan konta</th>
		</tr>
		</thead>
		<tbody>
			<? if(isset($financials) && ! empty($financials)) {
					$grubszaKasa = pow($profil['cash'], 0.75);
					foreach($financials as $f) {
						$grubsza = "";
						if($f['change'] > $grubszaKasa || $f['change'] < -$grubszaKasa)
							$grubsza = "font-weight: 700;";
                        $wal = $f['wal'];
						echo '<tr>';
						echo (($f['byl']) ? '<td style="border-top: 0;"></td>' : ('<td>'.$f['dzien'].'</td>'));
						echo '<td>'.$f['godzina'].'</td>';
						echo '<td style="font-size: 10px;">'.$f['description'].'</td>';
						echo '<td>'.(($f['change'] >= 0) ? ("<span style='color: green; ".$grubsza."'>+".formatCash($f['change'])." ".$wal."</span>") : ("<span style='color: red; ".$grubsza."'>".formatCash($f['change'])." ".$wal."</span>")).'</td>';
						echo '<td>'.(($f['balance'] >= 0) ? (formatCash($f['balance'])." ".$wal) : ("<span style='color: red;'>".formatCash($f['balance'])." ".$wal."</span>")).'</td>';

						echo '</tr>';
					}
				} else
					echo '<tr><td></td><td colspan="5">Nie odnotowano zmian.</td></tr>';
			?>
		</tbody>
	</table>
<?=HTML::anchor('journal', 'Zobacz cały dziennik', array('class' => 'btn btn-default col-xs-12 col-sm-6'));?>
	<?=HTML::anchor('summation', 'Podsumowanie', array('id' => 'tutorial_podsumowanie', 'class' => 'btn btn-primary col-xs-12 col-sm-6'));?>
	<div class="clearfix"></div>
</div>

<div class="col-lg-6 well notepad">
	<div class="page-header">
		<h3><span id="tutorial_notatnik">Notatnik</span></h3>
	</div>
	<div class="top">
		<div class="delete"><i class="glyphicon glyphicon-remove"></i></div>
		<ul class="tabs" onselectstart="return false" onselect="return false">

			<li class="new"><span>Nowy...</span></li>
		</ul>
	</div>
	<div class="clearfix"></div>
	<div style="position: relative;">
		<textarea class="content" style="height: 250px;"></textarea>
		<div class="status"><i class="glyphicon"></i></div>
	</div>
</div>

<script type="text/javascript" src="<?=URL::base(TRUE);?>assets/js/notepad.js"></script>

<script>
$(function() {
    var $rows = $('#journal tbody tr');
    var items = [],
        itemtext = [],
        currGroupStartIdx = 0;
    $rows.each(function(i) {
        var $this = $(this);
        var itemCell = $(this).find('td:eq(0)')
        var item = itemCell.text();
        itemCell.remove();
        if ($.inArray(item, itemtext) === -1) {
            itemtext.push(item);
            items.push([i, item]);
            groupRowSpan = 1;
            currGroupStartIdx = i;
            $this.data('rowspan', 1)
        } else {
            var rowspan = $rows.eq(currGroupStartIdx).data('rowspan') + 1;
            $rows.eq(currGroupStartIdx).data('rowspan', rowspan);
        }
    });
    $.each(items, function(i) {
        var $row = $rows.eq(this[0]);
        var rowspan = $row.data('rowspan');
        $row.prepend('<td rowspan="' + rowspan + '">' + this[1] + '</td>');
    });

	var n1 = new tutorialElement($('#tutorial_odloty'), "Hard_Bottom_Left", "Site_Bottom", "Odprawy oraz zaplanowane odloty", "inner", false);
	var n2 = new tutorialElement($('#tutorial_loty'), "Hard_Top_Right", "Hard_Top_Right", "Samoloty w powietrzu", "inner", false);
	var n3 = new tutorialElement($('#tutorial_dziennik'), "Site_Top", "Site_Top", "Dziennik, wyświetlający wszystkie ostatnie zmiany stanu konta", "inner", false);
	var n4 = new tutorialElement($('#tutorial_notatnik'), "Site_Top", "Site_Top", "Prywatny notatnik do użytku gracza", "inner", false);
	var n5 = new tutorialElement($('#tutorial_podsumowanie'), "Site_Left", "Site_Top", "Podsumowanie finansowe gracza z wybranego okresu czasu", "inner", false);
	window.tutorialElements.push(n1, n2, n3, n4, n5);
});
</script>