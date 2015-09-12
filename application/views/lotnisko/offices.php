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
			<? foreach($offices as $o)
				{
					$gracz = $o->user;

					echo '<div class="contact thumbnail" style="position: relative;">'.$gracz->drawButton().'<br /><img src="'.$gracz->getAvatar().'" style="width: 100px; height: 100px;" class="img-thumbnail"/></div>';

				} ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>