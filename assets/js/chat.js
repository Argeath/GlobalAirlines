;;(function($){$.winFocus||($.extend({winFocus:function(){var a=!0;$(document).data("winFocus")||$(document).data("winFocus",$.winFocus.init());for(x in arguments)"object"==typeof arguments[x]?(arguments[x].blur&&($.winFocus.methods.blur=arguments[x].blur),arguments[x].focus&&($.winFocus.methods.focus=arguments[x].focus),arguments[x].blurFocus&&($.winFocus.methods.blurFocus=arguments[x].blurFocus),arguments[x].initRun&&(a=arguments[x].initRun)):"function"==typeof arguments[x]?
void 0===$.winFocus.methods.blurFocus?$.winFocus.methods.blurFocus=arguments[x]:($.winFocus.methods.blur=$.winFocus.methods.blurFocus,$.winFocus.methods.blurFocus=void 0,$.winFocus.methods.focus=arguments[x]):"boolean"==typeof arguments[x]&&(a=arguments[x]);if(a)$.winFocus.methods.onChange()}}),$.winFocus.init=function(){$.winFocus.props.hidden in document?document.addEventListener("visibilitychange",$.winFocus.methods.onChange):($.winFocus.props.hidden=
"mozHidden")in document?document.addEventListener("mozvisibilitychange",$.winFocus.methods.onChange):($.winFocus.props.hidden="webkitHidden")in document?document.addEventListener("webkitvisibilitychange",$.winFocus.methods.onChange):($.winFocus.props.hidden="msHidden")in document?document.addEventListener("msvisibilitychange",$.winFocus.methods.onChange):($.winFocus.props.hidden="onfocusin")in document?document.onfocusin=document.onfocusout=$.winFocus.methods.onChange:
window.onpageshow=window.onpagehide=window.onfocus=window.onblur=$.winFocus.methods.onChange;return $.winFocus},$.winFocus.methods={blurFocus:void 0,blur:void 0,focus:void 0,exeCB:function(a){$.winFocus.methods.blurFocus?$.winFocus.methods.blurFocus(a,!a.hidden):a.hidden?$.winFocus.methods.blur&&$.winFocus.methods.blur(a):$.winFocus.methods.focus&&$.winFocus.methods.focus(a)},onChange:function(a){var b={focus:!1,focusin:!1,pageshow:!1,blur:!0,focusout:!0,
pagehide:!0};if(a=a||window.event)a.hidden=a.type in b?b[a.type]:document[$.winFocus.props.hidden],$(window).data("visible",!a.hidden),$.winFocus.methods.exeCB(a);else try{$.winFocus.methods.onChange.call(document,new Event("visibilitychange"))}catch(c){}}},$.winFocus.props={hidden:"hidden"})})(jQuery);

$(function () {
    "use strict";

	var ChatType = 1;
 
    // for better performance - to avoid searching in DOM
	var trigger = $('#tmp-chat-trigger');
    var header = $('.tmp-chat .header');
    var content = $('.tmp-chat .content');
    var input = $('.tmp-chat .input');
    var status = $('#chat-status');
    var setDings = $("#chatHeaderSetDings");
    var setAutoUp = $("#chatHeaderSetAutoUp");

    function setStatus(bl) {
        if(bl == true) {
            $('#chat-status').removeClass("red glyphicon-remove").addClass("green glyphicon-ok");
        } else {
            $('#chat-status').removeClass("green glyphicon-ok").addClass("red glyphicon-remove");
        }
    }

    var normalTitle = document.title;
    var newMessages = 0;

    var hasFocus = true;

    $.winFocus(function(event, isVisible) {
        if (isVisible) {
            hasFocus = true;
            newMessages = 0;
            refreshTitle();
        } else {
            hasFocus = false;
        }
    });

	//AutoScrolling chat
	var autoScroll = false;
	if($.cookie('chatAutoScroll') == 1)
	{
		autoScroll = true;
        setAutoUp.attr("data-on", 1).find(".przekreslenie").remove();
	} else {
        setAutoUp.attr("data-on", 0).append('<div class="przekreslenie"></div>');
	}
	
	//Dinging chat
	var dings = false;
	if($.cookie('chatDings') == 1)
	{
		dings = true;
		setDings.attr("data-on", 1).find(".przekreslenie").remove();
	} else {
		setDings.attr("data-on", 0).append('<div class="przekreslenie"></div>');
	}

    var messages = [];

	var dinging = true;
	setTimeout(function() { dinging = false; }, 1500);

	var userToken = $("#userToken").attr('token');

    io.socket.get('/getHistory', function serverResponded (body, JWR) {
        if(body.success == true) {
            setStatus(true);
        } else {
            setStatus(false);
        }
        if(body.err) {
            console.log(body.err);
        }
    });

    //io.socket.on('connect', function () {

    messages = [];
    refreshMessages();

	io.socket.post('/user', {token: userToken}, function serverResponded (body, JWR) {
        if(body.success == true) {
            setStatus(true);
        } else {
            setStatus(false);
        }
        if(body.err) {
            addServerMessage(body.err);
            console.log(body.err);
        }
    });

    io.socket.on('message', function(json) {
        console.log(json);
        messages[messages.length] = json;
        refreshMessages();
    });

	/**
	 * Send mesage when user presses Enter key
	 */
	input.keydown(function(e) {
		if (e.keyCode === 13) {
			var msg = $(this).val();
			if (!msg) {
				return;
			}
            io.socket.post('/addMessage', { token: userToken, msg: msg },  function serverResponded (body, JWR) {
                console.log(body);

                if(body.hasOwnProperty('err')) {
                    addServerMessage(body.err);
                    console.log(body.err);
                }
            });

			$(this).val('');
		}
	});

	

    function refreshMessages() {
        content.find('.table tr').remove();
        messages.sort(function (a, b) {
            if (a == null || b == null) return false;
            return a.date - b.date
        });
        for (var i = 0; i < messages.length; i++) {
            if (messages[i] != null)
                addMessage(messages[i].author, messages[i].msg,
                    messages[i].avatar, messages[i].date);
        }
    }
    /**
     * Add message to the chat window
     */
    function addMessage(author, message, avatar, date) {
        var dt = new Date(date*1000);
        content.find('.table').prepend('<tr class="chatNew"><td width="40">' +
             + (dt.getHours() < 10 ? '0' + dt.getHours() : dt.getHours()) + ':'
             + (dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes()) + ':'
             + (dt.getSeconds() < 10 ? '0' + dt.getSeconds() : dt.getSeconds())
             + '</td><td width="10%"><a href="' + url_base() + 'profil/znajdz/' + author + '" class="btn btn-xs btn-primary"><img src="' + avatar + '" style="height: 20px; border-radius: 5px;"/> ' + author + '</a></td><td>' + message + '</td></tr>');

		if(autoScroll)
			content.scrollTop(0);
		else
			content.scrollTop(content.scrollTop()+37);
			
		onNewMessage();
    }

    function addServerMessage(message) {
        messages[messages.length] = { author: "Serwer", avatar: "/uploads/hologram.jpg", date: Math.round(+new Date()/1000)-3, msg: message };
        refreshMessages();
    }

    function dingNew() {
        $(".chatNew").each(function() {
            $(this).removeClass("chatNew");
            $(this).animate({"background-color": "rgba(255, 255, 255, 0.6)"}, 500, function() {
                $(this).animate({"background-color": "rgba(0, 0, 0, 0)"}, 500);
            });
        });
    }

    function refreshTitle() {
        if(newMessages == 0 || hasFocus) {
            document.title = normalTitle;
        } else {
            document.title = "(" + newMessages + ") " + normalTitle;
        }
    }

    function onNewMessage() {
        var container = $(".tmp-container");
        if(( ! container.hasClass("st-chat-open")) && !dinging && dings)
        {
            dinging = true;

            newMessages++;
            refreshTitle();

            setTimeout(function() { trigger.addClass('newMessage'); }, 1);
            setTimeout(function() { trigger.removeClass('newMessage'); }, 500);
            setTimeout(function() { dinging = false; }, 1000);

        } else if(container.hasClass("st-chat-open") && dinging == false && dings) {
            dingNew();
        } else {
            $(".chatNew").removeClass("chatNew");
        }
    }

	setAutoUp.tooltip().on("click", function(event) {
		event.preventDefault();
		var on = $(this).attr("data-on");
		if(on == 0)
		{
			$(this).find(".przekreslenie").remove();
			$.cookie('chatAutoScroll', 1);
			$(this).attr("data-on", 1);
			autoScroll = true;
		} else {
			$(this).append('<div class="przekreslenie"></div>');
			$.cookie('chatAutoScroll', 0);
			$(this).attr("data-on", 0);
			autoScroll = false;
		}
	});



    setDings.tooltip().on("click", function(event) {
		event.preventDefault();
		var on = $(this).attr("data-on");
		if(on == 0)
		{
			$(this).find(".przekreslenie").remove();
			$.cookie('chatDings', 1);
			$(this).attr("data-on", 1);
			dings = true;
		} else {
			$(this).append('<div class="przekreslenie"></div>');
			$.cookie('chatDings', 0);
			$(this).attr("data-on", 0);
			dings = false;
		}
	});
});