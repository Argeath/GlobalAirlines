<div class="well">
	<div class="page-header">
		<h1>Sklep</h1>
	</div>
	<ul class="pagination pagination-xm">
		<li <?=($action == 'index') ? "class='active'" : "";?>><?=HTML::anchor('sklep/' . $klasa . '/index', 'Sklep');?>
		<li <?=($action == 'aukcje') ? "class='active'" : "";?>><?=HTML::anchor('sklep/' . $klasa . '/aukcje', 'Aukcje');?>
</ul>



	<ul class="filtry pagination pagination-sm">
<?=$klasyText;?>
		<? if($action=='aukcje') { ?><li <?=($klasa == 15) ? "class='active'" : "";?>><?=HTML::anchor('sklep/15/' . $action, 'Moje Aukcje');?></li><? } ?>
		<? if($action=='aukcje') { ?><li <?=($klasa == 16) ? "class='active'" : "";?>><?=HTML::anchor('sklep/16/' . $action, 'Zakończone');?></li><? } ?>
	</ul>

	<div style="clear: both;"></div>
	<div class="thumbnail">
		<table class="table table-striped">
			<thead>
			<tr>
				<th width="15%">Samolot</th>
				<th>Parametry</th>
				<th>Załoga</th>
<?=(($action == 'aukcje') ? '<th>Stan</th>' : '')?>
				<th width="15%"><?=($action == 'aukcje') ? "Opcje" : "Kup"?></th>
			</tr>
			</thead>
			<tbody>
			<?
				if( ! empty($planesData)) {
					foreach($planesData as $data) {
						echo "<tr>
							<td><img src='" . URL::base(TRUE) . "assets/samoloty/" . $data['plane']->id . ".jpg' class='img-rounded hidden-xs' style='width: 100px;'/><br />" . $data['plane']->producent . " " . $data['plane']->model . "</td>
							<td>
								<div class='text-rounded " . (($data['plane']->wyrozZasieg == 1) ? 'bg-orange' : 'bg-blue') . " Jtooltip inline' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Zasięg samolotu" . (($data['plane']->wyrozZasieg == 1) ? $warning : '') . "' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-arrows-h'></i> " . $data['plane']->zasieg . " km</div>
								<div class='text-rounded " . (($data['plane']->wyrozMiejsca == 1) ? 'bg-orange' : 'bg-blue') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Miejsca pasażerskie" . (($data['plane']->wyrozMiejsca == 1) ? $warning : '') . "' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-users'></i> " . $data['plane']->miejsc . "</div>
								<div class='text-rounded " . (($data['plane']->wyrozSpalanie == 1) ? 'bg-orange' : 'bg-blue') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Spalanie silników" . (($data['plane']->wyrozSpalanie == 1) ? $warning : '') . "' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-fire'></i> " . $data['plane']->spalanie . " kg/km</div>
								<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Prędkość maksymalna' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-tachometer'></i> " . $data['plane']->predkosc . " km/h</div>
							</td>
							<td>
								<div class='text-rounded " . (($data['plane']->wyrozPilot == 1) ? 'bg-orange' : 'bg-blue') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Ilość wymaganych pilotów" . (($data['plane']->wyrozPilot == 1) ? $warning : '') . "' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-user'></i> " . $data['plane']->piloci . "</div>
								<div class='text-rounded " . (($data['plane']->wyrozStewardessa == 1) ? 'bg-orange' : 'bg-blue') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Ilość wymaganych stewardess" . (($data['plane']->wyrozStewardessa == 1) ? $warning : '') . "' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-female'></i> " . $data['plane']->zaloga_dodatkowa . "</div>
								<div class='text-rounded " . (($data['plane']->wyrozMechanik == 1) ? 'bg-orange' : 'bg-blue') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Mnożnik serwisowy" . (($data['plane']->wyrozMechanik == 1) ? $warning : '') . "' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='glyphicon glyphicon-wrench'></i> x" . $data['plane']->mechanicy . "</div>
							</td>
							<td width='10%' class='shopBuyTd'>";
                        if($data['level'] < $data['requiredLevel'])
                            echo "<div class='blockedShop'><i class='fa fa-lock'></i><br />Wymagany poziom: ".$data['requiredLevel']."</div>";
                        echo "<form method='post'><input type='hidden' name='plane' value='" . $data['plane']->id . "'/>
                                " . Prints::rusureButton(formatCash($data['plane']->cena) . ' ' . WAL, 'action', 'buy_wal', ['btn-primary']) . "
                                " . Prints::rusureButton(formatCash(round(sqrt($data['plane']->cena) / 40) * 20) . ' PP', 'action', 'buy_pkt', ['btn-success']) . "
                            </form>
                        </td>
                        </tr>";
					}
				} else {
                    echo "<tr><td colspan='4'>Brak</td></tr>";
                }
			?>
			</tbody>
		</table>
	</div>

	<?
	if($ilosc > $naStrone)
	{
		echo '<ul class="pagination">';
		$iloscStron = ceil($ilosc / $naStrone);
		$max = 9;
		$przesuniecie = paginationPrzesuniecie($iloscStron, $strona, $max);
		if($iloscStron < ($przesuniecie + $max))
			$max = $iloscStron - $przesuniecie;
		for($i=$przesuniecie; $i < $przesuniecie+$max; $i++)
		{
			$active = ($strona == $i) ? 'class="active"' : '';
			echo '<li '.$active.'>'.HTML::anchor('sklep/'.$klasa.'/'.$action.'/'.($i+1), ($i+1)).'</li>';
		}
		echo '</ul>';
	}
	?>
</div>