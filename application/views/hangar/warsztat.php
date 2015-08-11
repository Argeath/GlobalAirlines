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
		<? if( ! empty($planesData)) {
			foreach($planesData as $data) {
				echo "<tr>
					<td><img src='" . URL::base(TRUE) . "assets/samoloty/" . $data['plane']->plane_id . ".jpg' class='img-rounded hidden-xs' style='width: 100px;'/><br />" . $data['plane']->fullName() . "<br />(" . $data['poz'] . ")</td>
					<td width='25%'>
						Ulepszeń: " . round($data['upgrades'], 2) . "%<br />
						Stan: " . round($data['plane']->stan, 0) . "%<br />
						<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Mnożnik serwisowy' style='display:inline-block;width: 70px;'><i class='glyphicon glyphicon-wrench'></i> x" . $data['typ']->mechanicy . "</div>
					</td>
					<td width='20%'>
						" . HTML::anchor('warsztat/przeglad/' . $data['plane']->id, Form::submit('opcje', 'Przegląd generalny', array_merge(array('class' => "btn btn-primary btn-block btn-success"), $data['disabled'])), $data['disabledText']) . "
						" . HTML::anchor('warsztat/ulepszenia/' . $data['plane']->id, Form::submit('opcje', 'Ulepsz samolot', array_merge(array('class' => "btn btn-primary btn-block btn-primary"), $data['disabled'])), $data['disabledText']) . "
					</td></tr>";
			}
		} else {
			echo "<tr><td>Nie posiadasz &#382;&#246;dnych samolotów. &#381;&#246;dnych.</td></tr>";
		} ?>
	</table>
	</div>
</div>