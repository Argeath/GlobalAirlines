<div class="well">
	<div class="col-md-3 well">
		<? foreach($classes as $k => $c) {
			$name = $classesKeys[$k-1];
			echo '<h4>'.$name.'</h4>';
			
			//Planes
			foreach($c as $p) {
				$active = ($p->id == $id) ? 'btn-success' : 'btn-primary';
				echo HTML::anchor("testUpgrades/index/".$p->id, $p->fullName(), array('class' => "btn btn-block ".$active));
			}
		} ?>
	</div>
	<div class="col-md-9">
		<? if($id == 0) echo '<div class="well">Wybierz samolot</div>';
		else { ?>
		<div class="well">
			<h1><?= $plane->fullName(); ?></h1>
				<div class="well col-xs-12">
					<?= FORM::open(NULL, array('class' => 'checkbox-table')); ?>
					<table class="table">
						<tbody>
						<? foreach($upgradesConfig as $u => $v)
						{
							if( ! isset($plane_upgrades[$u]))
								$plane_upgrades[$u] = array();
							$pu = $plane_upgrades[$u];
							echo '<tr><th>'.$u.'</th>';
							foreach($v as $e => $ev)
							{
								$val = 0;
								if( isset($pu[$e]) && $pu[$e] == true)
									$val = 1;
								echo '<td class="checkbox-td" value="'.$val.'" category="'.$u.'" '.(($val==1) ? 'style="background-color: #A3A3FF;"' : '').'>'.$e.'</td>';
							}
							echo '</tr>';
						}
						?>
						</tbody>
					</table>
					<input type="hidden" class="checkbox-input" name="upgrades" value=''/>
					<input type="hidden" name="option" value="possible"/>
					<input type="submit" value="Odwróć zaznaczenie" class="btn btn-default checkbox-select"/>
					<input type="submit" value="Zapisz" class="btn btn-primary checkbox-save"/>
					</form>
				</div>
			<div class="clearfix"></div>
		</div>
		<div class="well">
			<?= FORM::open(); ?>
			<div class="col-md-2">
				Spalanie: <?= $plane->maxSpalanie; ?><br />
				<input type="text" name="maxSpalanie" value="<?= ($plane->maxSpalanie) ? round((1-$plane->maxSpalanie/$plane->spalanie)*100) : 0; ?>" class="form-control"/>
				<? for($x=0; $x <= 5; $x++)
				echo ($x*10).'% - '.round($plane->spalanie * (1 - $x / 10), 2).'<br />'; ?>
			</div>
			<div class="col-md-2">
				Zasięg: <?= $plane->maxZasieg; ?><br />
				<input type="text" name="maxZasieg" value="<?= ($plane->maxZasieg) ? round(($plane->maxZasieg/$plane->zasieg-1)*100) : 0; ?>" class="form-control"/>
				<? for($x=0; $x <= 5; $x++)
				echo ($x*10).'% - '.round($plane->zasieg * ($x / 10 + 1)).'<br />'; ?> 
			</div>
			<div class="col-md-2">
				Predkosc: <?= $plane->maxPredkosc; ?><br />
				<input type="text" name="maxPredkosc" value="<?= ($plane->maxPredkosc) ? round(($plane->maxPredkosc/$plane->predkosc-1)*100) : 0; ?>" class="form-control"/>
				<? for($x=0; $x <= 5; $x++)
				echo ($x*10).'% - '.round($plane->predkosc * ($x / 10 + 1)).'<br />'; ?>
			</div>
			<div class="col-md-2">
				Miejsc: <?= $plane->maxMiejsc; ?><br />
				<input type="text" name="maxMiejsc" value="<?= ($plane->maxMiejsc) ? round(($plane->maxMiejsc/$plane->miejsc-1)*100) : 0; ?>" class="form-control"/>
				<? for($x=0; $x <= 5; $x++)
				echo ($x*10).'% - '.round($plane->miejsc * ($x / 10 + 1)).'<br />'; ?>
			</div>
			<div class="col-md-2">
				Komfort:<br />
				<input type="text" name="komfort" value="<?= $plane->komfort; ?>" class="form-control"/>
			</div>
			<div class="col-md-2">
				<br />
				<input type="hidden" name="option" value="maxes"/>
				<input type="submit" value="Zapisz" class="btn btn-primary btn-block"/>
			</div>
			
			</form>
			<div class="clearfix"></div>
		</div>
		<div class="well">
			<? for($i=0; $i<=2; $i++) {
				if( ! isset($classesKeys[$plane->klasa-1+$i]))
					continue;
				$klasaN = $classesKeys[$plane->klasa-1+$i];
				$klasaV = $classesConfig[$klasaN];
				$zasieg = $classesZasiegConfig[$klasaN];
				echo '<div class="well col-sm-4">
					<h4>'.$klasaN.'</h4>
					Pasażerów: '.$klasaV[0].' - '.$klasaV[1].'<br />
					Zasięg: '.$zasieg[0].' - '.$zasieg[1].'<br />
				</div>';
			} ?>
			<div class="clearfix"></div>
		</div>
	<? } ?>
	</div>
	
	<div class="clearfix"></div>
</div>

<script>
$(function() {
	$('.checkbox-table').each(function()
	{
		var table = $(this);
		var upgrades = <?= ( ! empty($plane->upgrades)) ? $plane->upgrades : '{}'; ?>;
		table.find('.checkbox-td').on('click', function() {
			if($(this).attr('value') == 0) {
				$(this).attr('value', 1);
				$(this).css('background-color', '#A3A3FF');
				var category = $(this).attr('category');
				var element = $(this).text();
				if(typeof upgrades[category] == 'undefined')
					upgrades[category] = {};
				upgrades[category][element] = true; 
			} else {
				$(this).attr('value', 0);
				$(this).css('background-color', '#FFF');
				var category = $(this).attr('category');
				var element = $(this).text();
				upgrades[category][element] = false;
			}
		});
		table.find('.checkbox-save').on('click', function(e) {
			var json = encodeURIComponent(JSON.stringify(upgrades));
			table.find('.checkbox-input').val(json);
		});
		table.find('.checkbox-select').on('click', function(e) {
			e.preventDefault();
			table.find('.checkbox-td').each(function() { $(this).trigger('click'); });
		});
	});

});
</script>