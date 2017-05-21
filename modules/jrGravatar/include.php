<?php
/**
 * Jamroom Gravatar Images module
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
 * meta
 */
function jrGravatar_meta()
{
    $_tmp = array(
        'name'        => 'Gravatar Images',
        'url'         => 'gravatar',
        'version'     => '1.1.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add support for Gravatar images - used if a user has not uploaded a User or Profile Image.',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2947/gravatar-images',
        'category'    => 'site',
        'license'     => 'mpl',
        'requires'    => 'jrImage:1.0.2'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGravatar_init()
{
    jrCore_register_event_listener('jrImage', 'img_src', 'jrGravatar_img_src_listener');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrGravatar', 'gimg');
    return true;
}

//--------------------------
// EVENT LISTENERS
//--------------------------

/**
 * Use a Gravatar Image if a user has not uploaded a User Image
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGravatar_img_src_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_args['module']) || (isset($_conf['jrGravatar_enabled']) && $_conf['jrGravatar_enabled'] == 'off')) {
        return $_data;
    }

    $src = false;
    switch ($_args['module']) {

        // User image
        case 'jrUser':
            if (isset($_args['type']) && $_args['type'] == 'user_image') {
                if (!isset($_args['_item']) || !is_array($_args['_item'])) {
                    $_args['_item'] = jrCore_db_get_item('jrUser', $_args['item_id'], true);
                }
                if (!isset($_args['_item']['user_image_size']) && isset($_args['_item']['user_email'])) {

                    // $_args:
                    // [module] => jrUser
                    // [type] => user_image
                    // [item_id] => 2
                    // [size] => large
                    // [crop] => auto
                    // [alt] => <alt text>
                    // [class] => img_shadow
                    $_sz           = jrImage_get_allowed_image_widths();
                    $siz           = $_sz["{$_args['size']}"];
                    $_args['size'] = $siz;
                    if (isset($_conf['jrGravatar_local_cache']) && $_conf['jrGravatar_local_cache'] == 'on') {
                        $gru = jrCore_get_module_url('jrGravatar');
                        $url = jrGravatar_get_image_url($_args['_item']['user_email'], $siz, false);
                        if ($url) {
                            if ($key = jrGravatar_get_cached_gravatar_image($url, $_args['_item']['_profile_id'], $_args['_item']['_user_id'])) {
                                $url = "{$_conf['jrCore_base_url']}/{$gru}/gimg/{$key}.png";
                                if (isset($_args['url_only']) && $_args['url_only'] === true) {
                                    $src = $url;
                                }
                                else {
                                    $src = jrGravatar_get_image_html($url, $_args, $_args['_item']['_updated']);
                                }
                            }
                        }
                    }
                    else {
                        $url = jrGravatar_get_image_url($_args['_item']['user_email'], $siz, true);
                        if (isset($_args['url_only']) && $_args['url_only'] === true) {
                            $src = $url;
                        }
                        else {
                            $src = jrGravatar_get_image_html($url, $_args, $_args['_item']['_updated']);
                        }
                    }
                }
            }
            break;

        // Profile image
        case 'jrProfile':

            if (isset($_conf['jrGravatar_enable_profile']) && $_conf['jrGravatar_enable_profile'] == 'on' && isset($_args['type']) && $_args['type'] == 'profile_image') {
                if (!isset($_args['_item']) || !is_array($_args['_item'])) {
                    $_args['_item'] = jrCore_db_get_item('jrProfile', $_args['item_id'], true);
                }
                if (!isset($_args['_item']['profile_image_size']) && isset($_args['_item']['_user_id'])) {
                    // We need to get the user account associated with this profile
                    $_us = jrCore_db_get_item('jrUser', $_args['_item']['_user_id'], true);
                    if ($_us && is_array($_us) && isset($_us['user_email'])) {
                        $_sz           = jrImage_get_allowed_image_widths();
                        $siz           = $_sz["{$_args['size']}"];
                        $_args['size'] = $siz;
                        if (isset($_conf['jrGravatar_local_cache']) && $_conf['jrGravatar_local_cache'] == 'on') {
                            $gru = jrCore_get_module_url('jrGravatar');
                            $url = jrGravatar_get_image_url($_us['user_email'], $siz, false);
                            if ($url) {
                                if ($key = jrGravatar_get_cached_gravatar_image($url, $_us['_profile_id'], $_us['_user_id'])) {
                                    $url = "{$_conf['jrCore_base_url']}/{$gru}/gimg/{$key}.png";
                                    if (isset($_args['url_only']) && $_args['url_only'] === true) {
                                        $src = $url;
                                    }
                                    else {
                                        $src = jrGravatar_get_image_html($url, $_args, $_us['_updated']);
                                    }
                                }
                            }
                        }
                        else {
                            $url = jrGravatar_get_image_url($_us['user_email'], $siz, true);
                            if (isset($_args['url_only']) && $_args['url_only'] === true) {
                                $src = $url;
                            }
                            else {
                                $_args['width'] = $siz;
                                $src            = jrGravatar_get_image_html($url, $_args, $_us['_updated']);
                            }
                        }
                    }
                }
            }
            break;

    }
    if ($src) {
        $_data['src'] = $src;
    }
    return $_data;
}

//--------------------------
// FUNCTIONS
//--------------------------

/**
 * Get a gravatar image URL
 * @param string $email
 * @param int $size
 * @param bool $amp
 * @return string
 */
function jrGravatar_get_image_url($email, $size, $amp = false)
{
    global $_conf;
    if ($amp) {
        $amp = '&amp;';
    }
    else {
        $amp = '&';
    }
    $eml = md5($email);
    // default image
    $d = '';
    if (isset($_conf['jrGravatar_default_image']) && $_conf['jrGravatar_default_image'] != 'default') {
        $d = "{$amp}d={$_conf['jrGravatar_default_image']}";
    }
    // rating
    $r = '';
    if (isset($_conf['jrGravatar_rating']) && $_conf['jrGravatar_rating'] != 'g') {
        $r = "{$amp}g={$_conf['jrGravatar_rating']}";
    }
    // Handle hiDPI
    $size = ($size * 2);
    if ($size > 1000) {
        $size = 1000;
    }
    return "https://secure.gravatar.com/avatar/{$eml}?s={$size}{$d}{$r}";
}

/**
 * Get the HTML for a Gravatar Image URL
 * @param string $url
 * @param array $_args
 * @param int $updated
 * @return string
 */
function jrGravatar_get_image_html($url, $_args, $updated)
{
    $sty = '';
    if (isset($_args['style']) && strlen($_args['style']) > 0) {
        $sty = 'style="' . $_args['style'] . '"';
    }
    $w = (int) $_args['size'];
    if (isset($_args['width']) && $_args['width'] > 0) {
        $w = (int) $_args['width'];
    }
    $c = '';
    if (isset($_args['class']) && strlen($_args['class']) > 0) {
        $c = 'class="' . $_args['class'] . '"';
    }
    $a = '';
    if (isset($_args['alt']) && strlen($_args['alt']) > 0) {
        $a = 'alt="' . htmlentities($_args['alt'], ENT_QUOTES, 'UTF-8') . '"';
    }
    if (isset($_args['title'])) {
        $t = 'title="' . htmlentities($_args['title'], ENT_QUOTES, 'UTF-8') . '"';
    }
    else {
        $t = $a;
    }
    return "<img src=\"{$url}&amp;_v={$updated}\" width=\"{$w}\" {$c} {$a} {$t} {$sty}>";
}

/**
 * Cache a gravatar image locally
 * @param string $url
 * @param int $profile_id
 * @param int $user_id
 * @return bool|string
 */
function jrGravatar_get_cached_gravatar_image($url, $profile_id, $user_id = 0)
{
    global $_conf;
    $dir = jrCore_get_module_cache_dir('jrImage');
    $dir = "{$dir}/{$_conf['jrImage_active_cache_dir']}";
    $key = jrGravatar_get_image_cache_key($url, $profile_id, $user_id);
    $dir = "{$dir}/" . substr($key, 0, 2);
    if (!is_dir($dir)) {
        mkdir($dir, $_conf['jrCore_dir_perms'], true);
    }
    $img = "{$dir}/{$key}.png";
    if (!is_file($img)) {
        if (!jrCore_download_file($url, $img, 10, 80, null, null, null, false)) {
            return false;
        }
        else {
            // Is it a good image?
            if (strpos(file_get_contents($img), 'DOCTYPE')) {
                // Gravatar error
                unlink($img);
                return false;
            }
        }
    }
    return $key;
}

/**
 * Get a unique image cache key
 * @param string $url
 * @param int $profile_id
 * @param int $user_id
 * @return string
 */
function jrGravatar_get_image_cache_key($url, $profile_id, $user_id = 0)
{
    global $_conf;
    return md5($url . $profile_id . $user_id . $_conf['jrGravatar_default_image'] . $_conf['jrGravatar_rating']);
}
