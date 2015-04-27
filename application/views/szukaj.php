<div class="well">
	<div class="page-header">
		<h1>Znalezieni gracze</h1>
	</div>
	<?= $players; ?>
	<div class="clearfix"></div>
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
			echo '<li '.$active.'>'.HTML::anchor('profil/znajdz/'.$nick.'/'.($i+1), ($i+1)).'</li>';
		}
	
		echo '</ul>';
	
	}
	?>
</div>