<?php
/**
 * Jamroom Timeline module
 *
 * copyright 2018 The Jamroom Network
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
 * jrAction_config
 */
function jrAction_config()
{
    // Max Action Text Length
    $_tmp = array(
        'name'     => 'max_length',
        'type'     => 'text',
        'validate' => 'number_nz',
        'default'  => 140,
        'label'    => 'Max Update Length',
        'help'     => 'What is the maximum length of text allowed (in characters) in a Timeline Update?',
        'min'      => 100,
        'max'      => 256000,
        'order'    => 1
    );
    jrCore_register_setting('jrAction', $_tmp);

    // User Editor
    $_tmp = array(
        'name'     => 'editor',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'enable editor',
        'help'     => 'Check this option to enable the WYSIWYG editor for the &quot;Post a new Activity Update&quot; form field',
        'order'    => 2
    );
    jrCore_register_setting('jrAction', $_tmp);

    // Prune System Entries
    $_opt = array(
        0 => 'Pruning Disabled',
        1 => '1 day'
    );
    foreach (range(2, 120) as $day) {
        $_opt[$day] = "{$day} days";
    }
    $_tmp = array(
        'name'     => 'prune',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nn',
        'required' => 'on',
        'default'  => 0,
        'label'    => 'prune system entries',
        'help'     => 'Enabling the Prune System Entries option will delete any system generated (i.e. created an item, updated an item, etc.) Timeline entries after the number of days specified.<br><br><b>NOTE:</b> Timeline entries that are manually created by users are never deleted.',
        'order'    => 3
    );
    jrCore_register_setting('jrAction', $_tmp);

    // Check Modules
    $_tmp = array(
        'name'     => 'check_modules',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'only active modules',
        'help'     => 'Check this option to add an additional check that ensures Timeline Entries only appear for modules that are currently Active.  For large and active systems it is recommended to uncheck this option as it adds query overhead that can slow down the Timeline display.',
        'order'    => 4
    );
    jrCore_register_setting('jrAction', $_tmp);

    // Delete action when item is deleted
    $_tmp = array(
        'name'     => 'delete_with_item',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'delete timeline entries',
        'help'     => 'If this option is checked, when an item is deleted that has associated created and updated Timeline entries, the Timeline entries will be deleted as well.',
        'order'    => 5
    );
    jrCore_register_setting('jrAction', $_tmp);

    // Quick Share
    $_tmp = array(
        'name'     => 'quick_share',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'enable quick share',
        'help'     => 'Check this option to enable the Quick Share options in a Profile timeline.',
        'order'    => 1,
        'section'  => 'Quick Share'
    );
    jrCore_register_setting('jrAction', $_tmp);

    return true;
}
