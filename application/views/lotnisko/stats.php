<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Lotnisko <small><?=$lotnisko->getName();?> (<?=$lotnisko->code?>)</small></h1>
			</div>
			<ul class="pagination pagination-xm">
				<li <?=($action == 'index') ? "class='active'" : "";?>><?=HTML::anchor('airport/index/' . $city_id, 'Lotnisko');?></li>
				<li <?=($action == 'offices') ? "class='active'" : "";?>><?=HTML::anchor('airport/offices/' . $city_id, 'Biura');?></li>
				<li <?=($action == 'stats') ? "class='active'" : "";?>><?=HTML::anchor('airport/stats/' . $city_id, 'Statystyki');?></li>
				<? if($office_id > 0) { ?> <li <?=($action == 'office') ? "class='active'" : "";?>><?=HTML::anchor('airport/office/' . $office_id, 'Twoje biuro');?></li>
				<? } else { ?> <li <?=($action == 'newOffice') ? "class='active'" : "";?>><?=HTML::anchor('airport/newOffice/' . $city_id, 'Wykup biuro');?></li><? } ?>
			</ul>
			<div class="clearfix"></div>
			<div class="col-lg-3 well">
				<div id="chart1"></div>
			</div>
			<div class="col-lg-4 well">
				<div id="chart3"></div>
			</div>
			<div class="col-lg-5 well">
				<div id="chart2"></div>
			</div>
			<div class="clearfix"></div>

		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo URL::base(TRUE);?>assets/jqplot/jquery.jqplot.min.css" />
<script type="text/javascript" src="<?=URL::base(TRUE);?>assets/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="<?=URL::base(TRUE);?>assets/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="<?=URL::base(TRUE);?>assets/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="<?=URL::base(TRUE);?>assets/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="<?=URL::base(TRUE);?>assets/jqplot/plugins/jqplot.highlighter.min.js"></script>
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/jqplot/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?=URL::base(TRUE);?>assets/jqplot/plugins/jqplot.pieRenderer.min.js"></script>

<?
	$udzialData = "";
	$inni = 0;
	$wyswietlonych = 0;
	if($udzialSuma > 0)
	{
		foreach($udzial as $us => $u)
		{
			if($wyswietlonych < 10)
			{
				$udzialData .= "['".$users[$us]->username."', ".$u."], ";
				$wyswietlonych++;
			} else
				$inni += $u;
		}
	} else
		$udzialData = "['Brak ruchu', 1]";

	if($inni > 0)
		$udzialData .= "['Reszta', ".$inni."]";

	$ruchData = "";
	foreach($ruch as $d => $u)
		$ruchData .= "['".$d."', ".$u."], ";

	$zarobkiData = "";
	foreach($zarobki as $d => $u)
		$zarobkiData .= "['".$d."', ".$u."], ";
?>

<script>
$(function() {
	var data = [<?=$udzialData;?>];
	var plot1 = jQuery.jqplot ('chart1', [data],
		{
			title:'Udział w lotach (14 dni)',
			animate: true,
			animateReplot: true,
			seriesDefaults: {
			// Make this a pie chart.
			renderer: jQuery.jqplot.PieRenderer,
			rendererOptions: {
			  // Put data labels on the pie slices.
			  // By default, labels show the percentage of the slice.
			  showDataLabels: true
			},
			highlighter: {
				show: true,
				sizeAdjust: 7.5,
				bringSeriesToFront: true
			},
		  },
		  legend: { show:true, location: 'e' }
		}
	);
	var data2 = [<?=$ruchData;?>];
	var plot2 = $.jqplot('chart2', [data2], {
      title:'Ruch na lotnisku (14 dni)',
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
			formatString:'%d'
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

	var data3 = [<?=$zarobkiData;?>];
	var plot3 = $.jqplot('chart3', [data3], {
      title:'Zarobki na lotnisku (14 dni)',
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
              formatString:'%d <?=WAL;?>'
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