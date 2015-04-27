$(function () {
    "use strict";
 
    // for better performance - to avoid searching in DOM
	var trigger = $('#st-chat-trigger');
    var header = $('.st-chat').find('.header');
    var content = $('.st-chat').find('.content');
    var input = $('.st-chat').find('.input');
	
	//AutoScrolling chat
	var autoScroll = false;
	if($.cookie('chatAutoScroll') == 1)
	{
		autoScroll = true;
		$('#chatHeaderSetAutoUp').find(".przekreslenie").remove();
		$('#chatHeaderSetAutoUp').attr("on", 1);
	} else {
		$('#chatHeaderSetAutoUp').append('<div class="przekreslenie"></div>');
		$('#chatHeaderSetAutoUp').attr("on", 0);
	}
	
	//Dinging chat
	var dings = false;
	if($.cookie('chatDings') == 1)
	{
		dings = true;
		$('#chatHeaderSetDings').find(".przekreslenie").remove();
		$('#chatHeaderSetDings').attr("on", 1);
	} else {
		$('#chatHeaderSetDings').append('<div class="przekreslenie"></div>');
		$('#chatHeaderSetDings').attr("on", 0);
	}
	
	var dinging = true;
	setTimeout(function() { dinging = false; }, 1500);
	
	
	
    // my name sent to the server
    var username = $('#playerNick').text();
    var password = $('#playerPass').text();
	var logged = false;
	
	
    // if user is running mozilla then use it's built-in WebSocket
    window.WebSocket = window.WebSocket || window.MozWebSocket;
 
    // if browser doesn't support WebSocket, just show some notification and exit
    if (! WebSocket || !window.WebSocket) {
        input.attr('disabled', 'disabled').text('Sorry, but your browser doesn\'t '
                                    + 'support WebSockets.');
    }
    if ("https:" != document.location.protocol) {
        // open connection
        try {
            var connection = new WebSocket('ws://otstest.matinf.pl:8080/');
        } catch (err) {
            console.log(err.message);
        }
        connection.onopen = function () {
            // first we want users to enter their names
            input.removeAttr('disabled');
            input.text("");
            connection.send(username + ":" + password);
        };

        connection.onerror = function (error) {
            // just in there were some problems with conenction...
            input.attr('disabled', 'disabled').text('Sorry, but your browser doesn\'t '
									    + 'support WebSockets.');
            console.log(error);
        };

        // most important part - incoming messages
        connection.onmessage = function (message) {
            // try to parse JSON message. Because we know that the server always returns
            // JSON this should work without any problem but we should make sure that
            // the massage is not chunked or otherwise damaged.
            try {
                var json = JSON.parse(message.data);
            } catch (e) {
                console.log('This doesn\'t look like a valid JSON: ', message.data);
                return;
            }
            // NOTE: if you're not sure about the JSON structure
            // check the server source code above
            if (json.type === 'login') {
                var ret = json.data;
                if (ret == 'successful') {
                    logged = true;
                    input.removeAttr('disabled').focus();
                } else {
                    logged = false;
                    input.text("You are not logged in.");
                }
            } else if (json.type === 'history') { // entire message history
                // insert every single message to the chat window
                if (json.data == null)
                    return;
                json.data.sort(function (a, b) { if (a == null || b == null) return false; return a['time'] - b['time'] });
                for (var i = 0; i < json.data.length; i++) {
                    if (json.data[i] != null)
                        addMessage(json.data[i]['author'], json.data[i]['text'],
						    json.data[i]['avatar'], new Date(json.data[i]['time']));
                }
            } else if (json.type === 'message') { // it's a single message
                input.removeAttr('disabled'); // let the user write another message
                addMessage(json.data['author'], json.data['text'],
					       json.data['avatar'], new Date(json.data['time']));
            } else {
                console.log('Hmm..., I\'ve never seen JSON like this: ', json);
            }
        };

        /**
	     * Send mesage when user presses Enter key
	     */
        input.keydown(function (e) {
            if (e.keyCode === 13) {
                var msg = $(this).val();
                if (!msg) {
                    return;
                }
                // send the message as an ordinary text
                connection.send(msg);
                $(this).val('');
                // disable the input field to make the user wait until server
                // sends back response
                input.attr('disabled', 'disabled');
                input.removeAttr('disabled').focus();
            }
        });

        /**
	     * This method is optional. If the server wasn't able to respond to the
	     * in 3 seconds then show some error message to notify the user that
	     * something is wrong.
	     */
        setInterval(function () {
            if (connection.readyState !== 1) {
                input.attr('disabled', 'disabled').val('Unable to comminucate '
												     + 'with the Chat server.');
                //setTimeout(connectChat, 1000);
            } else {
                if (input.val() == 'Unable to comminucate with the Chat server.')
                    input.attr('disabled', false).val('');
            }
        }, 10000);
    }
    /**
     * Add message to the chat window
     */
    function addMessage(author, message, avatar, dt) {
        content.find('.table').prepend('<tr class="chatNew"><td style="width: 50px;">' +
             + (dt.getHours() < 10 ? '0' + dt.getHours() : dt.getHours()) + ':'
             + (dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes())
             + '</td><td style="width: 150px;"><a href="' + url_base() + 'profil/znajdz/' + author + '" class="btn btn-xs btn-primary";"><img src="' + url_base() + 'uploads/' + avatar + '.jpg" style="height: 20px; border-radius: 5px;"/> ' + author + '</a></td><td>' + message + '</td></tr>');
			 
		if(autoScroll) {
			content.scrollTop(0);
		} else
			content.scrollTop(content.scrollTop()+37);
		onNewMessage();
    }
	
	function dingNew() {
		$(".chatNew").each(function() {
			$(this).removeClass("chatNew");
			$(this).animate({"background-color": "rgba(255, 255, 255, 0.6)"}, 500, function() {
				$(this).animate({"background-color": "rgba(0, 0, 0, 0)"}, 500);
			});
		});
	
	}
	
	function onNewMessage() {
		if(( ! $(".st-container").hasClass("st-chat-open")) && !dinging && dings)
		{
			dinging = true;
			setTimeout(function() { trigger.addClass('newMessage'); }, 1);
			setTimeout(function() { trigger.removeClass('newMessage'); }, 500);
			setTimeout(function() { dinging = false; }, 1000);
			
		} else if($(".st-container").hasClass("st-chat-open") && dinging == false && dings) {
			dingNew();
		} else {
			$(".chatNew").removeClass("chatNew");
		}
	};
	
	$("#chatHeaderSetAutoUp").tooltip().on("click", function(event) {
		event.preventDefault();
		var on = $(this).attr("on");
		if(on == 0)
		{
			$(this).find(".przekreslenie").remove();
			$.cookie('chatAutoScroll', 1);
			$(this).attr("on", 1);
			autoScroll = true;
		} else {
			$(this).append('<div class="przekreslenie"></div>');
			$.cookie('chatAutoScroll', 0);
			$(this).attr("on", 0);
			autoScroll = false;
		}
	});
	
	
	
	$("#chatHeaderSetDings").tooltip().on("click", function(event) {
		event.preventDefault();
		var on = $(this).attr("on");
		if(on == 0)
		{
			$(this).find(".przekreslenie").remove();
			$.cookie('chatDings', 1);
			$(this).attr("on", 1);
			dings = true;
		} else {
			$(this).append('<div class="przekreslenie"></div>');
			$.cookie('chatDings', 0);
			$(this).attr("on", 0);
			dings = false;
		}
	});
});