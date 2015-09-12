<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Warsztat <small>Ulepszenia samolotu</small></h1>
			</div>
			<ul class="pagination pagination-xm" style="margin-bottom: 50px;">
				<li><?= HTML::anchor('samoloty', 'Samoloty'); ?>
				<li><?= HTML::anchor('warsztat', 'Warsztat'); ?>
				<li class='active'><?= HTML::anchor('warsztat/ulepszenia/'.$planeId, 'Ulepszenia'); ?>
			</ul>
			<div class="clearfix"></div>

			<?
			$ulepszen = 0;
			if( $upgrades && ! empty($upgrades))
			{
				foreach($upgrades as $c => $arr)
				{
					$elementow = 0;
					$code = '<div class="col-lg-3 col-md-6" style="min-width: 400px;" onselectstart="return false" onselect="return false">
							<div class="upgrade">
								<div class="circle">';
					foreach($arr as $e => $b)
					{
						if( ! $b)
							continue;
						$nazwa = $e;
						$level = (isset($upgraded[$c][$e])) ? $upgraded[$c][$e] : 0;
						$lT = ($level > 0) ? '<span>'.rome($level).'</span>' : '';
						$code .= '<div class="element '.cleanString($nazwa, true, '-').'" url="'.URL::site('ajax/upgradeInfo/'.$planeId.'/'.$c.'/'.$e).'">'.$lT.'</div>';
						$elementow++;
					}
					$code .= '</div>
							<div class="category '.cleanString($c, true, '-').'"></div>
						</div>
					</div>';
					if( $elementow > 0)
						echo $code;

					$ulepszen++;
				}
			}
			if($ulepszen == 0)
					echo '<div class="well">Ten samolot nie ma żadnych dostępnych ulepszeń</div>';
			?>
			<div class="clearfix" style="margin-bottom: 30px;"></div>
		</div>
	</div>
</div>

<script>
$(function() {
	$(".upgrade").each(function() {
		var circle = $(this).find('.circle');
		var items = circle.find('div');
		var l = items.length;
		var i = 0;
		items.each(function() {
			$(this).css('left', (50 - 45*Math.cos(-0.5 * Math.PI - 2*(1/l)*i*Math.PI)).toFixed(4) + "%");
			$(this).css('top', (50 + 45*Math.sin(-0.5 * Math.PI - 2*(1/l)*i*Math.PI)).toFixed(4) + "%");
			i++;
		});

		$(this).find('.category').on('click', function(e) {
			e.preventDefault;
			circle.toggleClass('open');
		});
	});
	
	$('.element').on('click', function() {
		var remote = $(this).attr('url');
		$("#upgradeModal .modal-content").load(remote);
		$("#upgradeModal").modal('show');
	});
});
</script>