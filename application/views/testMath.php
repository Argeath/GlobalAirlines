<div class="well">
	<div class="page-header">
		<h1>Test</h1>
	</div>

	<ul class="filtry pagination pagination-sm">
		<?= $biuraText; ?>
	</ul>

	<ul class="filtry pagination pagination-sm" style="margin-left: 50px;">
		<?= $klasyText; ?>
	</ul>
	
	<div style="clear: both;"></div>
	
	<div class="well">
		<?= FORM::open(); ?>
		<b>Wzór na zlecenia</b><br />
		Zmienne: $D - dystans; $P - pasażerów; $V - średnia prędkość; $S - średnie spalanie; $R - średni zasięg; $M - średnio miejsc; $K - klasa(1-11); $B - biuro(1-12)<br />
		<div class="form-group <?= ($notWorkingZ) ? 'has-error' : ''; ?>">
		<input type="text" name="Wzlecenia" class="form-control" value="<?= $Wzlecenia; ?>"/>
		</div>
		<b>Wzór na opłaty dodatkowe</b><br />
		Zmienne: $D - dystans; $P - pasażerów; $M - miejsc; $S - spalanie; $Z - zasieg; $V - prędkość; $AV - średnia prędkość; $AS - średnie spalanie; $K - klasa(1-12); $B - biuro(1-10)<br />
		<div class="form-group <?= ($notWorkingO) ? 'has-error' : ''; ?>">
			<input type="text" name="Woplaty" class="form-control"  value="<?= $Woplaty; ?>"/>
		</div>
		<button class="btn btn-primary btn-small">Zapisz</button>
		</form>
		<b>Średni zarobek w klasie:<b/> <?= $averagePerHour.' '.WAL; ?><br />
		<b>Średni koszt w klasie:<b/> <?= $averageCost.' '.WAL; ?>
	</div>
	
	<div class="well">
		<table class="table table-striped">
			<thead>
			<tr>
				<th width="5%">ID</th>
				<th width="5%">Osób</th>
				<th width="10%">Dystans</th>
				<th width="12%" style="border-right: 2px solid #000;">Zarobek</th>
				<th width="5%">ID</th>
				<th width="5%">Osób</th>
				<th width="10%">Dystans</th>
				<th width="12%" style="border-right: 2px solid #000;">Zarobek</th>
				<th width="5%">ID</th>
				<th width="5%">Osób</th>
				<th width="10%">Dystans</th>
				<th style="border-right: 2px solid #000;">Zarobek</th>
			</tr>
			</thead>
			<tbody>
				<? 
				
				$poIle = 0;
				foreach($orders as $a)
				{
					if( ! isset($distArr[$a->from]) || ! isset($distArr[$a->from][$a->to]))
					{
						$from = ORM::factory("City", $a->from);
						if( ! isset($distArr[$a->from]))
							$distArr[$a->from] = $from->getDistances();
					}
					$distance = $distArr[$a->from][$a->to];
					if($poIle == 0)
						echo '<tr>';
					echo '<td class="order_'.$a->id.'">#'.$a->id.'</td>';
					echo '<td class="order_'.$a->id.'">'.$a->count.'</td>';
					echo '<td class="order_'.$a->id.'">'.formatCash($distance).'</td>';
					echo '<td class="order_'.$a->id.'" style="border-right: 2px solid #000;">'.formatCash($a->cash).' '.WAL.'</td>';
					if($poIle == 2) {
						echo '</tr>';
						$poIle = 0;
					} else
						$poIle++;
				} ?>
			</tbody>
		</table>
	</div>
	
	<? $width = $orders->count() * 100 + 270; ?>
	<div class="well" style="overflow-x: scroll;">
		<table class="table table-striped" style="width: <?= $width; ?>px;">
			<thead>
			<tr>
				<th width="270">Samolot</th>
				<? foreach($orders as $a) {
					echo "<th width='100'>#".$a->id."</th>";
				}?>
			</tr>
			</thead>
			<tbody>
				<? foreach($samoloty as $s)
				{
					echo '<tr>';
					echo '<td>'.$s['name'].'</td>';
					foreach($orders as $a) {
						echo "<td class='order_highlight' orderId='".$a->id."' style='font-size: 9px;'>".$s['orders'][$a->id]."</td>";
					}
					echo '</tr>';
				} ?>
			</tbody>
		</table>
	</div>
</div>


<script>
$(function() {
	$('.order_highlight').hover(function() {
		var orderId = $(this).attr('orderId');
		$('.order_' + orderId).addClass('actual');
	}, function() {
		var orderId = $(this).attr('orderId');
		$('.order_' + orderId).removeClass('actual');
	});
});
</script>