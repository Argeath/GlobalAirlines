<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="col-xs-6 col-md-2">
				<div class="well">
					<?= $gracz->drawAvatar(); ?><br />
					<?= $gracz->drawButton(); ?><br /><br />
					<? if($znajomi == false) { ?>
						<?= Form::open('kontakty'); ?>
						<input type="hidden" name="typ" value="nowy"/>
						<input type="hidden" name="new" value="<?= $gracz->id; ?>"/>

						<?= Form::submit('create', 'Dodaj do znajomych', array( 'class' => "btn btn-primary btn-block")); ?>
						<?= Form::close(); ?>
					<? } ?>
				</div>

				<div class="well">
					<table class="table">
						<thead>
							<th colspan="2">Biura</th>
						</thead>
						<? foreach($offices as $o) {
							echo "<tr>";
							echo "<td>".(($o->isHome()) ? '<i class="glyphicon glyphicon-home"></i>' : '')." ".$o->getFlag()." ".HTML::anchor('airport/index/'.$o->city->id, $o->getName())."</td>";
							echo "</tr>";
						} ?>
					</table>
				</div>
			</div>

			<div class="well col-xs-12 col-md-6">
				<table class="table">
					<thead>
						<th colspan="2">Flota</th>
					</thead>
					<? foreach($planes as $p) {
						echo "<tr>";
						echo "<td><img src='".URL::base(TRUE)."assets/samoloty/".$p->plane_id.".jpg' class='img-rounded hidden-xs' style='width: 70px;'/><br />".$p->fullName()."</td>";
						echo "<td>Pokonana trasa: ".formatCash($p->km)."km<br />Czasu w powietrzu: ".Helper_TimeFormat::secondsToText($p->hours)."<br />Maksymalna wartość samolotu: ".formatCash($p->getCost())." ".WAL."</td>";
						echo "</tr>";
					} ?>
				</table>
			</div>

			<div class="well col-xs-12 col-md-4">
				<div class="well"><div id="chart1" style="height:300px; margin-bottom: 20px;"></div></div>
			</div>

			<div class="clearfix"></div>
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo URL::base(TRUE); ?>assets/jqplot/jquery.jqplot.min.css" />
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.trendline.min.js"></script>
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="<?php echo URL::base(TRUE); ?>assets/jqplot/excanvas.js"></script><![endif]-->

<script type="text/javascript">
  $(function() {

      cashFormatter = function (format, val) {
          return val.toLocaleString() + ' <?= WAL; ?>';
      }

	var line1=[<?= $chart1; ?>];
	var plot1 = $.jqplot('chart1', [line1], {
	  title:'Zyski',
	  animate: true,
      animateReplot: true,
	  axes:{
		xaxis:{
		  renderer:$.jqplot.DateAxisRenderer,
		  tickOptions:{
			formatString:'%d.%m'
		  } 
		},
		yaxis:{
		  tickOptions:{
			formatter: cashFormatter
			}
		}
	  },
	  highlighter: {
		show: true,
		sizeAdjust: 7.5,
		bringSeriesToFront: true
	  },
	  cursor: {
		show: false
	  }
	});
  });
</script>