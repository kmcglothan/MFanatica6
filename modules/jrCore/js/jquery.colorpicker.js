jQuery.fn.colourPicker = function(conf)
{
    // Config for plug
    var config = jQuery.extend({
        id: 'jqp',	 	// id of colour-picker container
        ico: 'ico.gif',	// SRC to colour-picker icon
        title: '',	        // Default dialogue title
        inputBG: true,		// Whether to change the input's background to the selected colour's
        speed: 300,		// Speed of dialogue-animation
        openTxt: 'Open color picker'
    }, conf);

    // Inverts a hex-colour
    var hexInvert = function(hex)
    {
        var r = hex.substr(0, 2);
        var g = hex.substr(2, 2);
        var b = hex.substr(4, 2);
        return 0.212671 * r + 0.715160 * g + 0.072169 * b < 0.5 ? 'ffffff' : '000000'
    };

    // Add the colour-picker dialogue if not added
    var colourPicker = jQuery('#' + config.id);

    if (!colourPicker.length) {
        colourPicker = jQuery('<div id="' + config.id + '"></div>').appendTo(document.body).hide();

        // Remove the colour-picker if you click outside it (on body)
        jQuery(document.body).click(function(event)
        {
            if (!(jQuery(event.target).is('#' + config.id) || jQuery(event.target).parents('#' + config.id).length)) {
                colourPicker.hide(config.speed);
            }
        });
    }

    // For every select passed to the plug-in
    return this.each(function()
    {
        // Insert icon and input
        var select = jQuery(this);
        var value = select.val();
        var extra, trans;
        if (value === 'transparent') {
            extra = jQuery('<input type="text" name="' + select.attr('name') + '_hex" value="' + value + '" class="form-text style-input">').insertAfter(select);
            trans = ' style-color-transparent';
        }
        else {
            extra = jQuery('<input type="text" name="' + select.attr('name') + '_hex" value="#' + value + '" class="form-text style-input">').insertAfter(select);
            trans = '';
        }
        var input = jQuery('<input type="text" name="' + select.attr('name') + '" value="' + value + '" class="form-text style-color' + trans + '">').insertAfter(extra);
        var loc = '';

        // Build a list of colours based on the colours in the select
        jQuery('option', select).each(function()
        {
            var option = jQuery(this);
            var hex = option.val();
            var title = option.text();

            loc += '<li><a href="#" title="'
                + title
                + '" rel="'
                + hex
                + '" style="background: #'
                + hex
                + '; color: '
                + hexInvert(hex)
                + ';">'
                + title
                + '</a></li>';
        });

        // Remove select
        select.remove();

        // If user wants to, change the input's BG to reflect the newly selected colour
        if (config.inputBG) {
            input.change(function()
            {
                if (input.val() == 'transparent') {
                    input.css({background: 'transparent'});
                }
                else {
                    input.css({background: '#' + input.val(), color: '#' + input.val()});
                }
            });

            input.change();
        }

        // When you click the icon
        input.click(function()
        {
            // Show the colour-picker next to the icon and fill it with the colours in the select that used to be there
            var iconPos = input.offset();
            var heading = config.title ? '<h2>' + config.title + '</h2>' : '';

            colourPicker.html(heading + '<ul>' + loc + '</ul>').css({
                position: 'absolute',
                left: iconPos.left + 'px',
                top: iconPos.top + 'px'
            }).show(config.speed);

            // When you click a colour in the colour-picker
            jQuery('a', colourPicker).click(function()
            {
                // The hex is stored in the link's rel-attribute
                var hex = jQuery(this).attr('rel');

                input.val(hex);

                // If user wants to, change the input's BG to reflect the newly selected colour
                if (config.inputBG) {
                    input.css({background: '#' + hex, color: '#' + hex});
                }

                // Trigger change-event on input
                input.change();
                if (hex === 'transparent') {
                    extra.val(hex);
                    input.val('transparent').addClass('style-color-transparent');
                }
                else {
                    extra.val('#' + hex);
                    input.removeClass('style-color-transparent');
                }

                // Hide the colour-picker and return false
                colourPicker.hide(config.speed);

                return false;
            });

            return false;
        });
    });
};
