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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrPlaylistAds_meta()
{
    return array(
        'name'        => 'Playlist Ads',
        'url'         => 'playlistads',
        'version'     => '1.0.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Inject media file based advertising into playlists',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/975/user-playlist-ads',
        'requires'    => 'jrPlaylist,jrCore:6.0.4',
        'category'    => 'site',
        'license'     => 'jcl'
    );
}

/**
 * module init
 * @return bool
 */
function jrPlaylistAds_init()
{
    // Register jrPlaylistAds tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPlaylistAds', 'create', array('Create', 'Create a new ad media item'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPlaylistAds', 'select', array('Update', 'Update an existing ad media item'));

    // jrPlaylistAds listener
    jrCore_register_event_listener('jrCore', 'media_playlist', 'jrPlaylistAds_insert_ads');

    jrCore_register_module_feature('jrCore', 'javascript', 'jrPlaylistAds', true);

    return true;
}

//----------------------
// EVENT LISTENER
//----------------------

/**
 * Insert advert tracks/videos into playlist
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrPlaylistAds_insert_ads($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_module_is_active('jrPlaylist') && jrCore_checktype($_conf['jrPlaylistAds_ads_interval'], 'number_nz')) {
        // Get language strings
        $_lg = jrUser_load_lang_strings();
        if (isset($_data[0]) && is_array($_data[0])) {
            // Audio or video playlist?
            $prefix = '';
            if ($_data[0]['module'] == 'jrAudio' || $_data[0]['module'] == 'jrSoundCloud') {
                $extension = 'mp3';
                $prefix    = jrCore_db_get_prefix('jrAudio');
            }
            elseif ($_data[0]['module'] == 'jrVideo') {
                $extension = 'flv,mp4';
                $prefix    = jrCore_db_get_prefix('jrVideo');
            }
            if (isset($extension)) {
                // Get all advert tracks
                $_s  = array(
                    "limit"    => 100,
                    "order_by" => array(
                        "playlistads_title" => 'asc'
                    )
                );
                $_xt = jrCore_db_search_items('jrPlaylistAds', $_s);
                if (isset($_xt['_items'][0]) && is_array($_xt['_items'][0])) {
                    // Yep - we have adverts - filter the type we want
                    $_xt = $_xt['_items'];
                    $_at = array();
                    foreach ($_xt as $xt) {
                        if (stristr($extension, strtolower($xt['playlistads_file_extension']))) {
                            $_at[] = $xt;
                        }
                    }
                    if (count($_at) > 0) {
                        // All good - let's do it
                        $_tmp = array();
                        $acnt = 0;
                        foreach ($_data as $data) {
                            $acnt++;
                            $_tmp[] = $data;
                            if ($acnt == $_conf['jrPlaylistAds_ads_interval']) {
                                // Time to insert an advert
                                $x                              = rand(0, count($_at) - 1);
                                $_at[$x]["{$prefix}_title"]     = $_lg['jrPlaylistAds'][1];
                                $_at[$x]["{$prefix}_title_url"] = jrCore_url_string($_lg['jrPlaylistAds'][1]);
                                $_at[$x]['module']              = 'jrPlaylistAds';
                                $_tmp[]                         = $_at[$x];
                                $acnt                           = 0;
                            }
                        }
                        $_data = $_tmp;
                    }
                }
            }
        }
    }
    return $_data;
}
