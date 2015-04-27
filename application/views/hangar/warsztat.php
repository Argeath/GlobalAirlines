<div class="well">
	<div class="page-header">
		<h1>Warsztat</h1>
	</div>
	<ul class="pagination pagination-xm">
		<li><?= HTML::anchor('samoloty', 'Samoloty'); ?>
		<li class='active'><?= HTML::anchor('warsztat', 'Warsztat'); ?>
	</ul>
	<div class="thumbnail">
	<table class='table table-striped'>
		<?= ( ! empty($planesText)) ? $planesText : "<tr><td>Nie posiadasz &#382;&#246;dnych samolotów. &#381;&#246;dnych.</td></tr>"; ?>
	</table>
	</div>
</div>