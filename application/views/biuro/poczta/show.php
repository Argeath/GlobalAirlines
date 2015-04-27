<div class="well">
	<div class="page-header">
		<h1>Poczta <small>Wiadomość</small></h1>
	</div>
	<table class="table table-striped">
	<tr>
		<th>Data</th>
		<td><?= timestampToText($message->data, true); ?></td>
	</tr>
	<tr>
		<th>Użytkownicy</th>
		<td>
			<? if($message->typ == 1)
				echo $sender->drawButton().' => Ty';
			elseif($message->typ == 2)
				echo 'Ty -> '.$sender->drawButton(); ?>
		</td>
	</tr>
	<tr>
		<th>Tytuł</th>
		<td><?= $message->title; ?></td>
	</tr>
	<tr>
		<th colspan="2">Wiadomość</th>
	</tr>
	<tr>
		<td colspan="2"><?= $message->message; ?></td>
	<tr>
	</table>
	<?= HTML::anchor('poczta/index/'.$message->typ, '<i class="glyphicon glyphicon-step-backward"></i> Wróć', array( 'class' => "btn btn-default btn-xs")); ?>
	<?= HTML::anchor('poczta/new/'.$message->sender, 'Odpowiedz <i class="glyphicon glyphicon-share"></i>', array( 'class' => "btn btn-default btn-xs")); ?>
</div>