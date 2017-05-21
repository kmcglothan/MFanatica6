<?php
/**
 * Jamroom Like It module
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
 * config
 */
function jrLike_config()
{
    // Require Login
    $_tmp = array(
        'name'     => 'require_login',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Require Login',
        'help'     => 'If this option is checked, only logged in users will be able to &quot;Like&quot; an item.',
        'order'    => 1
    );
    jrCore_register_setting('jrLike', $_tmp);

    $_tmp = array(
        'name'     => 'allow_actions',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Add To Timeline',
        'help'     => 'If this option is checked, when a user likes (or dislikes) an item, an Action entry will be created in the user timeline.',
        'order'    => 2
    );
    jrCore_register_setting('jrLike', $_tmp);

    $_tmp = array(
        'name'     => 'like_option',
        'type'     => 'select',
        'options'  => array('all' => 'Like and Dislike', 'like' => 'Like Only', 'dislike' => 'Dislike only'),
        'default'  => 'all',
        'validate' => 'printable',
        'label'    => 'Allowed Options',
        'help'     => 'Allow Likes, Dislikes, or both',
        'order'    => 3
    );
    jrCore_register_setting('jrLike', $_tmp);

    $_dsi  = jrCore_get_datastore_modules();
    $_opts = array();
    $def   = false;
    foreach ($_dsi as $k => $v) {
        if (!$def) {
            $def = $k;
        }
        if (is_file(APP_DIR . "/modules/{$k}/templates/item_list.tpl")) {
            $_mta      = jrCore_module_meta_data($k);
            $_opts[$k] = $_mta['name'];
        }
    }
    ksort($_opts);
    $_tmp = array(
        'name'     => 'like_default',
        'type'     => 'select',
        'options'  => $_opts,
        'default'  => $def,
        'validate' => 'printable',
        'label'    => 'Default Liked Item',
        'help'     => 'When viewing the &quot;Items I Like&quot; section, which module tab should be the default?',
        'order'    => 4
    );
    jrCore_register_setting('jrLike', $_tmp);

    jrCore_delete_setting('jrLike', 'prune');
    return true;
}
