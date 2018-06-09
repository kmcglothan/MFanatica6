<?php
/**
 * Jamroom Simple Chat module
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
 * quota_config
 */
function jrChat_quota_config()
{
    $_tmp = array(
        'name'     => "create_rooms",
        'label'    => "Can Create Rooms",
        'help'     => 'Check this option to allow users with profiles in this quota to create new public and private chat rooms.',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'section'  => 'permissions',
        'order'    => 1
    );
    jrProfile_register_quota_setting('jrChat', $_tmp);

    $_tmp = array(
        'name'     => "create_chats",
        'label'    => "Can Start Private Chat",
        'help'     => 'Check this option to allow users with profiles in this quota to start chats with other users on the system.',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'section'  => 'permissions',
        'order'    => 2
    );
    jrProfile_register_quota_setting('jrChat', $_tmp);

    $_tmp = array(
        'name'     => "followers_only",
        'label'    => "Followers Only",
        'help'     => 'If the Followers module is active, by default the Chat module only allows a user to initiate a chat with a follower - this prevents users from being contacted by users they are not following.  You can remove this restriction by unchecking this option, which will allow users to initiate a chat with any other user in the system.',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'section'  => 'permissions',
        'order'    => 3
    );
    jrProfile_register_quota_setting('jrChat', $_tmp);

    // Max Size
    $_tmp = array(
        'name'     => 'max_size',
        'default'  => '2097152',
        'type'     => 'select',
        'options'  => 'jrChat_get_allowed_attachment_sizes',
        'validate' => 'number_nz',
        'required' => 'on',
        'label'    => 'maximum file size',
        'help'     => 'Select the maximum allowed size for a file uploaded to a chat',
        'section'  => 'file attachments',
        'order'    => 4
    );
    jrProfile_register_quota_setting('jrChat', $_tmp);

    // Attachment Type
    $_tmp = array(
        'name'     => 'file_types',
        'default'  => 'png,jpg,jpeg,jfif,gif,zip,mp4,mp3,doc,txt',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => 'on',
        'label'    => 'allowed file types',
        'help'     => 'Enter the file extensions of the types of files you want to allow to be uploaded to a chat.  Separate multiple file extensions with commas.',
        'section'  => 'file attachments',
        'order'    => 4
    );
    jrProfile_register_quota_setting('jrChat', $_tmp);

    return true;
}
