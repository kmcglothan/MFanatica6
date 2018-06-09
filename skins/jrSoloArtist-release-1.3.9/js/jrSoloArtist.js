function jrLoad(id,url,round_id,round_num) {
    if (typeof url == "undefined") {
        return false;
    }
    if (url == 'blank') {
        $(id).hide();
    }
    else if (id == '#hidden') {
        $(id).hide();
        $(id).load(url);
    }
    else {
        if (id == '#rank') {
            var t = $('#rank').height();
            if (t == null) {
                $('#main').html('<div class="inner"><div id="rank"></div></div>');
            }
        }
        var h = $(id).height();
        if (h > 150) {
            $(id).height(h);
        }
        $(id).fadeTo(100,0.5,function() {
            $(id).html('<div style="text-align:center;padding:20px;margin:0 auto;"><img src="'+ core_system_url +'/skins/jrSoloArtist/img/submit.gif" style="margin:15px;"><br>Loading...</div>');
            $(id).load(url,function() {
                var l = $(id).text();
                if (l.length < 1 && id != '#player') {
                    $(id).html('');
                }
                if (h > 150) {
                    $(id).height('100%');
                }
                if (round_id && round_num > 0) {
                    $(id).fadeTo(100,1.00,function() {
                        if (jQuery.browser.msie) {
                            this.style.removeAttribute('filter');
                        }
                        $(round_id).corner(round_num +'px');
                    });
                }
                else {
                    $(id).fadeTo(100,1,function() {
                        if (jQuery.browser.msie) {
                            this.style.removeAttribute('filter');
                        }
                    });
                }
            })
        });
    }
}
function jrSetActive(id,no_remove) {
    if (typeof no_remove == "undefined") {
        $('.p_choice').removeClass('p_choice_active');
    }
    $(id).addClass('p_choice_active');
}

function jrSiteLogin() {

    $('#loginform').modal({

        onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.slideDown('fast', function () {
                    dialog.data.fadeIn('fast');
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

function jrSiteSignup() {

    $('#signupform').modal({

        onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.slideDown('fast', function () {
                    dialog.data.fadeIn('fast');
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

/******************************************
 * Scrollable content script II- © Dynamic Drive (www.dynamicdrive.com)
 * Visit http://www.dynamicdrive.com/ for full source code
 * This notice must stay intact for use
 ******************************************/

    // modified 17-October-2011


function move(id,spd){
    var obj=document.getElementById(id),max=-obj.offsetHeight+obj.parentNode.offsetHeight,top=parseInt(obj.style.top);
    if ((spd>0&&top<=0)||(spd<0&&top>=max)){
        obj.style.top=top+spd+"px";
        move.to=setTimeout(function(){ move(id,spd); },20);
    }
    else {
        obj.style.top=(spd>0?0:max)+"px";
    }
}

// Scroll To Top Function
$(document).ready(function(){
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });

    $('.scrollup').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });

});
