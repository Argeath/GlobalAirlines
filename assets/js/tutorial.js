$(function () {
    var ArrowTypes = {
        EASY: {
            LEFT: {
                SIZE: {X: 102, Y: 76},
                POINT: { X: 0, Y: 0 },
                CLASS: "sprite-pd-lge"
            },
            RIGHT: {
                SIZE: { X: 102, Y: 76 },
                POINT: { X: 1, Y: 0 },
                CLASS: "sprite-ld-pge"
            }
        },
        HARD: {
            TOP: {
                LEFT: {
                    SIZE: { X: 68, Y: 107 },
                    POINT: { X: 0, Y: 0 },
                    CLASS: "sprite-pd-lgh"
                },
                RIGHT: {
                    SIZE: { X: 68, Y: 107 },
                    POINT: { X: 1, Y: 0 },
                    CLASS: "sprite-ld-pgh"
                }
            },
            BOTTOM: {
                LEFT: {
                    SIZE: { X: 107, Y: 68 },
                    POINT: { X: 0, Y: 1 },
                    CLASS: "sprite-pg-ldh"
                },
                RIGHT: {
                    SIZE: { X: 107, Y: 68 },
                    POINT: { X: 1, Y: 1 },
                    CLASS: "sprite-lg-pdh"
                }
            }
        },
        SITE: {
            TOP: {
                SIZE: { X: 26, Y: 107 },
                POINT: { X: 0.5, Y: 0 },
                CLASS: "sprite-gora"
            },
            BOTTOM: {
                SIZE: { X: 26, Y: 107 },
                POINT: { X: 0.5, Y: 1 },
                CLASS: "sprite-dol"
            },
            LEFT: {
                SIZE: { X: 107, Y: 26 },
                POINT: { X: 0, Y: 0.5 },
                CLASS: "sprite-lewo"
            },
            RIGHT: {
                SIZE: { X: 107, Y: 26 },
                POINT: { X: 1, Y: 0.5 },
                CLASS: "sprite-prawo"
            }
        },
        stringToType: function(type)
        {
            if (type == 'Easy_Left')
                type = ArrowTypes.EASY.LEFT;
            else if (type == 'Easy_Right')
                type = ArrowTypes.EASY.RIGHT;
            else if (type == 'Hard_Top_Left')
                type = ArrowTypes.HARD.TOP.LEFT;
            else if (type == 'Hard_Top_Right')
                type = ArrowTypes.HARD.TOP.RIGHT;
            else if (type == 'Hard_Bottom_Left')
                type = ArrowTypes.HARD.BOTTOM.LEFT;
            else if (type == 'Hard_Bottom_Right')
                type = ArrowTypes.HARD.BOTTOM.RIGHT;
            else if (type == 'Site_Bottom')
                type = ArrowTypes.SITE.BOTTOM;
            else if (type == 'Site_Top')
                type = ArrowTypes.SITE.TOP;
            else if (type == 'Site_Left')
                type = ArrowTypes.SITE.LEFT;
            else if (type == 'Site_Right')
                type = ArrowTypes.SITE.RIGHT;
            return type;
        }
    };

    var layer = $('#tutorialLayer');
    var layerInner = $('#tutorialLayerInner');
    var id = 0;
    var boxSize = { X: 300, Y: 65 };


    function drawField(type, typeMini, target, text, lay, blink, visible) {
        var blinkT = "";
        if (blink)
            blinkT = "blink_me";
        var visibleT = "";
        if (!visible)
            visibleT = "not-visible";

        type = ArrowTypes.stringToType(type);
        typeMini = ArrowTypes.stringToType(typeMini);

        var window_width = $(window).width();
        if (window_width < 992)
            type = typeMini;

        var targetD = target[0];
        var rect = targetD.getBoundingClientRect();

        var divSize = {
            X: type.SIZE.X + boxSize.X,
            Y: type.SIZE.Y + boxSize.Y
        };

        if (type.POINT.X == 0.5)
            divSize.X = boxSize.X;
        if (type.POINT.Y == 0.5)
            divSize.Y = boxSize.Y;

        var targetPoint = { X: 0, Y: 0 };
        if (type.POINT.X == 0 && type.POINT.Y == 0)
            targetPoint = { X: rect.right, Y: rect.bottom };
        else if (Math.ceil(type.POINT.X) == 1 && type.POINT.Y == 0)
            targetPoint = { X: rect.left, Y: rect.bottom };
        else if (type.POINT.X == 0 && Math.ceil(type.POINT.Y) == 1)
            targetPoint = { X: rect.right, Y: rect.top};
        else if (Math.ceil(type.POINT.X) == 1 && Math.ceil(type.POINT.Y) == 1)
            targetPoint = { X: rect.left, Y: rect.top };

        if (type.POINT.X == 0.5)
            targetPoint.X = (rect.right + rect.left) / 2;

        if (type.POINT.Y == 0.5)
            targetPoint.Y = (rect.bottom + rect.top) / 2;

        var parentOffset = lay.parent().offset();
        targetPoint.X -= parentOffset.left;
        targetPoint.Y -= parentOffset.top;
        //console.log(targetPoint);
        
        var divPos = { X: targetPoint.X - (type.POINT.X * divSize.X), Y: targetPoint.Y - (type.POINT.Y * divSize.Y) };

        var div = $('<div/>', {
            id: 'field_' + ++id,
            class: 'field ' + visibleT,
            style: 'width: ' + divSize.X + 'px; height: ' + divSize.Y + 'px; top: ' + divPos.Y + 'px; left: ' + divPos.X + 'px;'
        }).appendTo(lay);

        var arrowPos = {
            X: (type.POINT.X * divSize.X) - (type.POINT.X * type.SIZE.X),
            Y: (type.POINT.Y * divSize.Y) - (type.POINT.Y * type.SIZE.Y)
        };

        $('<div/>', {
            id: 'arrow_' + id,
            class: blinkT + ' sprite ' + type.CLASS,
            style: 'top: ' + arrowPos.Y + 'px; left: ' + arrowPos.X + 'px;'
        }).appendTo(div);

        var textPos = {
            X: ((!Math.ceil(type.POINT.X) << 0) * type.SIZE.X),
            Y: ((!Math.ceil(type.POINT.Y) << 0) * type.SIZE.Y)
        };

        var textDiv = $('<div/>', {
            id: 'text_' + id,
            style: 'width: ' + boxSize.X + 'px; height: ' + boxSize.Y + 'px; top: ' + textPos.Y + 'px; left: ' + textPos.X + 'px;',
            class: 'textDiv ' + blinkT
        }).appendTo(div);
        var span = $('<span/>').appendTo(textDiv);
        span.text(text);
        return div;
    }
	
	/*function drawTutorial()
	{
		var step = window.tutorialStep;
	}*/

    function drawInfo()
    {
        var layVisible = 0;
        var layInnerVisible = 0;
        window.tutorialElements.forEach(function (e) {
            if (e.layer == 'layer' && e.visible)
                layVisible++;
            else if (e.visible)
                layInnerVisible++;

            drawField(e.type, e.typeMini, e.target, e.txt, e.layer, e.blink, e.visible);
            e.callbacks();
        });

        if (($.trim(layerInner.html()) != '' || $.trim(layer.html()) != '') && (layInnerVisible > 0 || layVisible > 0)) {
            layerInner.addClass('active');
            drawCloseButton();
        } else {
            layerInner.removeClass('active');
        }
    }

    function drawCloseButton()
    {
        var close = $("<div/>", {
            id: 'close',
            class: 'sprite sprite-close'
        }).appendTo(layer);
        close.on('click', function () {
            window.tutorialElements.forEach(function (e) {
                if (!e.tutorial)
                    e.visible = false;
            });
            updateTutorials();
        });
        //drawField(ArrowTypes.SITE.RIGHT, close, 'Kliknij aby zamknąć pomoc', layerInner, false, true);
        return close;
    }

    $('#tutorial_activate').on('click', function () {
        window.tutorialElements.forEach(function (e) {
            if (!e.tutorial && ! e.visible)
                e.visible = true;
			else if(!e.tutorial && e.visible)
				e.visible = false;
        });
        updateTutorials();
    });

    function updateTutorials()
    {
        layer.empty();
        layerInner.empty();
        id = 0;
        drawInfo();
    }
    
    drawInfo();
    $(window).resize(function () {
        updateTutorials();
    });
    $('.main').scroll(function () {
        updateTutorials();
    });
});