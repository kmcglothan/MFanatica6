<?php
/**
 * Jamroom Strong Password module
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

// make sure we are not being called directly
defined('APP_DIR') or exit();

 * jrStrongPassword_meta
 */
function jrStrongPassword_meta()
{
    $_tmp = array(
        'name'        => 'Strong Password',
        'url'         => 'strongpassword',
        'version'     => '1.0.6',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Enforce a strong password during account creation and password changes',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2962/strong-password',
        'category'    => 'users',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrStrongPassword_init
 */
function jrStrongPassword_init()
{
    jrCore_register_event_listener('jrCore', 'form_validate_exit', 'jrStrongPassword_form_validate_exit_listener');
    return true;
}

/**
 * Check user password and enforce password policy
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrStrongPassword_form_validate_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_conf['jrStrongPassword_active']) && $_conf['jrStrongPassword_active'] == 'on' &&
        isset($_post['module']) && $_post['module'] == 'jrUser' &&
        isset($_post['option']) && ($_post['option'] == 'signup_save' || $_post['option'] == 'account_save')
    ) {

        if ($_post['option'] == 'account_save' && strlen($_post['user_passwd1']) === 0) {
            // Not changing password...
            return $_data;
        }

        $_tc = array(
            'uppercase' => array('/[A-Z]/', 2),
            'lowercase' => array('/[a-z]/', 3),
            'symbol'    => array('/[!@#$%^&*()\-_=+{};:,<.>]/', 4),
            'number'    => array('/[0-9]/', 5)
        );
        $_wr = false;
        $_ln = jrUser_load_lang_strings();
        foreach ($_tc as $name => $_prg) {
            if (isset($_conf["jrStrongPassword_{$name}"]) && $_conf["jrStrongPassword_{$name}"] == 'on') {
                if (preg_match_all($_prg[0], $_post['user_passwd1'], $_mt) < 1) {
                    if (!$_wr) {
                        $_wr = array($_ln['jrStrongPassword'][1]);
                    }
                    $_wr[] = $_ln['jrStrongPassword']["{$_prg[1]}"];
                }
            }
        }
        if (strlen($_post['user_passwd1']) < $_conf['jrStrongPassword_length']) {
            $_wr[] = $_ln['jrStrongPassword'][6] . ' ' . $_conf['jrStrongPassword_length'] . ' ' . $_ln['jrStrongPassword'][7];
        }
        if (isset($_wr[0])) {
            jrCore_set_form_notice('error', implode('<br>&bull;&nbsp;', $_wr), false);
            jrCore_form_field_hilight('user_passwd1');
            jrCore_form_field_hilight('user_passwd2');
            jrCore_form_result();
        }
    }
    return $_data;
}
