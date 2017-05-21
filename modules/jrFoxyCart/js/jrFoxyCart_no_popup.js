/**
 * Add items to the FoxyCart minicart without displaying a popup
 */
jQuery(document).ready(function() {

    // Restart the process event collection object
    try {

        fcc.events.cart.process = new FC.client.event();

        // Define the new process event
        fcc.events.cart.process.add(function(e) {
            var href = '';
            if (e.tagName == 'A') {
                href = e.href;
            }
            else if (e.tagName == 'FORM') {
                href = 'https://'+storedomain+'/cart?'+jQuery(e).serialize();
            }
            if (href.match("cart=(checkout|updateinfo)") || href.match("redirect=")) {
                return true;
            }
            else if (href.match("cart=view")) {
                jQuery.colorbox({ href: href, iframe: true, width: colorbox_width, height: colorbox_height, close: colorbox_close, onClosed: function(){fcc.events.cart.postprocess.execute(e);} });
                return false;
            }
            else {
                $('#'+ e.id +' #add_to_cart_success').fadeIn(150,function() {
                     jQuery.getJSON(href + '&output=json&callback=?', function(data) {
                         fcc.cart_update();
                         $('#'+ e.id +' #add_to_cart_success').delay(3000).fadeOut(150);
                    });
                });
                return false;
            }
        });
    }
    catch(e) {
        return true;
    }
});