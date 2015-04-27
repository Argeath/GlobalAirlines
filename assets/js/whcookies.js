$(function() {
function WHCreateCookie(name, value, days) {
    var date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    var expires = "; expires=" + date.toGMTString();
	document.cookie = name+"="+value+expires+"; path=/";
}
function WHReadCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

window.onload = WHCheckCookies;

function WHCloseCookiesWindow() {
    WHCreateCookie('cookies_accepted', 'T', 365);
    $('#cookies-message').animate({ height: 0, padding: 0 }, 1000, function() { $(this).remove(); });
}

function WHCheckCookies() {
    if(WHReadCookie('cookies_accepted') != 'T') {
        var message_container = document.createElement('div');
        message_container.id = 'cookies-message-container';
        var html_code = '<div id="cookies-message" class="panel_fw" style="margin: 0;padding: 10px 0px; font-size: 14px; line-height: 22px; text-align: center; position: relative; top: 0px; width: 100%; border-top: 0; border-top-left-radius: 0; border-top-right-radius: 0;">Ta strona używa ciasteczek (cookies), dzięki którym nasz serwis może działać lepiej. <a href="http://wszystkoociasteczkach.pl" target="_blank">Dowiedz się więcej</a><a id="accept-cookies-checkbox" name="accept-cookies" style="background-color: #00AFBF; padding: 5px 10px; color: #FFF; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; display: inline-block; margin-left: 10px; text-decoration: none; cursor: pointer;">Rozumiem</a></div>';
		message_container.innerHTML = html_code;
        document.body.insertBefore(message_container, document.body.childNodes[0]);
		$('#accept-cookies-checkbox').click(function() {
			WHCloseCookiesWindow();
		});
    }
}
});