<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Dziennik Finansowy</h1>
			</div>
			<?= HTML::anchor('summation', 'Podsumowanie', array('class' => 'btn btn-primary btn-block', 'id' => 'dziennik-podsumowanie')); ?><br />
			<table class="table table-striped" id="journal">
				<thead>
				<tr>
					<th>Dzień</th>
					<th>Godzina</th>
					<th>Opis</th>
					<th id="dziennik-zmiana">Zmiana</th>
					<th>Stan konta</th>
				</tr>
				</thead>
				<tbody>
					<? if(isset($financials) && ! empty($financials)) {
							$grubszaKasa = pow(GlobalVars::$profil['cash'], 0.75);
							foreach($financials as $f) {
								$grubsza = "";
								if($f['change'] > $grubszaKasa || $f['change'] < -$grubszaKasa)
									$grubsza = "font-weight: 700;";
								echo '<tr>';
								echo (($f['byl']) ? '<td style="border-top: 0;"></td>' : ('<td>'.$f['dzien'].'</td>'));
								echo '<td>'.$f['godzina'].'</td>';
								echo '<td>'.$f['description'].'</td>';
								echo '<td>'.(($f['change'] >= 0) ? ("<span style='color: green; ".$grubsza."'>+".formatCash($f['change'])." ".WAL."</span>") : ("<span style='color: red; ".$grubsza."'>".formatCash($f['change'])." ".WAL."</span>")).'</td>';
								echo '<td>'.(($f['balance'] >= 0) ? (formatCash($f['balance'])." ".WAL) : ("<span style='color: red;'>".formatCash($f['balance'])." ".WAL."</span>")).'</td>';

								echo '</tr>';
							}
						} else
							echo '<tr><td colspan="5">Nie odnotowano zmian.</td></tr>';
					?>
				</tbody>
			</table>
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
					echo '<li '.$active.'>'.HTML::anchor('journal/'.($i+1), ($i+1)).'</li>';
				}

				echo '</ul>';

			}
			?>
		</div>
	</div>
</div>

<script>
$(function() {
    var n1 = new tutorialElement($('#dziennik-podsumowanie'), "Site_Top", "Site_Top", "Podsumowanie finansowe", "inner", false);
    var n2 = new tutorialElement($('#dziennik-zmiana'), "Hard_Top_Right", "Site_Top", "Zmiany stanu pieniędzy lub punktów premium konta", "inner", false);
    window.tutorialElements.push(n1, n2);

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
});
</script>