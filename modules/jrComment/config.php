<?php
/**
 * Jamroom Comments module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * config
 */
function jrComment_config()
{
    $_opt = array(
        0 => "0 (wait timer disabled)"
    );
    foreach (range(1, 30) as $min) {
        $_opt[$min] = $min;
    }
    // System Name
    $_tmp = array(
        'name'     => 'wait_time',
        'default'  => 1,
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nn',
        'required' => 'on',
        'label'    => 'comment wait timer',
        'help'     => 'How many minutes must elapse before a user can post another comment?',
        'section'  => 'general settings',
        'order'    => 1
    );
    jrCore_register_setting('jrComment', $_tmp);

    // Enable Editor
    $_tmp = array(
        'name'     => 'editor',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'enable editor',
        'help'     => 'Check this option to enable the WYSIWYG editor for the new comment textarea form field',
        'section'  => 'general settings',
        'order'    => 2
    );
    jrCore_register_setting('jrComment', $_tmp);

    // Check Modules
    $_tmp = array(
        'name'     => 'check_modules',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'only active modules',
        'help'     => 'Check this option to add an additional check that ensures Comments only appear for modules that are currently Active.  For large and active systems it is recommended to leave this option unchecked as it adds query overhead that can slow down Comment listings.',
        'section'  => 'general settings',
        'order'    => 3
    );
    jrCore_register_setting('jrComment', $_tmp);

    $_opt = array(
        'desc' => "Newest Comment First",
        'asc'  => "Oldest Comment First (default)"
    );
    // Direction
    $_tmp = array(
        'name'     => 'direction',
        'default'  => 'asc',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'printable',
        'required' => 'on',
        'label'    => 'comment sort direction',
        'help'     => 'How should comments be sorted on the page?',
        'section'  => 'list options',
        'order'    => 10
    );
    jrCore_register_setting('jrComment', $_tmp);

    // Pagebreak
    $_tmp = array(
        'name'     => 'pagebreak',
        'default'  => '0',
        'type'     => 'text',
        'validate' => 'number_nn',
        'required' => 'on',
        'label'    => 'comments per page',
        'help'     => 'How many comments should be shown on each page?<br><br><strong>Note:</strong> set to 0 (zero) to disable pagination for comments',
        'section'  => 'list options',
        'order'    => 11
    );
    jrCore_register_setting('jrComment', $_tmp);

    // Threading
    $_tmp = array(
        'name'     => 'threading',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'enable threading',
        'help'     => 'If checked, Comment threading will be enabled which allows a user to &quot;reply&quot; to another comment and have their comment show up threaded under the comment they are replying to.',
        'section'  => 'list options',
        'order'    => 12
    );
    jrCore_register_setting('jrComment', $_tmp);

    // Delete Thread
    $_tmp = array(
        'name'     => 'save_thread',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'save threads',
        'help'     => 'If this option is checked, when a comment in a thread that has replies is deleted, the deleted comment will be replaced with <strong>(deleted)</strong> (Language ID 19) so the replies remain.  If this is NOT checked, any comments that are replies to the comment being deleted will also be removed.  If there are no replies to the comment the comment is removed.',
        'section'  => 'list options',
        'order'    => 13
    );
    jrCore_register_setting('jrComment', $_tmp);

    // Show Quote Button
    $_tmp = array(
        'name'     => 'quote_button',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'show quote button',
        'help'     => 'If this option is checked, a &quot;Quote User&quot; button will be shown on comments that allows the user to quota the user above them.  This can be helpful if you are not using threaded comments.',
        'section'  => 'list options',
        'order'    => 14
    );
    jrCore_register_setting('jrComment', $_tmp);

    return true;
}
