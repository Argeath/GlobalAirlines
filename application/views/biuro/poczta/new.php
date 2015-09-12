<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Poczta <small>Nowa wiadomość</small></h1>
			</div>
			<ul class="pagination pagination-sm">
				<li><?= HTML::anchor('poczta/index/1', 'Odebrane'); ?></li>
				<li><?= HTML::anchor('poczta/index/2', 'Wysłane'); ?></li>
				<li class='active'><?= HTML::anchor('poczta/new', 'Napisz wiadomość'); ?></li>
				</ul><br /><br />
			<?= Form::open('poczta/new'); ?>
			<div class="col-md-4">
				<div class="input-group">
					<div class="input-group-btn select-group left-group">
						<select name='receiver' class="form-control">
							<option></option>
							<?= $contacts; ?>
						</select>
					</div>
					<input type="text" name='receiver2' class="form-control" placeholder="Wybierz z kontaktów lub wpisz nick" <? if(!empty($nickname)) echo 'value="'.$nickname.'"'; ?>/>
				</div>
			</div>
			<div class="col-md-8">
				<input type="text" name='title' class="form-control" placeholder="Tytuł wiadomości"/>
			</div>
			<div class="col-xs-12">
				<textarea name='message' style="width: 100%; max-width: 100%; min-height: 200px;" class="form-control" placeholder="Wiadomość"></textarea>

				<?= Form::submit('send', 'Wyślij', array( 'class' => "btn btn-primary btn-block")); ?>
			</div>
			<?= Form::close(); ?>
			<div class="clearfix"></div>
			<br />
			<?= HTML::anchor('poczta/index/1', '<i class="glyphicon glyphicon-step-backward"></i>Wróć', array( 'class' => "btn btn-default btn-xs")); ?>
		</div>
	</div>
</div>