<?php
/**
 * Jamroom Image Galleries module
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
 * quota_config
 */
function jrGallery_quota_config()
{
    // Profile Gallery Index Style
    $_tmp = array(
        'name'     => 'gallery_group',
        'type'     => 'checkbox',
        'default'  => 'off',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'Group by Gallery',
        'help'     => 'If checked, images will be grouped by their Gallery on the Profile Gallery index page',
        'section'  => 'gallery options',
        'order'    => 1
    );
    jrProfile_register_quota_setting('jrGallery', $_tmp);

    // Gallery Order
    $_tmp = array(
        'name'     => 'gallery_order',
        'type'     => 'checkbox',
        'default'  => 'on',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'Allow Image Ordering',
        'help'     => 'If checked, Users with a Profile in this quota will be able to manually set the order of the images within a Gallery by dragging and dropping the images in the order they want the images to appear.<br><br>If unchecked, images within a gallery will be ordered by the time the image was added to the Gallery, in descending order.',
        'section'  => 'gallery options',
        'order'    => 2
    );
    jrProfile_register_quota_setting('jrGallery', $_tmp);

    // Enable Editor
    $_tmp = array(
        'name'     => 'image_editor',
        'type'     => 'checkbox',
        'default'  => 'on',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'Enable Image Editor',
        'help'     => 'If you have entered your Adobe Client Secret in the Global Config, you can enable (or disable) the use of the Image Editor by checking or unchecking this option',
        'section'  => 'gallery options',
        'order'    => 3
    );
    jrProfile_register_quota_setting('jrGallery', $_tmp);

    return true;
}
