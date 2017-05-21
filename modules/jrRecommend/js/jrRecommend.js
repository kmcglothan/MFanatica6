/**
 * jrRecommend Javascript functions
 * @copyright 2012 Talldude Networks, LLC.
 */

/**
 * Display a modal recommend form
 */
function jrRecommend_modal_form()
{
    $('#recommendform').modal({

        onOpen: function (dialog) {
            dialog.overlay.fadeIn(75, function () {
                dialog.container.slideDown(5, function () {
                    dialog.data.fadeIn(75);
                });
            });
        },
        onClose: function (dialog) {
            dialog.data.fadeOut('fast', function () {
                dialog.container.hide('fast', function () {
                    dialog.overlay.fadeOut('fast', function () {
                        $.modal.close();
                    });
                });
            });
        },
        overlayClose:true
    });
}