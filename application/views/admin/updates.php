<div class="well">
	<div class="page-header">
		<h1>Czasy updatów</h1>
	</div>
	
	<table class="table">
		<thead>
		<tr>
			<th>Nazwa</th>
			<th>Średni czas</th>
			<th>Ilość wykonanych</th>
			<th>Ostatni</th>
			<th>Liczone od</th>
		</tr>
		</thead>
		<tbody>
			<?
				if( ! empty($eves))
				{
					foreach($eves as $e)
					{
						$sredni = ($e['suma'] / $e['ilosc'])*1000;
						echo "<tr>";
						echo "<td>".eventTypeToName($e['id'])."</td>";
						echo "<td>".round($sredni)."ms</td>";
						echo "<td>".$e['ilosc']."</td>";
						echo "<td>".TimeFormat::timestampToText($e['lastTime'])."</td>";
						echo "<td>".TimeFormat::timestampToText($e['startOfWeek'])."</td>";
					}				
				} else
					echo "<tr><td colspan='5'>Brak</td></tr>";
			?>
		</tbody>
	</table>
</div>