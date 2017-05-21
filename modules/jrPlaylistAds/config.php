<?php
/**
 * Jamroom Playlist Ads module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * module config
 * @return bool
 */
function jrPlaylistAds_config()
{
    $_list = array(
        0  => 'Disabled',
        1  => 1,
        2  => 2,
        3  => 3,
        4  => 4,
        5  => 5,
        6  => 6,
        7  => 7,
        8  => 8,
        9  => 9,
        10 => 10
    );

    $_tmp = array(
        'name'     => "ads_interval",
        'label'    => "Ads Interval",
        'help'     => "After how many playlist items will an advertisement be inserted?",
        'type'     => 'select',
        'default'  => 0,
        'options'  => $_list,
        'validate' => 'number_nn',
        'order'    => 10
    );
    jrCore_register_setting('jrPlaylistAds', $_tmp);

    jrCore_delete_setting('jrPlaylistAds', 'house_channel_advert_album');
    jrCore_delete_setting('jrPlaylistAds', 'house_channel_advert_interval');
    jrCore_delete_setting('jrPlaylistAds', 'house_station_advert_album');
    jrCore_delete_setting('jrPlaylistAds', 'house_station_advert_interval');

    return true;
}
