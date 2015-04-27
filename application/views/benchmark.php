<div class="well">
	<? if(isset($durations) && ! empty($durations))
	{
		foreach($durations as $k => $v)
			echo 'Wynik ['.$k.']: '.round($v, 5).'<br />';
	} ?>
</div>










<? /*
<div class="well">
	<table id="t" class="col-sm-6" style="FONT-SIZE: 9.75pt; FONT-FAMILY: &quot;Microsoft Sans Serif&quot;" bordercolor="#000000" cellspacing="0" cellpadding="0" rules="all" bordercolorlight="#ffffff" border="1">
	<caption style="FONT-SIZE: 9.75pt; FONT-FAMILY: &quot;Microsoft Sans Serif&quot;; FONT-WEIGHT: bold" align="center"></caption>
	<thead>
	<tr id="RowHead">
	<th style="BACKGROUND-COLOR: menu" align="center">Numer w arkuszu </th>
	<th style="BACKGROUND-COLOR: menu" align="center">Nazwa </th>
	<th style="BACKGROUND-COLOR: menu" align="center">Kod </th>
	<th style="BACKGROUND-COLOR: menu" align="center">Kategoria </th></tr></thead>
	<colgroup style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px" align="left"></colgroup>
	<colgroup style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px" align="left"></colgroup>
	<colgroup style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px" align="left"></colgroup>
	<colgroup style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px" align="left"></colgroup>
	<tbody>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">1 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">religia/etyka </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">r/e </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">religia/etyka </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">2 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">godzina z wychowawcą </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">GW </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zachowanie </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">3 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">język polski </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">JP </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">4 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">język angielski </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">JA </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">język obcy </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">5 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">język niemiecki </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">JN </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">język obcy </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">6 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">wiedza o kulturze </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">WOK </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">7 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">historia </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">hist </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">8 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">wiedza o społeczeństwie </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">WOS </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">9 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">podstawy przedsiębiorczości </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PP </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">10 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">geografia </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ge </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">11 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">biologia </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">bio </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">12 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">chemia </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ch </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">13 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">fizyka </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">fiz </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">14 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">matematyka </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">mat </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">15 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">informatyka </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">inf </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">16 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">wychowanie fizyczne </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">WF </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">17 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">edukacja dla bezpieczeństwa </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">EdB </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">18 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">wychowanie do życia w rodzinie </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">WdŻ </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">19 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">historia i społeczeństwo </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">HiS </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">20 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">matematyka (zakres rozszerzony) </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">mat_roz </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">21 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">fizyka (zakres rozszerzony) </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">fiz_roz </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">22 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">podstawy techniki komputerowej </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PTK </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">23 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">podstawy algorytmiki i programowania </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PAiP </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">25 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">język angielski zawodowy </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">JAz </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">język obcy </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">26 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">podejmowanie i prowadzenie działalności gospodarczej </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PiPDzGosp </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">27 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">podstawy sieci komputerowych </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PSK </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">28 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">urządzenia techniki komputerowej </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">UTK </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">29 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">systemy operacyjne </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">SO </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">31 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">sieciowe systemy operacyjne rodziny Linux </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">SSOLi </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">32 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">sieciowe systemy operacyjne rodziny Windows </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">SSOWi </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">33 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">programowanie strukturalne i obiektowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PSiO </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">34 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">grafika komputerowa i aplikacje internetowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">GKiAI </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">35 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">bazy danych </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">BD </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">36 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">nowoczesne technologie w informatyce </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">NTwInf </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">37 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">podstawy elektrotechniki </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PE </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">38 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">układy analogowe i cyfrowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">UAiC </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">39 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">systemy pomiarowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">SysP </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">40 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">podstawy teleinformatyki </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PTinf </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">41 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">sieci teleinformatyczne </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">STinf </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">42 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">pracownia systemów komputerowych </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PrSK </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">43 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">pracownia elektryczna i elektroniczna </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PrEiEl </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">44 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">pracownia teleinformatyczna </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PrTinf </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">45 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">sieci rozległe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">SRoz </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">46 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">bezpieczeństwo w sieciach teleinformatycznych </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">BwSTinf </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">47 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">sieci komputerowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">SK </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">48 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">sieciowe systemy operacyjne </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">SSO </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">49 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">komputerowe wspomaganie projektowania </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">KWProj </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">50 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">podstawy elektrotechniki i elektroniki </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PEiEl </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">51 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">przyrządy i metody pomiarowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PiMP </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">52 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">technologia i materiałoznawstwo elektryczne </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">TiME </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">53 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">układy analogowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">UAn </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">54 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">układy cyfrowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">UC </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">55 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">konstrukcja i eksploatacja urządzeń elektronicznych </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">KiEUEl </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">56 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">układy mikroprocesorowe </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">UMp </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">57 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">układy automatyki </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">UAut </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">58 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">dzialalność gopodarcza </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">Dz.Gosp. </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">59 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">instalowanie urządzeń elektronicznych w praktyce </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">InstUEl </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">60 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">pracownia elektrotechniki i elektroniki </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PrEtiEl </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">61 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">pracownia konstrukcji i eksploatacji urządzeń elektronicznych </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PrKiEUEl </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">62 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">pracownia konstrukcji i eksploatacji urządzeń cyfrowych </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PrKiEUC </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">63 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">innowacyjne technologie </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">InnTech </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">64 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">praktyka zawodowa </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">praktyka </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">70 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">przetwarzanie i obróbka sygnałów </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">PiOSygn </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">70 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">Specjalizacja </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">spec </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zawodowe </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">70 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">zajęcia specjalizacyjne </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ZajSpec </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; BACKGROUND-COLOR: #eeeeee">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">71 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">Elektrotechnika i elektronika </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">EtiEl </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">ogólnokształcące </td></tr>
	<tr style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px">
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">71 </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">Język angielski dla teleinformatyków </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">JATinf </td>
	<td style="PADDING-LEFT: 4px; PADDING-RIGHT: 4px">język obcy </td></tr></tbody></table>

	<div id="result" class="col-sm-6"></div>
	<div class="clearfix"></div>
</div>

<script>
function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}
$(function() {
	$("#t tr").each(function() {
		$(this).children('td:nth-child(2)').each(function() {
			$("#result").append("'" + capitaliseFirstLetter($(this).text().trim()) + "',<br />");
		
		});
	
	});

});
</script> */ ?>