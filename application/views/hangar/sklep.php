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
<?=$samoloty;?>
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