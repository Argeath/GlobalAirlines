<div class="well">
	<div class="page-header">
		<h1>Twoje samoloty</h1>
	</div>
	<ul class="pagination pagination-xm">
		<li class='active'><?= HTML::anchor('samoloty', 'Samoloty'); ?>
		<li><?= HTML::anchor('warsztat', 'Warsztat'); ?>
	</ul>
	<div class="thumbnail">
	<table class='table table-striped'>
		<?= ( ! empty($planesText)) ? $planesText : "<tr><td>Nie posiadasz żadnych samolotów.</td></tr>"; ?>
	</table>
	</div>
</div>