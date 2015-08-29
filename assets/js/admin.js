$(function() {
	var act = null;
	$("#admin-zbanuj").click(function(e) {
		e.preventDefault();
		if(act == this)
			return;
		
		var dialog = $("<div/>").attr({id: "DialogAdminZbanuj", title: "Zbanuj gracza"}).appendTo('body');
		var form = $("<form/>").attr({action: "/admin/zbanuj/", method: "post"}).appendTo(dialog);
		var data = $("<input/>").attr({id: "data", name: "data", class: "form-control", placeholder: "Ban do...", "data-format": "dd/MM/yyyy hh:mm:ss", type: "text"}).prependTo(form)
			.datetimepicker({
				 lang:"pl",
				 timepicker:true,
				 format:"d.m.Y H:i",
				 minDate:0,
				 step:60
			});
		var input = $("<input/>").attr({type: "text", name: "user", class: "form-control", placeholder: "Podaj nick"}).prependTo(form)
			.autocomplete({
				source: function( request, response ) {
				  $.getJSON(url_base()+"ajax/searchUser/"+request.term, response );
				},
				minLength: 2
			});
		
		form.append('<button class="btn btn-primary btn-block">Zbanuj</button>');
		
		dialog.dialog({
		  dialogClass: "no-close",
		  resizable: false,
		  autoOpen: true,
		  height: 250,
		  width: 400,
		  modal: true,
		  buttons: {
			"Zamknij": function() {
			  $( this ).dialog( "close" );
			}
		  },
		  close: function() {
			act = null;
			dialog.remove();
		  }
		});
		act = this;
	});
	
	$("#admin-odbanuj").click(function(e) {
		e.preventDefault();
		if(act == this)
			return;
		
		var dialog = $("<div/>").attr({id: "DialogAdminOdbanuj", title: "Odbanuj gracza"}).appendTo('body');
		var form = $("<form/>").attr({action: "/admin/odbanuj/", method: "post"}).appendTo(dialog);
		var input = $("<input/>").attr({type: "text", name: "user", class: "form-control", placeholder: "Podaj nick"}).prependTo(form)
			.autocomplete({
				source: function( request, response ) {
				  $.getJSON(url_base()+"ajax/searchUser/"+request.term, response );
				},
				minLength: 2
			});
		
		form.append('<button class="btn btn-primary btn-block">Odbanuj</button>');
		
		dialog.dialog({
		  dialogClass: "no-close",
		  resizable: false,
		  autoOpen: true,
		  height: 250,
		  width: 400,
		  modal: true,
		  buttons: {
			"Zamknij": function() {
			  $( this ).dialog( "close" );
			}
		  },
		  close: function() {
			act = null;
			dialog.remove();
		  }
		});
		act = this;
	});
	
	$("#admin-zmutuj").click(function(e) {
		e.preventDefault();
		if(act == this)
			return;
		
		var dialog = $("<div/>").attr({id: "DialogAdminZmutuj", title: "Zmutuj gracza"}).appendTo('body');
		var form = $("<form/>").attr({action: "/admin/zmutuj/", method: "post"}).appendTo(dialog);
		var data = $("<input/>").attr({id: "data", name: "data", class: "form-control", placeholder: "Mute do...", "data-format": "dd/MM/yyyy hh:mm:ss", type: "text"}).prependTo(form)
			.datetimepicker({
				 lang:"pl",
				 timepicker:true,
				 format:"d.m.Y H:i",
				 minDate:0,
				 step:10
			});
		var input = $("<input/>").attr({type: "text", name: "user", class: "form-control", placeholder: "Podaj nick"}).prependTo(form)
			.autocomplete({
				source: function( request, response ) {
				  $.getJSON(url_base()+"ajax/searchUser/"+request.term, response );
				},
				minLength: 2
			});
		
		form.append('<button class="btn btn-primary btn-block">Zmutuj</button>');
		
		dialog.dialog({
		  dialogClass: "no-close",
		  resizable: false,
		  autoOpen: true,
		  height: 250,
		  width: 400,
		  modal: true,
		  buttons: {
			"Zamknij": function() {
			  $( this ).dialog( "close" );
			}
		  },
		  close: function() {
			act = null;
			dialog.remove();
		  }
		});
		act = this;
	});
	
	$("#admin-odmutuj").click(function(e) {
		e.preventDefault();
		if(act == this)
			return;
		
		var dialog = $("<div/>").attr({id: "DialogAdminOdmutuj", title: "Odmutuj gracza"}).appendTo('body');
		var form = $("<form/>").attr({action: "/admin/odmutuj/", method: "post"}).appendTo(dialog);
		var input = $("<input/>").attr({type: "text", name: "user", class: "form-control", placeholder: "Podaj nick"}).prependTo(form)
			.autocomplete({
				source: function( request, response ) {
				  $.getJSON(url_base()+"ajax/searchUser/"+request.term, response );
				},
				minLength: 2
			});
		
		form.append('<button class="btn btn-primary btn-block">Odmutuj</button>');
		
		dialog.dialog({
		  dialogClass: "no-close",
		  resizable: false,
		  autoOpen: true,
		  height: 250,
		  width: 400,
		  modal: true,
		  buttons: {
			"Zamknij": function() {
			  $( this ).dialog( "close" );
			}
		  },
		  close: function() {
			act = null;
			dialog.remove();
		  }
		});
		act = this;
	});
	
	$("#admin-kasa").click(function(e) {
		e.preventDefault();
		if(act == this)
			return;
		
		var dialog = $("<div/>").attr({id: "DialogAdminZmutuj", title: "Daj graczowi pieniądze"}).appendTo('body');
		var form = $("<form/>").attr({action: "/admin/kasa/", method: "post"}).appendTo(dialog);
		var kasa = $("<input/>").attr({id: "kasa", name: "kasa", class: "form-control", placeholder: "Ilość pieniędzy (Aby zabrać daj na minusie)", type: "text"}).prependTo(form);
		var input = $("<input/>").attr({type: "text", name: "user", class: "form-control", placeholder: "Podaj nick"}).prependTo(form)
			.autocomplete({
				source: function( request, response ) {
				  $.getJSON(url_base()+"ajax/searchUser/"+request.term, response );
				},
				minLength: 2
			});
		
		form.append('<button class="btn btn-primary btn-block">Daj graczowi <i class="glyphicon glyphicon-usd"></i></button>');
		
		dialog.dialog({
		  dialogClass: "no-close",
		  resizable: false,
		  autoOpen: true,
		  height: 250,
		  width: 400,
		  modal: true,
		  buttons: {
			"Zamknij": function() {
			  $( this ).dialog( "close" );
			}
		  },
		  close: function() {
			act = null;
			dialog.remove();
		  }
		});
		act = this;
	});


});