<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Podjęte zlecenia</h1>
			</div>
			<table style="width: 100%;" class="table table-striped">
			<thead>
				<tr>
					<th>Z</th>
					<th>Do</th>
					<th>Zapłata</th>
					<th>Pasażerów</th>
					<th class="hidden-xs">Deadline</th>
					<th>Wykonaj</th>
				</tr>
			</thead>
			<tbody>
			<?
			if( ! empty($ordersData)) {
				foreach($ordersData as $data) {
					echo "<tr>
						<td>" . Helper_Map::getCityName($data['order']->from) . "</td>
						<td>" . Helper_Map::getCityName($data['order']->to) . "</td>
						<td>" . formatCash($data['order']->cash) . " " . WAL . "</td>
						<td>" . $data['order']->count . "</td>
						<td class='hidden-xs'>" . Helper_TimeFormat::timestampToText($data['order']->deadline) . "</td>
						<td>
							" . Form::open('zlecenie/plane') . "
							<input type='hidden' name='zlecenie' value='" . $data['zlecenie']->id . "'/>
							" . Form::submit('send', 'Wykonaj', array('class' => "btn btn-primary"))
							. "</form></td>
					</tr>";
				}
			} else {
				echo "<tr><td colspan='7'>Brak zleceń</td></tr>";
			}
			?>
			</tbody>
			</table>
			<br />
			<div class="alert alert-info">
				Można anulować wysłany lot do <b>30 minut</b> przed rozpoczęciem odprawy. Aby odwołać lot wejdź w <b>Terminarz</b>, wybierz konkretny lot i kliknij <b>Odwołaj</b>.
			</div>
		</div>
	</div>
</div>