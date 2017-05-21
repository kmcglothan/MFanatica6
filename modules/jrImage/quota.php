<?php
/**
 * Jamroom Image Support module
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
function jrImage_quota_config()
{
    // Max Allowed Image Size
    $_tmp = array(
        'name'     => 'max_image_size',
        'default'  => '2097152',
        'type'     => 'select',
        'options'  => 'jrImage_get_allowed_image_sizes',
        'validate' => 'number_nz',
        'required' => 'on',
        'label'    => 'max image file size',
        'help'     => 'Select the maximum allowed size for an image file upload by a user to a profile in this quota.<br><br><b>NOTE:</b> This value is limited by the following settings in your server php.ini file: post_max_size, upload_max_filesize and memory_limit.  The upload size will be smaller than these settings due to the overhead involved in the Upload Progress Meter. To change these values contact your hosting provider.',
        'order'    => 1
    );
    jrProfile_register_quota_setting('jrImage',$_tmp);

    // Allowed Image Media Types
    $_tmp = array(
        'name'     => 'allowed_image_types',
        'default'  => 'png,gif,jpg,jpeg',
        'type'     => 'optionlist',
        'options'  => array('png' => 'png','gif' => 'gif','jpg' => 'jpg','jpeg' => 'jpeg'),
        'required' => 'on',
        'label'    => 'allowed image types',
        'help'     => 'Select the image file types you would like to allow',
        'validate' => 'core_string',
        'order'    => 2
    );
    jrProfile_register_quota_setting('jrImage',$_tmp);

    // Watermark images
    $_tmp = array(
        'name'     => 'watermark',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'watermark images',
        'help'     => 'If this option is checked, when an image is displayed it will be watermarked using the watermark image that is active in the Image Support -> Images tab.<br><br><strong>NOTE:</strong> The uploaded watermark image MUST be a PNG image with transparency in order to work properly',
        'section'  => 'watermark',
        'order'    => 10
    );
    jrProfile_register_quota_setting('jrImage',$_tmp);

    // Watermark Cutoff
    $_tmp = array(
        'name'     => 'watermark_cutoff',
        'default'  => '80',
        'type'     => 'text',
        'validate' => 'number_nn',
        'required' => 'on',
        'label'    => 'watermark cutoff',
        'help'     => 'If an image has a width LOWER than this value, a watermark will not be applied to the image (this can save processing time for images that are too small for the watermark to be effective on).<br><br>Set this to 0 (zero) to enable watermarking for all images regardless of size.',
        'section'  => 'watermark',
        'order'    => 11
    );
    jrProfile_register_quota_setting('jrImage',$_tmp);

    // Watermark x offset
    $_tmp = array(
        'name'     => 'watermark_x_offset',
        'default'  => '-5',
        'type'     => 'text',
        'validate' => 'signed',
        'required' => 'on',
        'label'    => 'watermark x offset',
        'help'     => 'How many pixels from the top (positive) or the bottom (negative) do you want the watermark to appear?',
        'section'  => 'watermark',
        'order'    => 12
    );
    jrProfile_register_quota_setting('jrImage',$_tmp);

    // Watermark y offset
    $_tmp = array(
        'name'     => 'watermark_y_offset',
        'default'  => '-5',
        'type'     => 'text',
        'validate' => 'signed',
        'required' => 'on',
        'label'    => 'watermark y offset',
        'help'     => 'How many pixels from the left (positive) or the right (negative) do you want the watermark to appear?',
        'section'  => 'watermark',
        'order'    => 13
    );
    jrProfile_register_quota_setting('jrImage',$_tmp);

    // Watermark for sale only
    $_tmp = array(
        'name'     => 'watermark_sale_only',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'only images for sale',
        'help'     => 'If this option is checked then only images that <b>have a price</b> will have a watermark applied to them',
        'section'  => 'watermark',
        'order'    => 14
    );
    jrProfile_register_quota_setting('jrImage',$_tmp);

    return true;
}
