<?php
/**
 * Jamroom 5 List Exclude module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 * jrProfileExclude_meta
 */
function jrProfileExclude_meta()
{
    $_tmp = array(
        'name'        => 'List Exclude',
        'url'         => 'profileexclude',
        'version'     => '1.0.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Exclude configured profiles from appearing in site listings to non-admin users',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/3899/profile-exclude',
        'license'     => 'mpl',
        'category'    => 'profiles'
    );
    return $_tmp;
}

/**
 * jrProfileExclude_init
 */
function jrProfileExclude_init()
{
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrProfileExclude_db_search_params_listener');
    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Limit uploads when creating items
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProfileExclude_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!jrUser_is_admin() && isset($_conf['jrProfileExclude_exclude']) && strlen($_conf['jrProfileExclude_exclude']) > 0 && !jrCore_get_flag('jrprofile_view_is_active')) {

        // See if we are in a Core List or Seamless List call
        if ((isset($_data['jrcore_list_function_call_is_active']) && $_data['jrcore_list_function_call_is_active'] == '1') || (isset($_data['jrseamless_list_function_call_is_active']) && $_data['jrseamless_list_function_call_is_active'] == '1')) {

            $_id = array();
            foreach (explode(',', $_conf['jrProfileExclude_exclude']) as $id) {
                $id = (int) trim($id);
                if ($id > 0) {
                    $_id[] = $id;
                }
            }
            if (count($_id) > 0) {
                if (!isset($_data['search'])) {
                    $_data['search'] = array();
                }
                $_data['search'][] = '_profile_id not_in ' . implode(',', $_id);
            }
            unset($_id);

        }
    }
    return $_data;
}
