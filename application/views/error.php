<? $detect = new MobileDetect; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
	<title>Global AirLines Simulator - Podgląd</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=1024, initial-scale=1, maximum-scale=2, user-scalable=yes">
	<meta name="description" content="Global AirLines Simulator - gra przeglądarkowa, polegająca na symulowaniu swojej własnej linii lotniczej. Wysyłaj samoloty, zarabiaj na zleceniach, zostań najbogatszy!" />
	<meta name="keywords" content="Plane, Simulator, Lotnictwo, AirLines, Air, Lanes, Samoloty, linie lotnicze, gra przeglądarkowa" />
	<link rel="canonical" href="<?php echo URL::base(TRUE); ?>"/>
	<link rel="Shortcut icon" href="<?php echo URL::base(TRUE); ?>assets/fav.ico" />
	
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-12606596-4', 'matinf.pl');
	  ga('send', 'pageview');

	</script>
	
	<script type="text/javascript">
		function url_base() { return "<?php echo URL::base(TRUE); ?>"; }
	</script>
</head>
<body class="st-content">

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pl_PL/all.js#xfbml=1&appId=587990371267671";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<link rel="stylesheet/less" type="text/css" href="<?php echo URL::base(TRUE); ?>assets/css/newstyle.less" />
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-darkness/jquery-ui.min.css">

<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/1.7.0/less.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<div class="container" style="height: 100%;">
	<div id="panel" class="col-md-6 col-md-offset-3 panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Wystąpił błąd.</h3>
		</div>
		<div class="panel-body">
			<?= (isset($msg)) ? $msg.'<br />' : ''; ?>
			<?= HTML::anchor('user/logout', 'Spróbuj ponownie'); ?>
		</div>
	</div>
</div>

<script>
$(function() {
	$('#panel').css({'margin-top':($(window).height()-$("#panel").height())/2,'top':'0'});
	$(window).on("resize", function() { 
		$('#panel').css({'margin-top':($(this).height()-$("#panel").height())/2,'top':'0'});
	});
});
</script>

</body>
</html>