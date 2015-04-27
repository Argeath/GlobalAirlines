<link rel="stylesheet" href="<?=URL::base();?>assets/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="<?=URL::base();?>assets/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>


<div class="col-md-2" style="height: 300px;">
	<img src="<?=URL::base(TRUE);?>assets/logo-airlines.png" class="logo-lg" />
</div>

<div class="col-md-10 font-txt" style="padding: 0;">
	<div class="well col-md-7" style="text-align: justify; text-justify: inter-word; min-height: 315px;">
		<h3 style="text-align: center;">Witaj w Global Airlanes!</h3><br /><br />
		Rozsiądź się wygodnie w <b>fotelu prezesa</b> i zacznij zarządzać własnymi <b>liniami lotniczymi</b>.
		Decyduj o losach twojej firmy. Kupuj <b>maszyny</b>, zatrudniaj <b>pracowników</b>, przymuj <b>zlecenia</b> i wysyłaj <b>samoloty</b>.
		Zarabiaj <b>duże pieniądze</b> i powiększaj <b>swoje imperium</b>. Stań się najlepszym <b>zarządcą linii lotniczych</b> na całym globie!
		Tylko tutaj i tylko teraz masz okazję wykazać się swoimi <b>umiejętnościami organizacji i planowania</b>.
		Podejmuj <b>odważne działania</b>, decyduj o wszystkim!
		<b>To od Ciebie zależy</b>, kto będzie dla Ciebie pracować, jakie maszyny będą w <b>twojej flocie</b>.
		Ulepszaj <b>samoloty</b>, zdobywaj <b>klientów</b>, osiągaj coraz lepszy <b>wizerunek swojej firmy</b>.<br /><br />
		<h4 style="text-align: center;"><?=HTML::anchor('user/create', 'To gra stworzona specjalnie dla Ciebie! Dołącz już dziś!');?></h4>
	</div>
	<div class="well col-md-5" style="min-height: 315px;">
		<div class="page-header">
			<h1>Logowanie</h1>
		</div>
		<? if ($message) : ?>
			<h3 class="alert alert-danger">
<?=$message;?>
</h3>
		<? endif; ?>
		<div class="form-group row">
<?=Form::open('user/login');?>
<div class="col-xs-12">
                <div class="col-md-6">
<?=Form::input('username', HTML::chars(Arr::get($_POST, 'username')), array('class' => "form-control", 'placeholder' => "Login"));?>
</div>
                <div class="col-md-6">
<?=Form::password('password', NULL, array('class' => "form-control", 'placeholder' => "Haslo"));?>
</div>
			</div>
			<div class="col-xs-12">
                <div class="col-md-6">
<?=Form::submit('login', 'Zaloguj', array('class' => "btn btn-primary btn-block"));?>
                </div>
                <div class="col-md-6">
				    <button id="fb_login" class="btn btn-primary btn-block" style="padding-left: 10px;"><img src="<?=URL::base(TRUE);?>assets/facebook.png" style="margin-left: -10px; height: 18px;"/> Zaloguj przez FB</button>
                </div>
			</div>
<?=Form::close();?>
		</div>
		<hr />
		<h4 class="link-lg"><?=HTML::anchor('user/create', 'Załóż konto');?></h4>
		<hr />
		<h4 class="link-lg"><?=HTML::anchor('forum', 'Forum');?></h4>
	</div>
</div>

<div class="clearfix"></div>
<div class="well font-txt">
	<a class="fancybox" rel="gallery" href="<?=URL::base();?>assets/gallery/1.jpg" title="Mapa z zaznaczonymi aktualnie dostępnymi lotniskami w Europie">
		<img src="<?=URL::base();?>assets/gallery/1_sm.jpg" style="height: 150px;" alt="" />
	</a>
	<a class="fancybox" rel="gallery" href="<?=URL::base();?>assets/gallery/2.jpg" title="Terminarz z wykonanymi/zaplanowanymi lotami">
		<img src="<?=URL::base();?>assets/gallery/2_sm.jpg" style="height: 150px;" alt="" />
	</a>
	<a class="fancybox" rel="gallery" href="<?=URL::base();?>assets/gallery/3.jpg" title="Wykres cen paliwa">
		<img src="<?=URL::base();?>assets/gallery/3_sm.jpg" style="height: 150px;" alt="" />
	</a>
	<a class="fancybox" rel="gallery" href="<?=URL::base();?>assets/gallery/4.jpg" title="Fragment dostępnych samolotów do kupna">
		<img src="<?=URL::base();?>assets/gallery/4_sm.jpg" style="height: 150px;" alt="" />
	</a>
	<a class="fancybox" rel="gallery" href="<?=URL::base();?>assets/gallery/5.jpg" title="Mapa lotnisk Ameryki Północnej">
		<img src="<?=URL::base();?>assets/gallery/5_sm.jpg" style="height: 150px;" alt="" />
	</a>
	<div class="clearfix"></div>
</div>


<div class="well font-txt" style="height: 100px;">
	Film instruktażowy
</div>


<script>
$(function() {
	$(".fancybox").fancybox();


});
</script>