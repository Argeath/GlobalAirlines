<div class="well">
	<div class="page-header">
		<h1>Dziennik finansowy <small>Podsumowanie</small></h1>
	</div>
	<div class="well col-xs-12 col-sm-2">
		<?= HTML::anchor('journal', 'Wróć', array('class' => 'btn btn-default btn-block')); ?>
		<br />
		<?= Form::open('summation', array('class' => 'well', 'id' => 'podsumowanie-okres')); ?>
			<div id="SummationFrom" class="input-group date col-xs-12" style="float: none; margin: 0 auto;">
				<input id="SummationFromInput" data-format="dd/MM/yyyy" type="text" name="from" class="form-control" placeholder="Pokaż od"/>
				<span class="input-group-addon add-on">
				  <i id="SummationFromButton" data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i>
				</span>
			</div>
			<div id="SummationTo" class="input-group date col-xs-12" style="float: none; margin: 0 auto;">
				<input id="SummationToInput" data-format="dd/MM/yyyy" type="text" name="to" class="form-control" placeholder="Pokaż do"/>
				<span class="input-group-addon add-on">
				  <i id="SummationToButton" data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i>
				</span>
			</div>
			<button class="btn btn-primary btn-block">Pokaż</button>
		</form>

        <?= Form::open('summation', array('class' => 'well')); ?>
            <input type="text" value="123" name="from"/>
            <button>ABC</button>
        </form>
		<br />
		<? $sizeOgolne = "col-xs-12"; if( ! isset($plane_id) || (int)$plane_id <= 0) $sizeOgolne = "col-xs-10 col-xs-offset-1"; ?>
		<?= HTML::anchor('summation', 'Ogólne', array('class' => 'btn btn-primary '.$sizeOgolne, 'id' => 'podsumowanie-ogolne')); ?>
		<? foreach($planes as $p) {
			$size = "col-xs-12";
			if( isset($plane_id) && $p->id == $plane_id)
				$size = "col-xs-10 col-xs-offset-1";
			$link = 'summation/'.$from.'/'.$to.'/'.$p->id;
			$textcolor = getContrastYIQ($p->color);
			$style = 'background-color: #'.$p->color.'; color: '.$textcolor.'; margin-top: 5px;';
			echo HTML::anchor($link, $p->rejestracja, array('class' => 'btn btn-primary '.$size, 'style' => $style));		
		} ?>
	
	</div>
	
	<div class="well col-xs-12 col-sm-6">
		<table class="table col-md-6">
			<thead>
			<tr>
				<th></th>
				<th>Zysk</th>
				<th>Strata</th>
			</tr>
			</thead>
			<tbody>
				<? foreach($dane as $k => $d) {
					echo "<tr>";
					echo "<td>".Helper_Financial::getText($k)."</td>";
					echo "<td>".(($d > 0) ? Helper_Prints::colorNumber($d)." ".WAL : "")."</td>";
					echo "<td>".(($d < 0) ? Helper_Prints::colorNumber($d)." ".WAL : "")."</td>";
					echo "</tr>";
				} 
				if( empty($dane))
					echo "<tr><td colspan='3'>Brak danych</td></tr>";				
				?>
				
				<tr style="border-top: 2px solid #BBB;" id="podsumowanie-razem">
					<td>Razem</td>
					<td colspan="2">
						<?= Helper_Prints::colorNumber($razem)." ".WAL; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="well col-xs-12 col-sm-4">
		<div class="well"><div id="chart1" style="height:300px; margin-bottom: 20px;"></div></div>
		<div class="well"><div id="chart2" style="height:300px;"></div></div>
	</div>
	<div class="clearfix"></div>
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
      var n1 = new tutorialElement($('#podsumowanie-ogolne'), "Site_Bottom", "Site_Bottom", "Podział finansów na konkretne samoloty", "inner", false);
      var n2 = new tutorialElement($('#podsumowanie-okres'), "Hard_Bottom_Left", "Site_Bottom", "Wyświetlenie podsumowania z konkretnego okresu czasu", "inner", false);
      var n3 = new tutorialElement($('#podsumowanie-razem'), "Site_Top", "Site_Top", "Podsumowanie konkretnych wydatków", "inner", false);
      var n4 = new tutorialElement($('#chart1'), "Site_Right", "Site_Right", "Wykres pokazujący stan konta na dany dzień(o godzinie 23:59)", "inner", false);
      var n5 = new tutorialElement($('#chart2'), "Site_Right", "Site_Right", "Wykres pokazujący zyski i straty z konkretnego dnia(od 0:00 do 23:59)", "inner", false);
      window.tutorialElements.push(n1, n2, n3, n4, n5);

	$("#SummationFromInput").datetimepicker({
		 lang:"pl",
		 timepicker:false,
		 format:"d.m.Y",
		 maxDate:0,
		 value:"<?= date('d.m.Y', $from); ?>"
		});
	$("#SummationFromButton").click(function(){
	  $("#SummationFromInput").datetimepicker("show");
	});
	$("#SummationToInput").datetimepicker({
		 lang:"pl",
		 timepicker:false,
		 format:"d.m.Y",
		 value:"<?= date('d.m.Y', $to); ?>"
		});
	$("#SummationToButton").click(function(){
	  $("#SummationToInput").datetimepicker("show");
	});

      cashFormatter = function (format, val) {
          return val.toLocaleString() + ' <?= WAL; ?>';
      }

	
	var line1=[<?= $chart1; ?>];
	var plot1 = $.jqplot('chart1', [line1], {
	  title:'Stan konta',
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
	
	var line2=[<?= $chart2; ?>];
	var plot2 = $.jqplot('chart2', [line2], {
	  title:'Zysk',
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