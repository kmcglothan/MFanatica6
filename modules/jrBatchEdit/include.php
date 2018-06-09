<?php
/**
 * Jamroom Batch Item Editor module
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
function jrBatchEdit_meta()
{
    $_tmp = array(
        'name'        => 'Batch Item Editor',
        'url'         => 'batchedit',
        'version'     => '1.0.11',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Easily edit DataStore items in batches',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2922/batch-item-editor',
        'category'    => 'admin',
        'priority'    => 255,
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrBatchEdit_init()
{
    // include
    jrCore_register_module_feature('jrCore', 'css', 'jrBatchEdit', 'jrBatchEdit.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrBatchEdit', 'jrBatchEdit.js', 'admin');

    // Core magic views
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrBatchEdit', 'batch', 'view_jrBatchEdit_batch');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrBatchEdit', 'batch_save', 'view_jrBatchEdit_batch_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrBatchEdit', 'batch_update', 'view_jrBatchEdit_batch_update');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrBatchEdit', 'batch_update_save', 'view_jrBatchEdit_batch_update_save');

    jrCore_register_event_listener('jrCore', 'view_results', 'jrBatchEdit_view_results_listener');

    // Register this tool for every module that has a datastore
    $_tmp = jrCore_get_datastore_modules();
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $mod => $pfx) {
            switch ($mod) {
                case 'jrCore':
                    break;
                default:
                    jrCore_register_module_feature('jrCore', 'tool_view', $mod, 'batch', array('Batch Edit Items', 'Edit selected DataStore item fields in batches'));
                    break;
            }
        }
    }

    return true;
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * Limit width of Batch Edit window
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBatchEdit_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_post['option']) && $_post['option'] == 'batch_update' && isset($_post['_1']) && strlen($_post['_1']) === 16) {
        $_repl = array(
            '<th class="page_table_header"'                      => '<th class="page_table_header batch-edit-header-text"',
            '<table class="page_table batch-edit-table-header">' => '<div style="max-width:1120px;overflow:auto"><table class="page_table batch-edit-table-header">',
            '<table class="page_table_pager">'                   => '<div style="max-width:1120px"><table class="page_table_pager">'
        );
        $_data = str_replace(array_keys($_repl), $_repl, $_data);
    }
    return $_data;
}
