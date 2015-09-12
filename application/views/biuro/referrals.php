<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>System Poleconych</h1>
			</div>

			<div class="well well-with-separators">
				<div class="col-md-4">
					<h3>O systemie</h3>
					System poleconych polega na <b>zapraszaniu znajomych</b> do gry z <b>korzyścią dla wszystkich</b>.
					Wystarczy, że ktoś <b>zarejestruje się</b>, korzystając z twojego <b>linku polecającego</b>.
					<br />
					<br />
					Twój link polecający:<br />
					<input type="text" value="<?= $url; ?>" class="form-control"/>
				</div>
				<div class="col-md-4 middle">
					<h3>Korzyści dla Zapraszającego</h3>
					<ul style="list-style-type: none;">
						<li>Po osiągnięciu <b>10</b> poziomu przez zaproszonego - <b>20 pkt. premium</b></li>
						<li>Po osiągnięciu <b>20</b> poziomu przez zaproszonego - <b>40 pkt. premium</b></li>
						<li>Po osiągnięciu <b>30</b> poziomu przez zaproszonego - <b>60 pkt. premium</b></li>
						<li>Po osiągnięciu <b>40</b> poziomu przez zaproszonego - <b>80 pkt. premium</b></li>
						<li>Po osiągnięciu <b>50</b> poziomu przez zaproszonego - <b>100 pkt. premium</b></li>
					</ul>
				</div>
				<div class="col-md-4">
					<h3>Korzyści dla Zaproszonego</h3>
					<ul style="list-style-type: none;">
						<li>Dodatkowe <b>25 000 <?= WAL ?></b> na początek gry</li>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>

			<div class="well col-md-4">
				<h3>"Kamienie milowe"</h3>
				<b>Nagrody</b> jednorazowe, przyznawane, gdy odpowiednia liczba poleconych osiągnie <b>10 poziom</b>.<br />
				<ul class="milestones well">
					<li <?= ($milestones >= 1) ? 'class="active"' : 'class="not-active"' ?>><span>1</span> <b>100</b> Punktów Premium</li>
					<li <?= ($milestones >= 5) ? 'class="active"' : 'class="not-active"' ?>><span>5</span> <b>200</b> Punktów Premium</li>
					<li <?= ($milestones >= 10) ? 'class="active"' : 'class="not-active"' ?>><span>10</span> Bombardier CRJ 700</li>
					<li <?= ($milestones >= 15) ? 'class="active"' : 'class="not-active"' ?>><span>15</span> 500 000 <?= WAL ?></li>
					<li <?= ($milestones >= 20) ? 'class="active"' : 'class="not-active"' ?>><span>20</span> Airbus A330-200</li>
					<li <?= ($milestones >= 30) ? 'class="active"' : 'class="not-active"' ?>><span>30</span> 5 000 000 <?= WAL ?></li>
					<li <?= ($milestones >= 50) ? 'class="active"' : 'class="not-active"' ?>><span>50</span> Boeing B747-8</li>
				</ul>
			</div>

			<div class="well col-md-8">
				<table class="table table-striped">
				<thead>
					<tr>
						<th>Gracz</th>
						<th style="width: 30%;">Poziom</th>
						<th style="width: 30%;">Zdobytych Punktów Premium za gracza</th>
					</tr>
				</thead>
				<tbody>
					<? foreach($referrals as $ref) {
						echo '<tr>';
							echo '<td>'.$ref->drawButton().'</td>';
							echo '<td>'.$ref->getLevel().'</td>';
							echo '<td>'.formatCash($ref->referred_points).'</td>';

						echo '</tr>';
					}
					if($referrals->count() == 0)
						echo '<tr><td colspan="3">Nie masz żadnych poleconych.</td></tr>';

					?>
				</tbody>
				</table>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>