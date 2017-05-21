<?php
/**
 * Jamroom 5 Video (Combined) module
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
 * @copyright 2003 - 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * quota_config
 */
function jrCombinedVideo_quota_config()
{
    global $_conf, $_post;
    if (!isset($_post['option']) || $_post['option'] != 'integrity_check_save') {
        // This is just a stub so the core creates the allowed access quota setting
        $_tm = jrCore_get_registered_module_features('jrCombinedVideo', 'combined_support');
        if (!$_tm || !is_array($_tm)) {
            $_ms = array(
                'jrVideo'   => 'Video Support',
                'jrVimeo'   => 'Vimeo Support',
                'jrYouTube' => 'YouTube Support'
            );
            $_ot = array();
            $url = jrCore_get_module_url('jrMarket');
            foreach ($_ms as $m => $t) {
                $_ot[] = "<a href=\"{$_conf['jrCore_base_url']}/{$url}/browse/module/search_string={$m}\"><u><b>{$t}</b></u></a>";
            }
            jrCore_set_form_notice('error', 'No Supported Video Modules found to Combine!<br>Ensure the following modules are installed and active:<br><br>' . implode('<br>', $_ot), false);
        }
    }
    return true;
}