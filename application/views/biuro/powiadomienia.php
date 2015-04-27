<div class="well">
	<div class="page-header">
		<h1>Powiadomienia</h1>
	</div>
	<table class="table table-striped" id="powiadomienia">
		<tbody>
			<?
			foreach($powiadomienia as $k => $u)
			{
				$dzien = strftime("%A, %d.%m.%Y", $u->data);
				$godzina = strftime("%R", $u->data);
				echo "<tr ".(($u->checked == 0) ? 'class="actual"' : '').">";
				echo '<td>'.$dzien.'</td>';
				echo '<td>'.$godzina.'</td>';
				echo '<td>'.$u->long.'</td>';
			}
			if(empty($powiadomienia))
				echo '<tr><td colspan="7">Brak</td></tr>';
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
			echo '<li '.$active.'>'.HTML::anchor('powiadomienia/'.($i+1), ($i+1)).'</li>';
		}
	
		echo '</ul>';
	
	}
	?>
</div>

<script>
$(function() {
    var $rows = $('#powiadomienia tbody tr');
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