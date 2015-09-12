<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Kup paliwo <small><?= $baza->getName(); ?></small></h1>
			</div>
			<div class="col-md-6 col-xs-12">
				<?= Form::open('paliwo'); ?>
				<input type="hidden" name="bazaId" value="<?= $baza->id; ?>"/>
				<div class="input-group" style="margin-top: 35px;">
					<input type="text" name="ilosc" class="form-control" placeholder="Max. <?= formatCash($max, 2); ?>"/>
					<span class="input-group-btn">
						<?= Form::submit('send', 'Kup', array( 'class' => "btn btn-default")); ?>
					</span>
				</div>
				<?= Form::close(); ?>
			</div>
			<div class="col-md-6 col-xs-12 well">
				<h2>Aktualna cena paliwa:<br />
				<b><?= formatCash(Helper_Oil::getOilCost(), 2); ?> <?= WAL; ?></b></h2>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>