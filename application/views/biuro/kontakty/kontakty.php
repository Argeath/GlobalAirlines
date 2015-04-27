<div class="well">
	<div class="page-header">
		<h1>Kontakty</h1>
	</div>
	Aby dodać nowy kontakt, wejdź w profil danej osoby i kliknij "Dodaj kontakt".<br /><br />
	<? if( ! empty($unaccepted))
		echo "<h4>Propozycje:</h4>";
		echo $unaccepted;
		echo "<div class='clearfix'></div>";
	?>
	
	<? if( ! empty($contacts))
		echo "<h4>Twoje kontakty:</h4>";
		echo $contacts;
		echo "<div class='clearfix'></div>";
	?>
</div>