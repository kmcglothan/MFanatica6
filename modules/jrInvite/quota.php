<?php
/**
 * Jamroom Invitations module
 *
 * copyright 2017 The Jamroom Network
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * quota_config
 */
function jrInvite_quota_config()
{
    // Follower and/or Email Invitations
    $_opts = array(
        1 => 'Followers Only',
        2 => 'Email Address List',
        3 => 'Both Followers and Email Address List'
    );
    $_tmp  = array(
        'name'     => 'invitee_type',
        'default'  => 1,
        'type'     => 'select',
        'options'  => $_opts,
        'required' => 'on',
        'label'    => 'Invitation Type',
        'help'     => 'Select who the user is allowed to invite:<br><br><b>Followers</b> - User can only invite existing followers <b>*</b><br><b>Email Address List</b> - User can import a list of Email Addresses to invite<br><b>Both</b> - User can select either option<br><br><b>*</b> If an admin user <i>all</i> users are selectable</b>',
        'validate' => 'number_nz',
        'order'    => 1
    );
    jrProfile_register_quota_setting('jrInvite', $_tmp);
    return true;
}
