<? $detect = new MobileDetect; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
	<title>Global AirLines Simulator - <?=((isset($title)) ? $title : '');?></title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=1024, initial-scale=0.5, maximum-scale=2, user-scalable=yes">
	<meta name="description" content="Global AirLines Simulator - gra przeglądarkowa, polegająca na symulowaniu swojej własnej linii lotniczej. Wysyłaj samoloty, zarabiaj na zleceniach, zostań najbogatszy!" />
	<meta name="og:description" content="Gra przeglądarkowa, polegająca na symulowaniu swojej własnej linii lotniczej. Wysyłaj samoloty, zarabiaj na zleceniach, zostań najbogatszy!" />
	<meta name="og:site_name" content="Global Airlines Simulator" />
	<meta name="fb:app_id" content="587990371267671" />
	<meta property="og:image" itemprop="image" content="http://serwer1418595.home.pl/assets/logo-airlines2.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />

	<meta name="keywords" content="Airlines Simulator, Lotnictwo, AirLines, Air, Lanes, Samoloty, linie lotnicze, gra przeglądarkowa" />
	<link rel="canonical" href="<?php echo URL::base(TRUE);?>"/>
	<link rel="Shortcut icon" href="<?php echo URL::base(TRUE);?>assets/fav.ico" />

	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?php echo URL::base(TRUE);?>assets/css/newstyle.css" />
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/ui-darkness/jquery-ui.css" />
	<link rel="stylesheet" href="<?php echo URL::base(TRUE);?>assets/css/jquery.datetimepicker.css" />
	<link rel="stylesheet" href="<?php echo URL::base(TRUE);?>assets/css/animate.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" />


	<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/jquery.transit.min.js"></script>
	<script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/jquery.complete-placeholder.min.js"></script>

	<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

	<script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/jquery.nicescroll.js"></script>
	<script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/tock.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-12606596-4', 'auto');
	  ga('send', 'pageview');

	</script>

	<script type="text/javascript">
		function url_base() { return "<?php echo URL::base(TRUE);?>"; }
	</script>
</head>
<body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pl_PL/all.js#xfbml=1&appId=587990371267671";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<script>
	window.tutorialElements = [];

	function tutorialElement(target, type, typeMini, txt, lay, blink, tutorial) {
        this.target = target;
        if(lay == 'layer')
            this.layer = $("#tutorialLayer");
        else
            this.layer = $("#tutorialLayerInner");

        this.txt = txt;

        this.blink = false;
        if (blink)
            this.blink = true;

		this.tutorial = false;
		if(tutorial)
			this.tutorial = true;

        this.type = type;
        this.typeMini = typeMini;
        this.callbacks = function () { };
        this.visible = false;

		return this;
    };

	var ajaxManager = (function() {
		 var requests = [];

		 return {
			addReq:  function(opt) {
				requests.push(opt);
			},
			removeReq:  function(opt) {
				if( $.inArray(opt, requests) > -1 )
					requests.splice($.inArray(opt, requests), 1);
			},
			run: function() {
				var self = this,
					oriSuc;

				if( requests.length ) {
					oriSuc = requests[0].complete;

					requests[0].complete = function() {
						 if( typeof(oriSuc) === 'function' ) oriSuc();
						 requests.shift();
						 self.run.apply(self, []);
					};

					$.ajax(requests[0]);
				} else {
				  self.tid = setTimeout(function() {
					 self.run.apply(self, []);
				  }, 1000);
				}
			},
			stop:  function() {
				requests = [];
				clearTimeout(this.tid);
			},
			countRequests: function() {
				return requests.length;
			}
		 };
	}());
	ajaxManager.run();
    $( window ).unload(function() {
        ajaxManager.stop();
    });
</script>

<div id="tmp-container" class="tmp-container">
		<? Menu::show(); ?>


		<div class="tmp-content">
			<div class="tmp-content-inner">
				<div class="top-panel" onselectstart="return false" onselect="return false">
					<? if($logged) {
						$userHTML = '';
					?>
					<div class="field panel">
						<div class="left hidden-xs"><img src="<?php echo URL::base(TRUE);?>uploads/<?=((isset($profil['avatar'])) ? $profil['avatar'] : '');?>.jpg" class="avatar"/></div>
						<div class="right">
							<b><?=((isset($profil['username'])) ? $profil['username'] : '');?></b><br />
<?=((isset($profil['cash'])) ? formatCash($profil['cash']) . ' ' . WAL : '');?><br />
<?=((isset($profil['premium_points'])) ? formatCash($profil['premium_points']) . ' PP' : '');?>
						</div>
						<div class="ttip panel" style="left: -20px; top: 48px;">
                            <div class="level-info" data-container="body" data-toggle="tooltip" data-placement="right" title="Poziom konta - doświadczenie">
                                <div class="level-field"><?=Experience::getLevelByExp($profil['exp']);?></div>
                                <div id='expbar'>
                                    <div class='label'><?=$profil['expLabel'];?></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="user-info" data-container="body" data-toggle="tooltip" data-placement="right" title="Przebyty dystans">
                                <div class="icon-field"><i class="fa fa-arrows-h"></i></div>
                                <div class="bar">
                                    <div class='label'><?=formatCash($profil['km']);?> km</div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="user-info" data-container="body" data-toggle="tooltip" data-placement="right" title="Czas spędzony w powietrzu">
                                <div class="icon-field"><i class="fa fa-cloud-upload"></i></div>
                                <div class="bar">
                                    <div class='label'><?=TimeFormat::secondsToText($profil['hours']);?></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
							<div class="user-info" data-container="body" data-toggle="tooltip" data-placement="right" title="Przewiezionych pasażerów">
								<div class="icon-field"><i class="fa fa-users"></i></div>

								<div class="bar">
									<div class='label'><?=formatCash($profil['pasazerow']);?></div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="user-info" data-container="body" data-toggle="tooltip" data-placement="right" title="Wykonanych zleceń">
								<div class="icon-field"><i class="fa fa-check"></i></div>

								<div class="bar">
									<div class='label'><?=formatCash($profil['zlecen']);?></div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="user-info" data-container="body" data-toggle="tooltip" data-placement="right" title="Posiadanych punktów premium">
								<div class="icon-field"><i class="fa fa-credit-card"></i></div>

								<div class="bar">
									<div class='label'><?=formatCash($profil['premium_points']);?></div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>

					<div class="field panel">
						<div class="left">
							<span class="glyphicon glyphicon-warning-sign bootstrap-icon <?=(($nowych_powiadomien > 0) ? 'blink' : '');?>">
								<div class="wide ttip panel">
									<?
										echo '<div style="padding-bottom: 10px;">';
										$powiadomienia = ORM::factory("MiniMessage")->where('user_id', '=', $profil['id'])->order_by('data', 'desc')->limit(5)->find_all();
										foreach($powiadomienia as $powiad)
										{
											echo '<div class="ttip-row '.(($powiad->checked==0) ? "actual" : "").'" mid="'.$powiad->id.'" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content=\''.$powiad->long.'\'>';
											echo TimeFormat::timeDeltaInWords($powiad->data).': '.$powiad->msg;
											echo "</div>";
										}
										echo "</div>".HTML::anchor('powiadomienia', 'Zobacz wszystkie', array('class' => 'btn btn-primary btn-block'));
									?>
								</div>
							</span>
						</div>
						<div class="right">
							<i class="glyphicon glyphicon-comment bootstrap-icon">
								<div class="wide ttip panel">
									<?
										echo '<div style="margin-bottom: 10px;">';
										$messages = ORM::factory("Message")->where('user_id', '=', $profil['id'])->and_where('typ', '=', 1)->order_by('data', 'desc')->limit(5)->find_all();
                                        if($messages->count() == 0)
                                            echo "<div class='text-center'>Brak wiadomości.</div><br />";
										foreach($messages as $msg)
										{
											$sender = ORM::factory("User", $msg->sender_id);
											$senderName = "";
											if($sender->loaded())
												$senderName = $sender->username;
											echo '<div class="ttip-row">';
											echo TimeFormat::timeDeltaInWords($msg->data).' ('.$senderName.'): '.$msg->title;
											echo "</div>";
										}
										echo "</div>".HTML::anchor('poczta', 'Zobacz wszystkie', array('class' => 'btn btn-primary btn-block'));
									?>
								</div>
							</i>
						</div>
						<div class="right">
							<i class="glyphicon glyphicon-info-sign bootstrap-icon" id="tutorial_activate"></i>
						</div>
					</div>
					<?
						$flights = ORM::factory("Flight")->where('started', '<=', time())->and_where('user_id', '=', $profil['id'])->and_where('end', '>=', time())->and_where('canceled', '=', 0)->order_by('end', 'ASC')->limit(3)->find_all();
						if($flights->count() > 0) { ?>
							<div class="field panel hidden-xs">
								<div class="left">
									<i class="glyphicon glyphicon-plane bootstrap-icon"></i>
								</div>
								<div class="right">
									<?
										$first = true;
										foreach($flights as $f)
										{
											$plane = $f->UserPlane;
											if( ! $plane->loaded())
												continue;
											echo ($first) ? "<b>" : '';
											echo $plane->rejestracja.": <span class='zegarCountdown' czas='".$f->end."' now='".time()."'>". TimeFormat::secondsToText($f->end - time()) ."</span> (".date("H:i", $f->end).")";
											echo ($first) ? "</b>" : '';
											echo "<br />";
											$first = false;
										}
									?>
								</div>
							</div>
						<? } ?>
						<div class="field panel">
							<div style="padding: 5px; text-align: center;">
							<small>Kurs paliwa</small><br />
							<h4 style="margin-top: 0;"><?=Oil::getOilCost() . ' ' . WAL;?></h4>
							</div>
						</div>
					<? } ?>
					<!--<div class="field panel">
						<div class="fb-like" style="margin: 5px; margin-top: 13px;" data-href="https://www.facebook.com/pages/Global-Airlines-Simulator/1427930397429929" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
					</div> -->
				</div>
                <div class="main">
                    <div class="mainScrollable">
                        <? Prints::printMsg(); ?>
                        <? Prints::printErrors(); ?>
<?=((isset($content)) ? $content : '');?>
						<div id="tutorialLayerInner"></div>
                    </div>
                </div>
				<? if($logged) { ?>
				<div class="tmp-chat hidden-xs">
					<div class="bottom-icon" id="tmp-chat-trigger">
						<div class="icon"></div>
					</div>
					<div class="header" onselectstart="return false" onselect="return false">
						<div class="sets">
							<i class="glyphicon glyphicon-flash" id="chatHeaderSetDings" on="1" data-container="body" data-toggle="tooltip" data-placement="top" title="Miganie czatu"></i>
							<i class="glyphicon glyphicon-circle-arrow-up" id="chatHeaderSetAutoUp" on="1" data-container="body" data-toggle="tooltip" data-placement="top" title="Automatyczne przewijanie"></i>
						</div>
						<div class="channel">LOCAL <p id="chat-status" class="glyphicon glyphicon-remove red"></p></div>
						<div class="right">
							<? if($logged && $profil['admin']) { ?><button id="tmp-admin-trigger" class="btn btn-primary btn-xs">ADMIN <i class="glyphicon glyphicon-chevron-left"></i></button><? } ?>
						</div>
					</div>
					<div class="body">
						<input type="text" class="input" placeholder="Napisz na chacie..."/>
						<div id="content" class="content">
							<table style="width: 100%; margin: 0px;" class="table table-striped">
							</table>
						</div>
					</div>
					<? if($logged && $profil['admin']) { ?>
					<div id="tmp-admin" class="tmp-admin">
						<div id="admin-zbanuj" class="btn btn-primary">
							Zbanuj gracza
						</div>
						<div id="admin-odbanuj" class="btn btn-primary">
							Odbanuj gracza
						</div>
						<div id="admin-zmutuj" class="btn btn-primary">
							Zmutuj gracza
						</div>
						<div id="admin-odmutuj" class="btn btn-primary">
							Odmutuj gracza
						</div>
						<div id="admin-kasa" class="btn btn-primary">
							Daj graczowi <?=WAL?>
</div>
<?=HTML::anchor("admin/updates", '<div class="btn btn-primary">
							Czasy updatów
						</div>');?>
</div>
					<? } ?>
				</div>
				<? } ?>
			</div>
		</div>
</div>
<div id="tutorialLayer"></div>
<div id="userToken" token="<?=(isset($profil) && isset($profil['token'])) ? $profil['token'] : ""?>"></div>
<?=(isset($modals)) ? $modals : '';?>
<?php //echo View::factory('profiler/stats') ?>
<? if($logged) { ?>
<script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>
<script type="text/javascript" src="<?php echo URL::base(TRUE);?>bower_components/sails.io.js/sails.io.js"></script>
<script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/chat.js"></script>
<? if($logged && $profil['admin']) { ?> <script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/admin.js"></script><? } ?>
<script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo URL::base(TRUE);?>assets/js/tutorial.js"></script>

<? } ?>

<script type="text/javascript">
	<? if($logged) { ?>
		io.sails.url = 'http://ws.planes.vipserv.org';
	<? } ?>
    $(function () {

        $('.rusureButtonGroup #cancelCheckbox').on('click', function() {
            $(this).parent().parent().find("#rusureButton").prop('disabled', !$(this).is(":checked"));
        });

    	$('#menu a').mouseover(function() { $(this).addClass('animated tada'); });
    	$('#menu a').mouseout(function() { $(this).removeClass('animated tada'); });


        $('.mainScrollable').niceScroll();
        $(".mainScrollable").trigger('mouseenter');
        $(".mainScrollable").trigger('mouseover');
        $(".mainScrollable").trigger('hover');


        function scaleMenu() {
            if($(window).height() < 610) {
                $('#clock').hide();
                $('.logo').hide();
            }
            else {
                $('#clock').show();
                $('.logo').show();
            }
            if($(window).height() < 460) {
                $('.tmp-menu .bottom-panel').hide();
            }
            else {
                $('.tmp-menu .bottom-panel').show();
            }
        }

        $(window).resize(function() {
            scaleMenu();
            $('.mainScrollable').getNiceScroll().resize();
        });
        scaleMenu();

        function formatState (state) {
            if (!$(state.element).attr('data-flag')) { return state.text; }
            return $(
                '<span><i class="flag flag-' + $(state.element).attr('data-flag') + '"></i> ' + state.text + '</span>'
            );
        };

        $('select').select2({
            templateResult: formatState
        });

		if($.cookie('chatOpen') == 1)
		{
			$("#tmp-container").addClass("no-transition");
			$("#tmp-container").addClass("tmp-chat-open");
			$(window).resize();
			setTimeout(function() { $("#tmp-container").removeClass("no-transition"); }, 1000);
		}
		<? if($logged && $profil['admin']) { ?>
		if($.cookie('adminOpen') == 1)
		{
			$("#tmp-container").addClass("tmp-admin-open");
			$("#tmp-admin-trigger i").removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
		}
		<? } ?>

		$("#tmp-chat-trigger").on('click', function(ev) {
			ev.preventDefault();
			$("#tmp-container").toggleClass("tmp-chat-open");
			setTimeout(function() { $(window).resize(); }, 1000);
			if($.cookie('chatOpen') == 1)
				$.cookie('chatOpen', 0, { expires: 31, path: '/' });
			else
				$.cookie('chatOpen', 1, { expires: 31, path: '/' });
		});
		<? if($logged && $profil['admin']) { ?>
		$("#tmp-admin-trigger").on('click', function(ev) {
			ev.preventDefault();
			$("#tmp-container").toggleClass("tmp-admin-open");
			$("#tmp-admin-trigger i").toggleClass("glyphicon-chevron-left").toggleClass("glyphicon-chevron-right");
			if($.cookie('adminOpen') == 1)
				$.cookie('adminOpen', 0, { expires: 31, path: '/' });
			else
				$.cookie('adminOpen', 1, { expires: 31, path: '/' });
		});
		<? } ?>

		$(".ttip-row").click(function() {
			if($(this).attr('mid') == undefined)
				return false;

			var url = url_base()+"index.php/ajax/deleteMiniMessage/"+$(this).attr('mid');
			$.ajax(url);
			$(this).removeClass('actual');
			if ($(".ttip .ttip-row.actual").length <= 0)
				$(".blink").removeClass('blink');
		}).popover();

		$(".top-panel .field").on('mouseleave', function() {
			$(".ttip-row").popover('hide');
		});

		//Przeniesc
		$(".clickableRow td").click(function() {
			if( ! $(this).hasClass('non-clickable'))
				window.document.location = $(this).parent().attr("href");
		});
		//Podglad
		$(".Jtooltip").tooltip();
		$("[data-toggle='tooltip']").tooltip();
		$(".Jpopover").popover();
		<? if($logged) { ?>
		//Expbar
		$( "#expbar" ).progressbar({
			value: <?=$profil['expPercent'];?>
		}).find( ".ui-progressbar-value" ).css({
		  "margin": '0px'
        });
		<? } ?>
    });
</script>

<script type="text/javascript">
function microtime(get_as_float){
	var unixtime_ms = new Date().getTime();
    var sec = parseInt(unixtime_ms / 1000);
    return get_as_float ? (unixtime_ms/1000) : (unixtime_ms - (sec * 1000))/1000 + ' ' + sec;
}
function pageReload() {
	var highestTimeoutId = setTimeout(";");
	for (var i = 0 ; i <highestTimeoutId ; i++) {
		clearTimeout(i);
	}
	location.reload();
}

//Przeniesc do oddzielnego pliku
$(function() {

	$('.zegarCountdown').each(function() {
		var zegar = this;
		var timer = new Tock({
		  countdown: true,
		  interval: 1000,
		  callback: function () {
				var czas = Math.round(timer.lap() / 1000);
				minut = Math.floor(czas/60);
				czas = czas % 60;

				godzin = Math.floor(minut/60);
				minut = minut % 60;

				dni = Math.floor(godzin/24);
				godzin = godzin % 24;
				var zero = '';
				if(czas < 10)
					zero = '0';

				var text =((dni>0)?dni+"d ":"")+((godzin>0)?godzin+"h ":"")+((minut>0)?minut+"m ":"")+zero+czas+'s';
				$(zegar).text(text);
			},
		  complete: function () {
				setTimeout(pageReload, 3000);
		    }
		});
		timer.start(($(this).attr('czas') - $(this).attr('now'))*1000);
	});

	function startTime() {
		var today=new Date();
		var h=today.getHours();
		var m=today.getMinutes();
		var s=today.getSeconds();
		m = checkTime(m);
		s = checkTime(s);
		$("#clock").text(h+":"+m+":"+s);
		var t = setTimeout(function(){startTime()},500);
	}

	function checkTime(i) {
		if (i<10) {i = "0" + i};
		return i;
	}
	startTime();
});
</script>



<script>
	$("#fb_login").click(function( event ) {
		event.preventDefault();
		window.location = "<?=$fb_loginPath;?>";
	});
</script>
</body>
</html>