<?php
/**
 * Jamroom 5 Support Center module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
 *
 * This software is provided "as is" and any express or implied
 * warranties, including, but not limited to, the implied warranties
 * of merchantability and fitness for a particular purpose are
 * disclaimed.  In no event shall the Jamroom Network be liable for
 * any direct, indirect, incidental, special, exemplary or
 * consequential damages (including but not limited to, procurement
 * of substitute goods or services; loss of use, data or profits;
 * or business interruption) however caused and on any theory of
 * liability, whether in contract, strict liability, or tort
 * (including negligence or otherwise) arising from the use of this
 * software, even if advised of the possibility of such damage.
 * Some jurisdictions may not allow disclaimers of implied warranties
 * and certain statements in the above disclaimer may not apply to
 * you as regards implied warranties; the other terms and conditions
 * remain enforceable notwithstanding. In some jurisdictions it is
 * not permitted to limit liability and therefore such limitations
 * may not apply to you.
 *
 * @copyright 2014 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Add help for HELP tab to module ACP views
 * @param $_post array
 * @param $_user array
 * @param $_conf array
 * @return array|bool
 */
function jrSupport_tips($_post, $_user, $_conf)
{
    global $_mods;
    //-----------------------
    // ALL ADMIN VIEWS
    //-----------------------
    if (isset($_post['module']) && isset($_mods["{$_post['module']}"])) {
        $_out = array(
            array(
                'view'        => 'VIEW_ACP_ALL',
                'selector'    => '#tsupport',
                'title'       => $_mods["{$_post['module']}"]['module_name'] . " &raquo; Help",
                'text'        => "<b>Support Center</b> adds the HELP tab to modules to help you find support options. <br><br>You will find helpful links to documentation, marketplace information as well as support options. If you have purchased <a href='https://www.jamroom.net/subscribe' target='_blank'>VIP Support</a> you can open a ticket from here.",
                'position'    => 'bottom center',
                'my_position' => 'top right',
                'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2867/support-center',
                'doc_title'   => 'Support Center documentation'
            )
        );
        return $_out;
    }
    return false;
}
