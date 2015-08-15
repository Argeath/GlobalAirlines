<div class="well">
	<div class="page-header">
		<h1>Ranking</h1>
	</div>
	<ul class="pagination pagination-xm">
	  <li <?= ($typ==null || $typ==0) ? "class='active'" : ""; ?>><?= HTML::anchor('ranking', 'wg. poziomu'); ?></li>
	  <li <?= ($typ==1) ? "class='active'" : ""; ?>><?= HTML::anchor('ranking/1', 'wg. przeleconych km'); ?></li>
	  <li <?= ($typ==2) ? "class='active'" : ""; ?>><?= HTML::anchor('ranking/2', 'wg. czasu w powietrzu'); ?></li>
	  <li <?= ($typ==3) ? "class='active'" : ""; ?>><?= HTML::anchor('ranking/3', 'wg. przewiezonych pasażerów'); ?></li>
	  <li <?= ($typ==4) ? "class='active'" : ""; ?>><?= HTML::anchor('ranking/4', 'wg. wykonanych zleceń'); ?></li>
	  <li <?= ($typ==5) ? "class='active'" : ""; ?>><?= HTML::anchor('ranking/5', 'wg. niewykonanych zleceń'); ?></li>
	  <li <?= ($typ==6) ? "class='active'" : ""; ?>><?= HTML::anchor('ranking/6', 'wg. ilości wypadków'); ?></li>
	</ul>
	<div class="clearfix"></div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Lp.</th>
				<th>Gracz</th>
				<th>Poziom doświadczenia</th>
				<th>Przeleconych km</th>
				<th>Czasu w powietrzu</th>
				<th>Przewiezionych pasażerów</th>
				<th>Wykonanych zleceń</th>
				<th>Niewykonanych zleceń</th>
				<th>Wypadków</th>
			</tr>		
		</thead>
		<tbody>
			<?
			foreach($users as $k => $u)
			{
				$lp = $k + 1 + $offset;
				echo "<tr ".(( ($zaznacz > 0 && $lp == $zaznacz) || ($zaznacz == 0 && $lp==$miejsce)) ? 'class="actual"' : '')."><td>".$lp."</td>";
				$avatar = $u->getAvatar();
				echo '<td><img src="'.$avatar.'" style="width: 100px; height: 100px;" class="img-thumbnail"/><br />'.$u->drawButton().'</td>';
				echo '<td>'.Helper_Experience::getLevelByExp($u->exp).' poz.</td>';
				echo '<td>'.formatCash($u->km, 0).' km</td>';
				echo '<td>'.Helper_TimeFormat::secondsToText($u->hours, true, true).'</td>';
				echo '<td>'.formatCash($u->pasazerow, 0).'</td>';
				echo '<td>'.formatCash($u->zlecen, 0).'</td>';
				echo '<td>'.formatCash($u->niewykonanych, 0).'</td>';
				echo '<td>'.formatCash($u->wypadkow, 0).'</td>';
			}
			if(empty($users))
				echo '<tr><td colspan="7">Brak</td></tr>';
			?>
		</tbody>
	</table>
	<div class="well col-xs-12 col-md-5">
		<?= Form::open(); ?>
			<div class="col-lg-4">
				<?= Form::input('nick', $szukany, array( 'class' => "form-control", 'placeholder' => "Znajdź gracza" )); ?>
			</div>
			<input type="submit" name="ZnajdzGracza" value="Znajdź gracza" class="btn btn-primary btn-medium col-lg-4"/>
			<input type="submit" name="ZnajdzGracza" value="Znajdź siebie" class="btn btn-success btn-medium col-lg-3 col-xs-offset-1"/>
			
		</form>
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
			echo '<li '.$active.'>'.HTML::anchor('ranking/'.$typ.'/'.($i+1), ($i+1)).'</li>';
		}
		echo '</ul>';
	}
	?>
	<div class="clearfix"></div>
</div>