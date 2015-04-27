<div class="well">
	<div class="page-header">
		<h1>Paliwo</h1>
	</div>
	<div id="chartField" class="well">
		<div id="chart1" style="height:300px;"></div>
	</div>
	<br />
	<div class="col-sm-6 col-xs-12 thumbnail">
		<div class="page-header"><h3 class="white">Twoje bazy</h3></div>
		<table class="table table-striped">
		<thead>
			<tr>
				<th>Miasto</th>
				<th>Paliwo</th>
				<th>Wartość paliwa</th>
				<th>Opcje</th>
			</tr>
		</thead>
		<tbody>
			<?= $bazyT; ?>
		</tbody>
		<tfoot>
			<tr>
				<td style="font-weight: 800;">Razem</td>
				<td style="font-weight: 800;"><?= formatCash($razemP, 2); ?> l</td>
				<td class="tdblank" style="font-weight: 800;"><?= formatCash($razemW, 0); ?> <?= WAL; ?></td>
			</tr>
		</tfoot>
		</table>
	</div>
	<div class="col-sm-6 col-xs-12 well">
		<h2>
			Aktualna cena paliwa<br />
			<b><?= formatCash(Oil::getOilCost(), 2); ?> <?= WAL; ?></b>
			<? if($profil['admin'])
				{
					Oil::debugOil();
				}
			?>
		</h2>
		
	</div>
	<div style="clear: both;"></div>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo URL::base(TRUE); ?>assets/jqplot/jquery.jqplot.min.css" />
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="<?= URL::base(TRUE); ?>assets/jqplot/plugins/jqplot.highlighter.min.js"></script>
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="<?php echo URL::base(TRUE); ?>assets/jqplot/excanvas.js"></script><![endif]-->

<script type='text/javascript'>
$(document).ready(function(){
  var line1=[<?= $cenyT; ?>];
  var plot1 = $.jqplot('chart1', [line1], {
      title:'Cena paliwa',
	  animate: true,
      animateReplot: true,
	  axes:{
		xaxis:{
		  renderer:$.jqplot.DateAxisRenderer,
		  tickOptions:{
			formatString:'%d.%m %H:%M'
		  } 
		},
		yaxis:{
		  tickOptions:{
			formatString:'%.2f <?= WAL; ?>'
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