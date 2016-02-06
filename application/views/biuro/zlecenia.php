<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Podjęte zlecenia</h1>
			</div>
			<table style="width: 100%;" class="table table-striped">
			<thead>
				<tr>
					<th><span id="tutorial_from">Z</span></th>
					<th><span id="tutorial_to">Do</span></th>
					<th><span id="tutorial_cash">Zapłata</span></th>
					<th>Pasażerów</th>
					<th class="hidden-xs" id="tutorial_deadline">Deadline</th>
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

<script>
	$(function() {
		var n1 = new tutorialElement($('#tutorial_from'), "Hard_Bottom_Left", "Site_Bottom", "Lotnisko, z którego samolot ma wystartować", "inner", false);
		var n2 = new tutorialElement($('#tutorial_to'), "Site_Top", "Site_Top", "Lotnisko, na którym samolot ma wylądować", "inner", false);
		var n3 = new tutorialElement($('#tutorial_cash'), "Hard_Top_Left", "Site_Bottom", "Ilość pieniędzy, jakie gracz otrzyma za zlecenie (Nie wliczono opłat za lot)", "inner", false);
		var n4 = new tutorialElement($('#tutorial_deadline'), "Site_Top", "Site_Top", "Termin po jakim gracz zostanie ukarany kwotą pieniężną, jeżeli do tego czasu samolot nie wyląduje", "inner", false);
		window.tutorialElements.push(n1, n2, n3, n4);
	});
</script>