<?php
/**
 * Jamroom Video module
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
function jrVideo_quota_config()
{
    // Allowed Video Media Types
    $_tmp = array(
        'name'     => 'allowed_video_types',
        'default'  => '3g2,3gp,avi,f4v,flv,m4v,mkv,mov,mp4,mpg,ogv,webm,wmv',
        'type'     => 'optionlist',
        'options'  => 'jrVideo_get_video_types',
        'required' => 'on',
        'label'    => 'allowed video types',
        'help'     => 'Select the types of video files you would like to allow users to upload.',
        'validate' => 'core_string',
        'order'    => 2
    );
    jrProfile_register_quota_setting('jrVideo', $_tmp);

    // Conversions
    $_tmp = array(
        'name'     => 'video_conversions',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'convert videos',
        'help'     => 'If this option is checked, uploaded video files will be converted to the proper formats to stream on the desktop as well as mobile devices.',
        'order'    => 3
    );
    jrProfile_register_quota_setting('jrVideo', $_tmp);
    return true;
}
