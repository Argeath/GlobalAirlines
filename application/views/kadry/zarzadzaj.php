<div class="well">
	<div class="page-header">
		<h1>Kadry <small>Zarządzanie</small></h1>
	</div>
	<div class="well col-xs-12 col-md-4" style="float: left;" id="kadry-podzial">
		<? 
		$colorN = 'btn-default';
		if($planeId == 0)
			$colorN = 'btn-primary';
		echo HTML::anchor('kadry/zarzadzaj/0', 'Nieprzypisani', array('class' => 'btn '.$colorN.' btn-medium'));
		foreach($planes as $p) {
			$color = 'btn-default';
			if($p->id == $planeId)
				$color = 'btn-primary';
			echo HTML::anchor('kadry/zarzadzaj/'.$p->id, $p->rejestracja, array('class' => 'btn '.$color.' btn-medium'));
		
		} ?>
	</div>
	<div class="well form_300">
		<h3>Zatrudnianie</h3>
		<?= ($planeId > 0) ? Form::open('kadry/zatrudnij/'.$planeId) : Form::open('kadry/zatrudnij'); ?>
		<select name="type" class="form-control">
			<option value="1">Pilot</option>
			<option value="2">Stewardessa</option>
		</select>
		<select name="experience" class="form-control">
			<option value="0">Amator(~5%)</option>
			<option value="1">Początkujący(~16%)</option>
			<option value="2">Doświadczony(~31%)</option>
			<option value="3">Zaawansowany(~44%)</option>
		</select>
		<button class="btn btn-block btn-primary" id="kadry-zatrudnij">Zatrudnij</button>
		<div class="clearfix"></div>
		</form>
	</div>
	<table class="table table-striped">
	<thead>
		<tr>
			<th id="kadry-pracownik">Pracownik</th>
			<th class="hidden-xs">Stan</th>
			<th>Doświadczenie</th>
			<th>Zadowolenie</th>
			<th width="15%">Płaca</th>
			<th width="15%">Opcje</th>
		</tr>
	</thead>
	<tbody>
		<?= $kadry; ?>
	</tbody>
	</table>
	<? if($planeId > 0)
		{
			$planeModel = $plane->plane;
			$pilotow = $planeModel->piloci;
			$dodatkowej = $planeModel->zaloga_dodatkowa;
			$juzPilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
			$juzDodatkowej = $plane->staff->where('type', '!=', 'pilot')->count_all();
			echo "<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Preferowane doświadczenie pilotów' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-graduation-cap'></i> ".($plane->getPreferStaffExp()+5) ."%</div> ";
			echo "<div class='text-rounded ".(($juzPilotow == $pilotow) ? 'bg-blue' : 'bg-red')." Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych pilotów' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-user'></i> ".$juzPilotow." / ".$pilotow."</div> ";
			echo "<div class='text-rounded ".(($juzDodatkowej == $dodatkowej) ? 'bg-blue' : 'bg-red')." Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych stewardess' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-female'></i> ".$juzDodatkowej." / ".$dodatkowej."</div><br />"; 
				
		}

	?>
	<? if($planeId > 0) echo HTML::anchor('samoloty/zaloga/'.$planeId, "Przypisz pracowników", array('class' => 'btn btn-primary btn-medium')); ?>
</div>

<script>
    $(function() {
        var n1 = new tutorialElement($('#kadry-podzial'), "Site_Left", "Site_Top", "Podział pracowników na samoloty", "inner", false);
        var n2 = new tutorialElement($('#kadry-zatrudnij'), "Site_Left", "Site_Top", "Zatrudnianie pracowników (Doświadczeni piloci obniżają szansę na wypadek oraz spalanie samolotu)", "inner", false);
        var n3 = new tutorialElement($('#kadry-pracownik'), "Site_Bottom", "Site_Left", "Lista pracowników", "inner", false);
        window.tutorialElements.push(n1, n2, n3);
    });
</script>