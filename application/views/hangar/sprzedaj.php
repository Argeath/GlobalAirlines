<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Samolot <small>Sprzedaż</small></h1>
			</div>
			<form method="post" class="form_300 well">
				Dostaniesz za niego: <?=formatCash($kasa) . " " . WAL;?>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="sprzedaj" value="tak"/>Czy jesteś pewien?
					</label>
				</div>
				<button class="btn btn-primary btn-block">Sprzedaj</button>

			</form>
		</div>
	</div>
</div>