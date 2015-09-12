<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Poczta</h1>
			</div>
			<ul class="pagination pagination-sm">
			<li <?= ($typ==null || $typ==1) ? "class='active'" : ""; ?>><?= HTML::anchor('poczta/index/1', 'Odebrane'); ?></li>
			<li <?= ($typ==2) ? "class='active'" : ""; ?>><?= HTML::anchor('poczta/index/2', 'Wysłane'); ?></li>
			<li><?= HTML::anchor('poczta/new', 'Napisz wiadomość'); ?></li>
			</ul><br /><br />
			<?= Form::open('poczta/delete'); ?>
			<table class="table table-striped">
			<thead>
				<tr>
					<th>Zaznacz</th>
					<th>Data</th>
					<th>Nadawca/Odbiorca</th>
					<th>Wiadomość</th>
				</tr>
			</thead>
			<tbody>
				<?= isset($messages) ? $messages : '<tr><td colspan="4">Brak wiadomości</td></tr>'; ?>
			</tbody>
			</table>

			<div style="text-align: left;">
				Zaznaczone: <a onclick="$('form').submit();" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span>Usuń</a>
			</div>

			<?= Form::close(); ?>

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
					echo '<li '.$active.'>'.HTML::anchor('poczta/index/'.$typ.'/'.($i+1), ($i+1)).'</li>';
				}

				echo '</ul>';

			}
			?>
		</div>
	</div>
</div>