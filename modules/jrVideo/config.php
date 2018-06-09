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
 * jrVideo_config
 */
function jrVideo_config()
{

    $_cnt = array(
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5
    );
    $_tmp = array(
        'name'     => 'conversion_worker_count',
        'default'  => 1,
        'type'     => 'select',
        'options'  => $_cnt,
        'required' => 'on',
        'label'    => 'max conversion workers',
        'help'     => 'What is the maximum number of video conversions that can be going on simultaneously? It is recommended to leave this value at 1 (one) unless you are on a dedicated server with multiple processors - each worker will utilize 100% of a single CPU core while converting media.',
        'validate' => 'number_nz',
        'section'  => 'video conversion',
        'min'      => 1,
        'max'      => 5,
        'order'    => 1
    );
    jrCore_register_setting('jrVideo', $_tmp);

    $_cnt = array(
        1  => 'Highest',
        3  => 'Higher',
        6  => 'High',
        9  => 'Normal',
        12 => 'Low',
        15 => 'Lower',
        19 => 'Lowest'
    );
    $_tmp = array(
        'name'     => 'conversion_priority',
        'default'  => 12,
        'type'     => 'select',
        'options'  => $_cnt,
        'required' => 'on',
        'label'    => 'conversion priority',
        'help'     => 'What priority should be given to the video conversion process?<br><br><b>Highest</b> will cause a larger load on your server during video conversion, yet it will finish the fastest.<br><b>Lowest</b> will place the least load on your server during video conversion, yet take the longest to complete.',
        'validate' => 'number_nz',
        'section'  => 'video conversion',
        'min'      => 1,
        'max'      => 19,
        'order'    => 2
    );
    jrCore_register_setting('jrVideo', $_tmp);

    $_cnt = array(
        0   => 'Disable Sample Creation',
        30  => '30 seconds',
        45  => '45 seconds',
        60  => '60 seconds (default)',
        90  => '90 seconds',
        120 => '120 seconds'
    );
    $_tmp = array(
        'name'     => 'sample_length',
        'default'  => 60,
        'type'     => 'select',
        'options'  => $_cnt,
        'required' => 'on',
        'label'    => 'video sample length',
        'help'     => 'If you are selling video downloads, the Video Module will create a &quot;video sample&quot; of videos that are for sale - how long should the video sample be?',
        'validate' => 'number_nn',
        'section'  => 'video conversion',
        'min'      => 0,
        'max'      => 120,
        'order'    => 3
    );
    jrCore_register_setting('jrVideo', $_tmp);

    // Support Flash
    $_tmp = array(
        'name'     => 'enable_flash',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'support flash video',
        'help'     => 'If this option is enabled, a <a href="https://en.wikipedia.org/wiki/Flash_Video" target="_blank"><u>Flash Video</u></a> version of the video file will be created so it can be played in older browsers that do not support HTML 5.<br><br><b>NOTE:</b> This option requires extra disk space to store the FLV video file.',
        'section'  => 'video conversion',
        'order'    => 4
    );
    jrCore_register_setting('jrVideo', $_tmp);

    // Block FLV/MP4 Downloads
    $_tmp = array(
        'name'     => 'block_download',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'block video downloads',
        'help'     => 'By default FLV and MP4 video files are blocked from being downloaded via the &quot;download&quot; URL, and are only playable in the site video players. Uncheck this option to allow download of FLV and MP4 files',
        'order'    => 10
    );
    jrCore_register_setting('jrVideo', $_tmp);

    // Cleanup old settings
    jrCore_delete_setting('jrVideo', 'player_type');
    jrCore_delete_setting('jrVideo', 'conversion_quality');

    return true;
}
