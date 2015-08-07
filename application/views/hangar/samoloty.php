<div class="well">
	<div class="page-header">
		<h1>Twoje samoloty</h1>
	</div>
	<ul class="pagination pagination-xm">
		<li class='active'><?=HTML::anchor('samoloty', 'Samoloty');?>
		<li><?=HTML::anchor('warsztat', 'Warsztat');?>
	</ul>
	<div class="thumbnail">
	<table class='table table-striped'>
		<?=(!empty($planesText)) ? $planesText : "<tr><td>Nie posiadasz żadnych samolotów.<br />
		" . HTML::anchor('sklep', "Kup samolot", array('class' => 'btn btn-primary btn-medium'));?>
	</table>
	</div>
</div>