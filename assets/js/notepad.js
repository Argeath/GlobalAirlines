$(function () {
    var notes = [];
    var status = $('.notepad .status');
    var tabs = $('.notepad .tabs');
    var content = $('.notepad .content');
    var active = null;

    function findNote(id) {
        var ret = false;
        $.each(notes, function (key, val) {
            if (val.id == id) {
                ret = val;
                return true;
            }
        });
        return ret;
    }

    function Note() {
        this.id = 0;
        this.name = "";
        this.text = "";
        this.tab = null;
        this.load = function (id, name, text) {
            this.id = id;
            this.name = name;
            this.text = text;
        };
        this.update = function () {
            changeStatus('loading');
            $.getJSON(url_base() + 'ajax/getNote/' + this.id, function (data) {
                changeStatus('done');
                this.id = data.id;
                this.name = data.name;
                this.text = data.text;
            });
        };
        this.save = function () {
            changeStatus('loading');
            var arr = { id: this.id, name: this.name, text: this.text };
			ajaxManager.addReq({
			   type: 'POST',
			   url: url_base() + 'ajax/saveNote/' + this.id,
			   data: arr,
			   success: function(data){}
		    });
        };
        this.addTab = function () {
            var thys = this;
            var li = $('<li />').attr({ 'nid': this.id }).on('click', function () {
                var id = parseInt($(this).attr('nid'));
                var note = findNote(id);
                if (note)
                    note.activate();
            }).on('dblclick', function () {
                var span = $(this).find('span');
                var input = $('<input />', {
                    'type': 'text',
                    'name': 'unique',
                    'value': span.html()
                }).keypress(function (e) {
					if (e.keyCode == 13) {
						$(this).blur();
						e.preventDefault();
					}
				});
                $(this).append(input);
                span.remove();
                input.focus();
            }).on('blur', 'input', function () {
                $(this).parent().append($('<span />').html($(this).val()));
                $(this).remove();
                thys.name = $(this).val();
                thys.save();
            });
            $('<span />').text(this.name).appendTo(li);
            li.prependTo(tabs);
            this.tab = li;
        };
        this.activate = function () {
            content.prop("readonly", false).val(this.text);
            $('.notepad .tabs .active').removeClass('active');
            this.tab.addClass('active');
            active = this;
        };
        this.delete = function () {
            var thys = this;
            if (this.id == 0) {
                content.val("");
                this.tab.remove();
                active = null;
            } else {
                $.ajax(url_base() + 'ajax/deleteNote/' + this.id)
                 .done(function () { content.val(""); thys.tab.remove(); active = null; })
                 .fail(function () { changeStatus('error'); });
            }
        };
    }

	function updateStatus() {
		if(ajaxManager.countRequests() > 0)
			changeStatus('loading');
		else
			changeStatus('done');
	}
	setInterval(updateStatus, 200);
	
    function changeStatus(type) {
        if (type == 'loading')
            status.find('i').removeClass('glyphicon-ok glyphicon-remove').addClass('glyphicon-refresh loading');
        else if (type == 'done')
            status.find('i').removeClass('glyphicon-refresh glyphicon-remove loading').addClass('glyphicon-ok');
        else if (type == 'error')
            status.find('i').removeClass('glyphicon-ok glyphicon-refresh loading').addClass('glyphicon-remove');
    }

    function getNotes() {
        changeStatus('loading');
        $.getJSON(url_base() + 'ajax/getNotes/', function (data) {
            changeStatus('done');
            $.each(data, function (key, val) {
                var note = new Note();
                note.load(val.id, val.name, val.text);
                note.addTab();
                notes.push(note);
            });
        });
    }

    content.prop("readonly", true);
    getNotes();

    content.on('blur', function () {
        if (active != null) {
            active.text = content.val();
            active.save();
        }
    });

    $(".notepad ul .new").on('click', function () {
		if(notes.length > 20)
			return alert('Nie możesz stworzyć więcej notatek.');
        var note = new Note();
        note.addTab();
        note.activate();
        note.update();
        
        notes.push(note);
    });

    $(".notepad .delete").on('click', function () {
        if (active != null)
            active.delete();
    })
});