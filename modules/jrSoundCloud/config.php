<?php
/**
 * Jamroom SoundCloud module
 *
 * copyright 2017 The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrSoundCloud_config
 */
function jrSoundCloud_config()
{
    // Client ID
    $_tmp = array(
        'name'     => 'client_id',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'string',
        'label'    => 'Client ID',
        'help'     => 'Enter the SoundCloud API Client ID code',
        'order'    => 1
    );
    jrCore_register_setting('jrSoundCloud', $_tmp);

    // Client Secret
    $_tmp = array(
        'name'     => 'client_secret',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'string',
        'label'    => 'Client Secret',
        'help'     => 'Enter the SoundCloud API Client Secret code',
        'order'    => 2
    );
    jrCore_register_setting('jrSoundCloud', $_tmp);

    // Daily Maintenance
    $_tmp = array(
        'name'     => 'daily_maintenance',
        'type'     => 'text',
        'default'  => 0,
        'validate' => 'number_nn',
        'label'    => 'Daily Maintenance',
        'help'     => 'If greater than zero, the specified number of uploaded soundcloud items will be checked sequentially on a daily basis, and removed if they are no longer active on SoundCloud. Removed items will be logged.',
        'order'    => 3
    );
    jrCore_register_setting('jrSoundCloud', $_tmp);

    return true;
}

/**
 * Show number of SoundCloud tracks uploading on Config screen
 * @param $_post array Posted data
 * @param $_user array Viewing User info
 * @param $_conf array Global Config
 * @return bool
 */
function jrSoundCloud_config_display($_post, $_user, $_conf)
{
    jrCore_set_form_notice('notice', "There are " . jrCore_db_get_datastore_item_count('jrSoundCloud') . " SoundCloud tracks uploaded");
    return true;
}
