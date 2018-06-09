<?php
/**
 * Jamroom Group Discussions module
 *
 * copyright 2018 The Jamroom Network
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
function jrGroupDiscuss_config()
{
    // Sort by Recently Active
    $_tmp = array(
        'name'     => 'recently_active',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Sort by Activity',
        'help'     => 'Group discussions will be ordered by the latest comments. A new comment in a discussion will make that group come to the top of the discussions list.',
        'order'    => 1
    );
    jrCore_register_setting('jrGroupDiscuss', $_tmp);

    // Send email to followers rate
    $_opt = array(
        'default' => 'Single Notification',
        'chatty'  => 'Notify on Each Post',
    );
    $_tmp = array(
        'name'     => 'follower_notification',
        'default'  => 'default',
        'type'     => 'select',
        'options'  => $_opt,
        'required' => 'on',
        'label'    => 'Follow Update Notification',
        'help'     => 'If a user is following a discussion this determines how they will be notified:<br><br><strong>Single Notification:</strong> Notify the user ONE time that a new followup has been posted to a discussion they are following.<br><br><strong>Notify on Each Post:</strong> Notify the user every time a new followup is posted to a discussion they are following.',
        'validate' => 'printable',
        'order'    => 17
    );
    jrCore_register_setting('jrGroupDiscuss', $_tmp);

    // Sort by Recently Active
    $_tmp = array(
        'name'     => 'update_always',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Always Allow Updates',
        'help'     => 'By default, if a discussion has comments on it, the creator is no longer allowed to update the initial post. Check this to always allow updates.',
        'order'    => 20
    );
    jrCore_register_setting('jrGroupDiscuss', $_tmp);

    // Auto Group Follow
    $_tmp = array(
        'name'     => 'notify_all',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Notify All',
        'help'     => 'If checked, when a group member creates a new discussion all group members will be notified - not just those following the group.',
        'order'    => 30
    );
    jrCore_register_setting('jrGroupDiscuss', $_tmp);

    return true;
}
