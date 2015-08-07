<div class="well">
	<div class="page-header">
		<h1>Kontakty</h1>
	</div>
	Aby dodać nowy kontakt, wejdź w profil danej osoby i kliknij "Dodaj kontakt".<br /><br />
	<?
        echo "<h4>Propozycje:</h4>";
        if( ! empty($unaccepted)) {
            foreach ($unaccepted as $con) {
                $gracz = $con->user;
                $avatar = $gracz->getAvatar();
                echo '<div class="contact thumbnail">' . HTML::anchor('profil/' . $gracz->id, $gracz->username) . '<br /><img src="' . $avatar . '"/><br />';
                echo Form::open('kontakty') . '
                    <input type="hidden" name="typ" value="akceptuj"/>
                    <input type="hidden" name="new" value="' . $gracz->id . '"/>

                    ' . Form::submit('create', 'Akceptuj', array('class' => "btn btn-primary btn-block")) . '
                    ' . Form::close();
                echo Form::open('kontakty') . '
                    <input type="hidden" name="typ" value="odrzuc"/>
                    <input type="hidden" name="new" value="' . $gracz->id . '"/>

                    ' . Form::submit('create', 'Odrzuć', array('class' => "btn btn-primary btn-block")) . '
                    ' . Form::close() . '</div>'; //Zmienic na czerwony kolor buttona
            }
            echo "<div class='clearfix'></div>";
        } else {
            echo "Brak propozycji.";
        }
	?>
	
	<?
        echo "<h4 style='margin-top: 25px;'>Twoje kontakty:</h4>";
        if( ! empty($contacts)) {
            foreach ($contacts as $con) {
                $gracz = NULL;
                if ($con->user_id == $user->id)
                    $gracz = $con->user2;
                else
                    $gracz = $con->user;
                $avatar = $gracz->getAvatar();
                $refT = "";
                if ($gracz->referrer_id == $user->id || $user->referrer_id == $gracz->id)
                    $refT = '<i class="fa fa-share-alt Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="Polecony/Polecający" style="position: absolute; top: 7px; right: 10px;"></i>';
                $this->contactsT .= '<div class="contact thumbnail" style="position: relative;">' . $gracz->drawButton() . ' ' . $refT . '<br /><img src="' . $avatar . '" style="width: 100px; height: 100px;" class="img-thumbnail"/>' . HTML::anchor('poczta/new/' . $gracz->id, "<i class='glyphicon glyphicon-envelope'></i> Wyślij wiadomość", array('class' => 'btn btn-xs btn-primary')) . '</div>';
            }
            echo "<div class='clearfix'></div>";
        } else {
            echo "Brak kontaktów.";
        }
	?>
</div>