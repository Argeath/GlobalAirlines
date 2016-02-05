;;(function($){$.winFocus||($.extend({winFocus:function(){var a=!0;$(document).data("winFocus")||$(document).data("winFocus",$.winFocus.init());for(x in arguments)"object"==typeof arguments[x]?(arguments[x].blur&&($.winFocus.methods.blur=arguments[x].blur),arguments[x].focus&&($.winFocus.methods.focus=arguments[x].focus),arguments[x].blurFocus&&($.winFocus.methods.blurFocus=arguments[x].blurFocus),arguments[x].initRun&&(a=arguments[x].initRun)):"function"==typeof arguments[x]?
void 0===$.winFocus.methods.blurFocus?$.winFocus.methods.blurFocus=arguments[x]:($.winFocus.methods.blur=$.winFocus.methods.blurFocus,$.winFocus.methods.blurFocus=void 0,$.winFocus.methods.focus=arguments[x]):"boolean"==typeof arguments[x]&&(a=arguments[x]);if(a)$.winFocus.methods.onChange()}}),$.winFocus.init=function(){$.winFocus.props.hidden in document?document.addEventListener("visibilitychange",$.winFocus.methods.onChange):($.winFocus.props.hidden=
"mozHidden")in document?document.addEventListener("mozvisibilitychange",$.winFocus.methods.onChange):($.winFocus.props.hidden="webkitHidden")in document?document.addEventListener("webkitvisibilitychange",$.winFocus.methods.onChange):($.winFocus.props.hidden="msHidden")in document?document.addEventListener("msvisibilitychange",$.winFocus.methods.onChange):($.winFocus.props.hidden="onfocusin")in document?document.onfocusin=document.onfocusout=$.winFocus.methods.onChange:
window.onpageshow=window.onpagehide=window.onfocus=window.onblur=$.winFocus.methods.onChange;return $.winFocus},$.winFocus.methods={blurFocus:void 0,blur:void 0,focus:void 0,exeCB:function(a){$.winFocus.methods.blurFocus?$.winFocus.methods.blurFocus(a,!a.hidden):a.hidden?$.winFocus.methods.blur&&$.winFocus.methods.blur(a):$.winFocus.methods.focus&&$.winFocus.methods.focus(a)},onChange:function(a){var b={focus:!1,focusin:!1,pageshow:!1,blur:!0,focusout:!0,
pagehide:!0};if(a=a||window.event)a.hidden=a.type in b?b[a.type]:document[$.winFocus.props.hidden],$(window).data("visible",!a.hidden),$.winFocus.methods.exeCB(a);else try{$.winFocus.methods.onChange.call(document,new Event("visibilitychange"))}catch(c){}}},$.winFocus.props={hidden:"hidden"})})(jQuery);

$(function () {
    "use strict";

    moment.locale("pl");
 
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

    var userDataObj = $("#userData");
	var userData = {
        id: userDataObj.attr('user-id'),
        username: userDataObj.attr('user-name'),
        thumbnail: userDataObj.attr('user-avatar')
    };

    var loc = window.location, new_uri;
    if (loc.protocol === "https:") {
        new_uri = "wss:";
    } else {
        new_uri = "ws:";
    }
    new_uri += "//global-airlines-chat.herokuapp.com";

    var socket = io.connect(new_uri);
    var isAdmin = false;

    function addMessage(msg, front) {
        var tr = $("<div/>", {
            "class": "msg",
            "msg_date": msg.date,
            "msg_text": msg.message,
            "msg_user_id": msg.user.id
        });

        var msgDate = new Date(msg.date);

        var userText = $("<div/>", {
            "class": "msg_user"
        });

        if (msg.user.thumbnail) {
            userText.prepend($("<img/>", {
                src: msg.user.thumbnail,
                "class": "avatar"
            }));
        }
        userText.append($("<a/>", {
            href: "/profil/" + msg.user.id
        }).text(msg.user.username));
        tr.append(userText);

        tr.append($("<div/>", {
            "class": "msg_text"
        }).text(msg.message));

        var msgDateMoment = moment(msg.date);

        tr.append($("<div/>", {
            "class": "msg_date"
        }).text(msgDateMoment.fromNow()));

        if (isAdmin) {
            tr.append($("<div/>", {
                "class": "msg_remove"
            }).append($("<div/>", {
                "class" : "removeMessage"
            }).on("click", function(event) {
                event.preventDefault();

                var row = $(this).parent().parent();
                var newMsg = {
                    dateStr: row.attr("msg_date"),
                    message: row.attr("msg_text"),
                    user: {
                        id: row.attr("msg_user_id")
                    }
                };

                socket.emit("remove", {
                    "adminKey": $("#adminKey").val(),
                    "msg": newMsg
                });
            }).append($("<i/>", {
                "class": "fa fa-times"
            }))));
        }

        onNewMessage();

        if (front) {
            content.append(tr);

            if(autoScroll)
                content.stop(true).animate({ scrollTop: content.prop('scrollHeight') }, "slow");

            return true;
        }

        var last;
        $(".tmp-chat .content > div").each(function () {
            var thisDate = new Date($(this).attr("msg_date"));
            if (msgDate > thisDate)
                last = this;
            else
                return false;
            return true;
        });
        if (last)
            $(last).after(tr);
        else
            content.prepend(tr);
        return true;
    }

    function addInfoMessage(msg) {
        var tr = $("<div/>", {
            "class": "msg"
        });

        tr.append($("<div/>", {
            "class": "msg_text"
        }).text(msg.message));

        content.append(tr);
        content.stop(true).animate({ scrollTop: content.height() }, "slow");

        onNewMessage();
        return true;
    }

    socket.on("connect", function() {
        content.find(".msg").remove();
        setStatus(true);
        socket.emit("login", userData);

        socket.emit("history", null);

        socket.on("message", function (msg) {
            addMessage(msg, true);
        });

        socket.on("history", function (msg) {
            addMessage(msg, false);
        });

        socket.on("update", function(msg) {
            $(".tmp-chat .content > div").each(function() {
                if (msg.date === $(this).attr("date") && msg.user.id == $(this).attr("msg_user_id")) {
                    $(this).find(".msg_text").text(msg.message);
                }
            });
        });

        socket.on("info", function(info) {
            addInfoMessage(info);
        });

        socket.on("disconnect", function() {
            setStatus(false);
        })
    });

    function sendMessage() {
        var text = $.trim(input.val());
        if (text === "")
            return;

        socket.emit("message", text);
        input.val("");
        input.focus();
    }

    $(function () {
        isAdmin = $("#adminKey").data("key") !== "";

        $("#sendMessage").on("click", function (event) {
            event.preventDefault();
            sendMessage();
        });

        input.on("keypress", function(e) {
            if (e.which === 13) {
                sendMessage();
            }
        });

        $("#loadMore").on("click", function (event) {
            event.preventDefault();
            $("#content").stop(true).animate({ scrollTop: 0 }, "slow");

            var lastMessage = $(".tmp-chat .content > div").first();
            var msg = {
                dateStr: lastMessage.attr("msg_date"),
                message: lastMessage.attr("msg_text"),
                user: {
                    id: lastMessage.attr("msg_user_id")
                }
            };

            socket.emit("history", msg);
        });
    });

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