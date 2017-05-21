<?php
/**
 * Jamroom System Core module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Information about the Jamroom Core
 * @return array
 */
function jrCore_meta()
{
    $_tmp = array(
        'name'        => 'System Core',
        'url'         => 'core',
        'version'     => '6.0.7',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Provides low level functionality for all system operations',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2857/system-core',
        'category'    => 'core',
        'license'     => 'mpl',
        'locked'      => true,
        'activate'    => true
    );
    return $_tmp;
}

/**
 * Core Initialization
 * @return bool
 */
function jrCore_init()
{
    global $_conf, $_urls, $_mods;

    ob_start();
    if (function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');
    }
    else {
        jrCore_notice('Error', 'required PHP Multibyte String function (mb_internal_encoding) not found - enable in PHP config)');
    }

    // Bring in local config
    if (!@include_once APP_DIR . '/data/config/config.php') {
        header("Location: {$_conf['jrCore_base_url']}/install.php");
        exit;
    }

    // Some core config
    $_conf['jrCore_base_url'] = (isset($_conf['jrCore_base_url']{0})) ? rtrim($_conf['jrCore_base_url'], '/') : jrCore_get_base_url();
    $_conf['jrCore_base_dir'] = APP_DIR;

    // Check for SSL...
    if (strpos($_conf['jrCore_base_url'], 'http:') === 0 && !empty($_SERVER['HTTPS'])) {
        $_conf['jrCore_base_url'] = 'https://' . substr($_conf['jrCore_base_url'], 7);
    }

    // Core magic views
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'admin', 'view_jrCore_admin');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'admin_save', 'view_jrCore_admin_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'stream', 'view_jrCore_stream_file');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'download', 'view_jrCore_download_file');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'template_compare', 'view_jrCore_template_compare');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'template_compare_save', 'view_jrCore_template_compare_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'template_modify', 'view_jrCore_template_modify');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'template_modify_save', 'view_jrCore_template_modify_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'browser', 'view_jrCore_browser');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'browser_item_update', 'view_jrCore_browser_item_update');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'browser_item_update_save', 'view_jrCore_browser_item_update_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'browser_item_delete', 'view_jrCore_browser_item_delete');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'license', 'view_jrCore_license');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'form_designer', 'view_jrCore_form_designer');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'form_designer_save', 'view_jrCore_form_designer_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'form_field_update', 'view_jrCore_form_field_update');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'form_field_update_save', 'view_jrCore_form_field_update_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'dashboard', 'view_jrCore_dashboard');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'item_display_order', 'view_jrCore_item_display_order');

    // Core tool views
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'cache_reset', array('Reset Caches', 'Reset database and filesystem caches'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'integrity_check', array('Integrity Check', 'Validate, Optimize and Repair module and skin installs'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'system_check', array('System Check', 'Display information about your System and installed modules'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'performance_check', array('Performance Check', 'Run a performance test on your server and optionally share the results'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'skin_menu', array('User Menu Editor', 'Customize the items and options that appear in the main User drop down Menu'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'module_detail_features', array('Item Detail Features', 'Set the Order of Item Detail Features provided by modules'));

    // Our default view for admins
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrCore', 'admin/global');

    // Core checktype plugins
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'allowed_html');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'core_string');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'user_name');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'date');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'date_birthday');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'domain');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'email');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'float');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'hex');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'ip_address');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'is_true');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'md5');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'multi_word');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'not_empty');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'signed');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'number');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'number_nn');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'number_nz');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'onoff');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'price');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'printable');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'sha1');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'string');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'url');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'url_name');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'file_name');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'yesno');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'json');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'array');

    // Core form fields supported
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'hidden');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'checkbox');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'checkbox_spambot');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'date');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'datetime');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'file');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'editor');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'optionlist');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'password');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'radio');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'select');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'select_and_text');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'select_multiple');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'text');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'textarea');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'custom');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'live_search');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'notice');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'date_birthday');

    jrCore_register_module_feature('jrTips', 'tip', 'jrCore', 'tip');

    // Bring in core javascript
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jquery-1.12.4.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jquery.simplemodal.1.4.4.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'lightbox-2.7.1.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jquery.livesearch.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'fileuploader.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', APP_DIR . "/modules/jrCore/contrib/jplayer/jquery.jplayer.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', APP_DIR . "/modules/jrCore/contrib/jplayer/jquery.jplayer.playlist.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', "jquery.sortable.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jrCore.js');

    // When javascript is registered, we have a function that is called
    jrCore_register_module_feature_function('jrCore', 'javascript', 'jrCore_enable_external_javascript');

    // Register our core CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jrCore.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jrCore_bbcode.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jrCore_tinymce.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jrCore_dashboard.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'fileuploader.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'lightbox.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jquery.livesearch.css');

    // When CSS is registered, we have a function that is called
    jrCore_register_module_feature_function('jrCore', 'css', 'jrCore_enable_external_css');

    // BBCode string formatter - MUST COME FIRST!
    $_tmp = array(
        'wl'    => 'bbcode',
        'label' => 'Allow BBCode',
        'help'  => 'If active, BBCode tags will be expanded to HTML tags.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrCore', 'jrCore_format_string_bbcode', $_tmp);

    // Allowed HTML string formatter
    $_tmp = array(
        'wl'    => 'html',
        'label' => 'Allow HTML',
        'help'  => 'If active, any HTML tags defined in the Allowed HTML Tags setting will be allowed in the text.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrCore', 'jrCore_format_string_allowed_html', $_tmp);

    // Convert @ tags string formatter
    $_tmp = array(
        'wl'    => 'at_tags',
        'label' => 'Convert @ Tags',
        'help'  => 'If active, links to Profiles written as @profile_name will be linked to the actual Profile.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrCore', 'jrCore_format_string_convert_at_tags', $_tmp);

    // Clickable URLs string formatter
    $_tmp = array(
        'wl'    => 'click_urls',
        'label' => 'Make URLs Clickable',
        'help'  => 'If active, URLs entered into the text will be hyperlinked so they are clickable.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrCore', 'jrCore_format_string_clickable_urls', $_tmp);

    // We don't need sessions on a couple views
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrCore', 'css');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrCore', 'icon_css');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrCore', 'icon_sprite');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrCore', 'test_template');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrCore', 'form_modal_status');

    // No Play Key replacement on some views
    jrCore_register_module_feature('jrCore', 'skip_play_keys', 'jrCore', 'template_compare', 'magic_view');
    jrCore_register_module_feature('jrCore', 'skip_play_keys', 'jrCore', 'template_modify', 'magic_view');

    // Core plugins
    jrCore_register_system_plugin('jrCore', 'email', 'activity', 'Log Sent Email to Activity Log');
    jrCore_register_system_plugin('jrCore', 'email', 'debug', 'Log Sent Email to Debug Log');
    jrCore_register_system_plugin('jrCore', 'media', 'local', 'Local File System');
    jrCore_register_system_plugin('jrCore', 'cache', 'mysql', 'Core Cache (default)');

    // Core event triggers
    jrCore_register_event_trigger('jrCore', 'allowed_html_tags', 'Fired when validating posted HTML in the editor');
    jrCore_register_event_trigger('jrCore', 'approve_pending_item', 'Fired when a pending item is approved by an admin');
    jrCore_register_event_trigger('jrCore', 'reject_pending_item', 'Fired when a pending item is rejected by an admin');
    jrCore_register_event_trigger('jrCore', 'daily_maintenance', 'Fired once a day after midnight server time');
    jrCore_register_event_trigger('jrCore', 'hourly_maintenance', 'Fired once an hour on the first request in the new hour');
    jrCore_register_event_trigger('jrCore', 'minute_maintenance', 'Fired once a minute on the first request in the new minute');
    jrCore_register_event_trigger('jrCore', 'db_increment_key', 'Fired when a datastore key is incremented via jrCore_db_increment_key');
    jrCore_register_event_trigger('jrCore', 'db_decrement_key', 'Fired when a datastore key is decremented via jrCore_db_decrement_key');
    jrCore_register_event_trigger('jrCore', 'db_create_datastore', 'Fired when a new DataStore is initialized');
    jrCore_register_event_trigger('jrCore', 'db_create_item', 'Fired before adding new data while creating a new DataStore item');
    jrCore_register_event_trigger('jrCore', 'db_create_item_exit', 'Fired after creating a new DataStore item and before return');
    jrCore_register_event_trigger('jrCore', 'db_update_item', 'Fired when a DataStore item is updated');
    jrCore_register_event_trigger('jrCore', 'db_get_item', 'Fired when a DataStore item is retrieved');
    jrCore_register_event_trigger('jrCore', 'db_delete_item', 'Fired when a DataStore item is deleted');
    jrCore_register_event_trigger('jrCore', 'db_delete_keys', 'Fired when specific keys are deleted from a DataStore item');
    jrCore_register_event_trigger('jrCore', 'db_search_items', 'Fired with an array of DataStore items that matched the search criteria  - check _items array');
    jrCore_register_event_trigger('jrCore', 'db_search_cache_check', 'Fired before cache is checked with unique Cache Key in _args');
    jrCore_register_event_trigger('jrCore', 'db_search_params', 'Fired when doing a DataStore search for search params');
    jrCore_register_event_trigger('jrCore', 'db_search_simple_keys', 'Fired when doing a DataStore search for keys that do not span multiple value rows');
    jrCore_register_event_trigger('jrCore', 'db_search_count_query', 'Fired with SQL Query and Query Time for COUNT query used in pagination');
    jrCore_register_event_trigger('jrCore', 'db_search_query', 'Fired with SQL Query and Query Time for main _items query');
    jrCore_register_event_trigger('jrCore', 'db_query_init', 'Fired in jrCore_db_query() with query to be run');
    jrCore_register_event_trigger('jrCore', 'db_query_exit', 'Fired in jrCore_db_query() with query results');
    jrCore_register_event_trigger('jrCore', 'db_verify_table', 'Fired in jrCore_db_verify_table() with schema as array');
    jrCore_register_event_trigger('jrCore', 'display_order', 'Fired with entries during the display_order magic view');
    jrCore_register_event_trigger('jrCore', 'download_file', 'Fired when a DataStore file is downloaded');
    jrCore_register_event_trigger('jrCore', 'email_addresses', 'Fired before emails are sent with array of email addresses');
    jrCore_register_event_trigger('jrCore', 'email_prepare', 'Fired just before emails are sent with queue entry data');
    jrCore_register_event_trigger('jrCore', 'email_sent', 'Fired with total sent when email queue entry is complete');
    jrCore_register_event_trigger('jrCore', 'format_string_display', 'Fired at the of jrCore_format_string with results');
    jrCore_register_event_trigger('jrCore', 'form_validate_init', 'Fired at the beginning of jrCore_form_validate()');
    jrCore_register_event_trigger('jrCore', 'form_validate_exit', 'Fired at the end of jrCore_form_validate()');
    jrCore_register_event_trigger('jrCore', 'form_field_create', 'Fired when a form_field is added to a form session');
    jrCore_register_event_trigger('jrCore', 'form_display', 'Fired when a form is displayed (receives form data)');
    jrCore_register_event_trigger('jrCore', 'form_result', 'Fired when a form target view has completed');
    jrCore_register_event_trigger('jrCore', 'get_save_data', 'Fired on exit of jrCore_form_get_save_data()');
    jrCore_register_event_trigger('jrCore', 'html_purifier', 'Fired during HTMLPurifier config setup');
    jrCore_register_event_trigger('jrCore', 'index_template', 'Fired when the skin index template is displayed');
    jrCore_register_event_trigger('jrCore', 'log_message', 'Fired when a message is logged to the Activity Log');
    jrCore_register_event_trigger('jrCore', 'media_playlist', 'Fired when a playlist is assembled in {jrCore_media_player}');
    jrCore_register_event_trigger('jrCore', 'media_player_params', 'Fired when all media player parameters are assembled in {jrCore_media_player}');
    jrCore_register_event_trigger('jrCore', 'module_view', 'Fired when a module view is going to be processed');
    jrCore_register_event_trigger('jrCore', 'parse_url', 'Fired when the current URL has been parsed into $_url');
    jrCore_register_event_trigger('jrCore', 'process_init', 'Fired when the core has initialized');
    jrCore_register_event_trigger('jrCore', 'process_exit', 'Fired when process exits');
    jrCore_register_event_trigger('jrCore', 'profile_template', 'Fired when a profile template is displayed');
    jrCore_register_event_trigger('jrCore', 'run_view_function', 'Fired before a view function is run for a module');
    jrCore_register_event_trigger('jrCore', 'save_media_file', 'Fired when a media file has been saved for a profile');
    jrCore_register_event_trigger('jrCore', 'skin_template', 'Fired when a skin template is displayed');
    jrCore_register_event_trigger('jrCore', 'stream_file', 'Fired when a DataStore file is streamed');
    jrCore_register_event_trigger('jrCore', 'stream_url_error', 'Fired when Media Player encounters a URL error');
    jrCore_register_event_trigger('jrCore', 'system_check', 'Fired in System Check so modules can run own checks');
    jrCore_register_event_trigger('jrCore', 'template_cache_reset', 'Fired when Reset Template Cache is fired');
    jrCore_register_event_trigger('jrCore', 'template_variables', 'Fired for replacement variables when parsing a template');
    jrCore_register_event_trigger('jrCore', 'template_file', 'Fired with template file info when a template is being parsed');
    jrCore_register_event_trigger('jrCore', 'template_list', 'Fired with parameters when {jrCore_list} is called in a template');
    jrCore_register_event_trigger('jrCore', 'parsed_template', 'Fired with the content of a parsed template');
    jrCore_register_event_trigger('jrCore', 'verify_module', 'Fired when a module is verified during Integrity Check or installed');
    jrCore_register_event_trigger('jrCore', 'repair_module', 'Fired when a module is repaired during the Integrity Check');
    jrCore_register_event_trigger('jrCore', 'verify_skin', 'Fired when a skin is verified during Integrity Check or activated');
    jrCore_register_event_trigger('jrCore', 'view_results', 'Fired when results from a module view are displayed');
    jrCore_register_event_trigger('jrCore', '404_not_found', 'Fired when a URL results in a 404 not found.');
    jrCore_register_event_trigger('jrCore', 'tpl_404', 'Fired when a template can not be found.');
    jrCore_register_event_trigger('jrCore', 'create_queue_entry', 'Fired when a process tries to create a queue entry');
    jrCore_register_event_trigger('jrCore', 'queue_entry_created', 'Fired after a queue entry has been created with queue_id');
    jrCore_register_event_trigger('jrCore', 'get_queue_entry', 'Fired when a worker tries to get a queue entry');
    jrCore_register_event_trigger('jrCore', 'release_queue_entry', 'Fired when a worker releases a queue entry back to the queue');
    jrCore_register_event_trigger('jrCore', 'sleep_queue_entry', 'Fired when a worker adjusts the sleep of an existing queue entry');
    jrCore_register_event_trigger('jrCore', 'delete_queue_entry', 'Fired when a worker tries to delete a queue entry');
    jrCore_register_event_trigger('jrCore', 'delete_queue_by_item_id', 'Fired when deleting a queue by module/item_id');
    jrCore_register_event_trigger('jrCore', 'get_queue_info', 'Fired in the Queue Viewer tool to get queue info');
    jrCore_register_event_trigger('jrCore', 'check_queues_ready', 'Fired when checking Queue State');
    jrCore_register_event_trigger('jrCore', 'set_queue_status', 'Fired when setting Queue Status');
    jrCore_register_event_trigger('jrCore', 'get_queue_worker_count', 'Fired when getting number of queue workers for a queue');
    jrCore_register_event_trigger('jrCore', 'all_events', 'Fired when any other event trigger is fired');
    jrCore_register_event_trigger('jrCore', 'admin_tabs', 'Fired when tabs are created for modules in the ACP');
    jrCore_register_event_trigger('jrCore', 'skin_tabs', 'Fired when tabs are created for skins in the ACP');
    jrCore_register_event_trigger('jrCore', 'empty_recycle_bin', 'Fired when the Recycle Bin is emptied');
    jrCore_register_event_trigger('jrCore', 'restore_recycle_bin_item', 'Fired when an item is restored from the Recycle Bin');
    jrCore_register_event_trigger('jrCore', 'expire_recycle_bin', 'Fired when expired items are deleted from the Recycle Bin');
    jrCore_register_event_trigger('jrCore', 'dashboard_tabs', 'Fired when the tab bar is created for the Dashboard');
    jrCore_register_event_trigger('jrCore', 'exclude_item_detail_buttons', 'Fired with button info on Item Detail pages');
    jrCore_register_event_trigger('jrCore', 'exclude_item_index_buttons', 'Fired with button info on Item Index pages');

    // If the tracer module is installed, we have a few events for it
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrCore', 'download_file', 'A user downloads a file');
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrCore', 'stream_file', 'A user streams a file');

    // Set core directory and file permissions
    if (!isset($_conf['jrCore_dir_perms'])) {
        $_conf['jrCore_dir_perms']  = 0755;
        $_conf['jrCore_file_perms'] = 0644;
    }

    // Check for install routine
    if (defined('IN_JAMROOM_INSTALLER')) {
        return true;
    }

    // We have to set a default cache seconds here as $_conf is NOT loaded yet!
    $_conf['jrCore_default_cache_seconds'] = 3600;

    // See if our master config has defined the active skin - it needs to be part of our cache key if it has
    $skn = (isset($_conf['jrCore_active_skin'])) ? $_conf['jrCore_active_skin'] : '';
    $key = md5("jrcore_conf_and_mods_{$skn}");
    jrCore_set_flag('jrcore_config_and_modules_key', $key);
    jrCore_set_flag('jrcore_in_module_init', 1);
    $_rt = _jrCore_mysql_is_cached('jrCore', $key, false);
    if (!$_rt) {

        // Get modules
        $tbl   = jrCore_db_table_name('jrCore', 'module');
        $req   = "SELECT * FROM {$tbl} ORDER BY module_priority ASC";
        $_mods = jrCore_db_query($req, 'module_directory');
        if (!is_array($_mods)) {
            jrCore_notice('Error', "unable to initialize modules - verify installation");
        }

        // Get settings
        $tbl = jrCore_db_table_name('jrCore', 'setting');
        if (isset($_conf['jrCore_active_skin']{1})) {
            $add = "`module` = '{$_conf['jrCore_active_skin']}'";
        }
        else {
            $add = "`module` = (SELECT `value` FROM {$tbl} WHERE `module` = 'jrCore' AND `name` = 'active_skin')";
        }
        $req = "SELECT CONCAT_WS('_', `module`, `name`) AS k, `value` AS v FROM {$tbl} WHERE (`module` IN('" . implode("','", array_keys($_mods)) . "') OR {$add})";
        $_cf = jrCore_db_query($req, 'k', false, 'v');
        if (!is_array($_cf)) {
            jrCore_notice('Error', "unable to initialize settings - verify installation");
        }
        $_conf                                 = array_merge($_cf, $_conf);
        $_conf['jrCore_default_cache_seconds'] = $_cf['jrCore_default_cache_seconds'];
        unset($_cf);

        $_ina = array();
        foreach ($_mods as $k => $v) {
            $_urls["{$v['module_url']}"] = $k;
            if ($k != 'jrCore') {
                // jrCore is already included ;)
                // If this module is NOT active, we add it to our inactive list of modules
                // so we can check in the next loop down any module dependencies
                if ($v['module_active'] != '1') {
                    $_ina[$k] = 1;
                }
                else {
                    // Error redirect here for users that simply try to delete a module
                    // by removing the module directory BEFORE removing the module from the DB!
                    if ((@include_once APP_DIR . "/modules/{$k}/include.php") === false) {
                        // Bad module
                        unset($_mods[$k], $_urls["{$v['module_url']}"]);
                    }
                }
            }
        }

        // init active modules
        foreach ($_mods as $k => $v) {
            if ($k != 'jrCore' && $v['module_active'] == '1') {
                if (isset($v['requires']{0})) {
                    // We have a module that depends on another module to be active
                    foreach (explode(',', trim($v['requires'])) as $req_mod) {
                        if (isset($_ina[$req_mod])) {
                            continue 2;
                        }
                    }
                }
                $func = "{$k}_init";
                if (function_exists($func)) {
                    $func();
                }
                $_mods[$k]['module_initialized'] = 1;
            }
        }
        unset($_ina);
        if (!jrCore_is_developer_mode()) {
            $_rt = array(
                '_conf' => $_conf,
                '_mods' => $_mods,
                '_urls' => $_urls
            );
            _jrCore_mysql_add_to_cache('jrCore', $key, $_rt, 0, 0, false);
        }
    }
    else {
        // We are cached
        $_conf = $_rt['_conf'];
        $_mods = $_rt['_mods'];
        $_urls = $_rt['_urls'];
        // Module setup
        foreach ($_mods as $_md) {
            if ($_md['module_directory'] != 'jrCore') {
                // jrCore is already included ;)
                // Error redirect here for users that simply try to delete a module
                // by removing the module directory BEFORE removing the module from the DB!
                @include_once APP_DIR . "/modules/{$_md['module_directory']}/include.php";
            }
        }
        // .. and init
        foreach ($_mods as $k => $_md) {
            if ($_md['module_directory'] != 'jrCore' && $_md['module_active'] == '1') {
                $func = "{$_md['module_directory']}_init";
                if (function_exists($func)) {
                    $func();
                }
                $_mods[$k]['module_initialized'] = 1;
            }
        }
    }
    jrCore_delete_flag('jrcore_in_module_init');

    // Set our timezone...
    date_default_timezone_set($_conf['jrCore_system_timezone']);

    // Initialize active skin...
    $func = "{$_conf['jrCore_active_skin']}_skin_init";
    if (!function_exists($func)) {
        require APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/include.php";
        if (function_exists($func)) {
            $func();
        }
    }
    ob_end_clean();

    // Core event listeners - must come after $_mods
    jrCore_register_event_listener('jrCore', 'view_results', 'jrCore_view_results_listener');
    jrCore_register_event_listener('jrCore', 'process_exit', 'jrCore_process_exit_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrCore_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'repair_module', 'jrCore_repair_module_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrCore_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'db_create_item_exit', 'jrCore_db_create_item_exit_listener');
    jrCore_register_event_listener('jrCore', 'run_view_function', 'jrCore_run_view_function_listener');
    jrCore_register_event_listener('jrCore', '404_not_found', 'jrCore_404_not_found_listener');
    jrCore_register_event_listener('jrMarket', 'updated_module', 'jrCore_updated_module_listener');

    $_tmp = array(
        'label' => 'pending item',
        'help'  => 'When a new Item is created and is pending review, how do you want to be notified?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrCore', 'pending_item', $_tmp);

    $_tmp = array(
        'label' => 97, // pending item approved
        'help'  => 98  // When a pending item is approved, how do you want to be notified?
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrCore', 'pending_approve', $_tmp);

    // Core item buttons
    $_tmp = array(
        'title'  => 'Item Order Button',
        'icon'   => 'refresh',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrCore', 'jrCore_item_order_button', $_tmp);

    $_tmp = array(
        'title'  => 'Item Create Button',
        'icon'   => 'plus',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrCore', 'jrCore_item_create_button', $_tmp);
    $_tmp['active'] = 'off';
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrCore', 'jrCore_item_create_button', $_tmp);

    $_tmp = array(
        'title'  => 'Item Update Button',
        'icon'   => 'gear',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrCore', 'jrCore_item_update_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrCore', 'jrCore_item_update_button', $_tmp);

    $_tmp = array(
        'title'  => 'Item Delete Button',
        'icon'   => 'trash',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrCore', 'jrCore_item_delete_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrCore', 'jrCore_item_delete_button', $_tmp);

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'queue depth', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'memory used', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'disk usage', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'CPU count', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'installed modules', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'installed skins', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', '1 minute load', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', '5 minute load', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', '15 minute load', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'pending items', 'jrCore_dashboard_panels');

    // provided widgets
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrCore', 'widget_list', 'Item List');

    // We run the core email queue
    $max_workers = 4;
    if (isset($_conf['jrMailer_throttle']) && $_conf['jrMailer_throttle'] > 0) {
        $max_workers = 1;
    }
    jrCore_register_queue_worker('jrCore', 'send_email', 'jrCore_send_email_queue_worker', 0, $max_workers, 60, HIGH_PRIORITY_QUEUE);
    jrCore_register_queue_worker('jrCore', 'send_email_low_priority', 'jrCore_send_email_queue_worker', 0, $max_workers, 60, LOW_PRIORITY_QUEUE);

    // Our Recycle bin media file cleanup worker
    jrCore_register_queue_worker('jrCore', 'empty_recycle_bin_files', 'jrCore_empty_recycle_bin_files_worker', 0, 1, 28800, LOW_PRIORITY_QUEUE);

    // Trigger our process_init event
    jrCore_trigger_event('jrCore', 'process_init', array());
    return true;
}

// Include Library
require APP_DIR . '/modules/jrCore/lib/mysql.php';
require APP_DIR . '/modules/jrCore/lib/datastore.php';
require APP_DIR . '/modules/jrCore/lib/module.php';
require APP_DIR . '/modules/jrCore/lib/media.php';
require APP_DIR . '/modules/jrCore/lib/checktype.php';
require APP_DIR . '/modules/jrCore/lib/smarty.php';
require APP_DIR . '/modules/jrCore/lib/cache.php';
require APP_DIR . '/modules/jrCore/lib/page.php';
require APP_DIR . '/modules/jrCore/lib/form.php';
require APP_DIR . '/modules/jrCore/lib/skin.php';
require APP_DIR . '/modules/jrCore/lib/util.php';
require APP_DIR . '/modules/jrCore/lib/misc.php';
require APP_DIR . '/modules/jrCore/lib/widget.php';
require APP_DIR . '/modules/jrCore/lib/queue.php';

//---------------------------------------------------------
// DASHBOARD
//---------------------------------------------------------

/**
 * User Profiles Dashboard Panels
 * @param $panel
 * @return bool|int
 */
function jrCore_dashboard_panels($panel)
{
    global $_mods;
    // The panel being asked for will come in as $panel
    $out = false;
    switch ($panel) {

        case 'pending items':
            $tbl = jrCore_db_table_name('jrCore', 'pending');
            $req = "SELECT COUNT(pending_id) AS cnt FROM {$tbl} WHERE pending_linked_item_id = 0";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $cnt = ($_rt && is_array($_rt) && isset($_rt['cnt'])) ? intval($_rt['cnt']) : 0;
            $out = array(
                'title' => jrCore_number_format($cnt)
            );
            break;

        case 'installed modules':
            $out = array(
                'title' => count($_mods)
            );
            break;

        case 'installed skins':
            $out = array(
                'title' => count(jrCore_get_skins())
            );
            break;

        case 'queue depth':
            $tbl = jrCore_db_table_name('jrCore', 'queue');
            $req = "SELECT COUNT(*) AS c FROM {$tbl}";
            $_rt = jrCore_db_query($req, 'SINGLE');
            $num = ($_rt && is_array($_rt)) ? intval($_rt['c']) : 0;
            $out = array(
                'title' => jrCore_number_format($num)
            );
            break;

        case 'memory used':
            $_rm = jrCore_get_system_memory();
            if (isset($_rm['percent_used']) && is_numeric($_rm['percent_used'])) {
                $out = array(
                    'title' => $_rm['percent_used'] . "%<br><span>" . jrCore_format_size($_rm['memory_used']) . " of " . jrCore_format_size($_rm['memory_total']) . '</span>',
                    'class' => (isset($_rm['class']) ? $_rm['class'] : 'bigsystem-inf')
                );
            }
            else {
                $out = array(
                    'title' => '?',
                    'class' => 'bigsystem-inf'
                );
            }
            break;

        case 'disk usage':
            $_ds = jrCore_get_disk_usage();
            if (isset($_ds['percent_used']) && is_numeric($_ds['percent_used'])) {
                $out = array(
                    'title' => $_ds['percent_used'] . "%<br><span>" . jrCore_format_size($_ds['disk_used']) . " of " . jrCore_format_size($_ds['disk_total']) . '</span>',
                    'class' => (isset($_ds['class']) ? $_ds['class'] : 'bigsystem-inf')
                );
            }
            else {
                $out = array(
                    'title' => '?',
                    'class' => 'bigsystem-inf'
                );
            }
            break;

        case 'CPU count':
            $_pc = jrCore_get_proc_info();
            if ($_pc && is_array($_pc)) {
                $num = count($_pc);
                jrCore_set_flag('jrCore_dashboard_cpu_num', $num);
                $out = array(
                    'title' => "{$num}<span>@ {$_pc[1]['mhz']}</span>",
                    'class' => 'bigsystem-inf'
                );
            }
            else {
                $out = array(
                    'title' => '?',
                    'class' => 'bigsystem-inf'
                );
            }
            break;

        case '1 minute load':
        case '5 minute load':
        case '15 minute load':
            $min = (int) jrCore_string_field($panel, 1);
            if (!$num = jrCore_get_flag('jrCore_dashboard_cpu_num')) {
                $num = jrCore_get_proc_info();
                if ($num && is_array($num)) {
                    $num = count($num);
                }
            }
            $_ll = jrCore_get_system_load($num);
            if (isset($_ll) && is_array($_ll)) {
                $out = array(
                    'title' => "{$_ll[$min]['level']}<br><span>{$_ll[1]['level']}, {$_ll[5]['level']}, {$_ll[15]['level']}</span>",
                    'class' => $_ll[$min]['class']
                );
            }
            else {
                $out = array(
                    'title' => '?',
                    'class' => 'bigsystem-inf'
                );
            }
            break;

        default:

            // All other "DS" Counts
            if (strpos($panel, 'item count')) {
                $mod = trim(jrCore_string_field($panel, 1));
                $out = array(
                    'title' => jrCore_db_get_datastore_item_count($mod),
                    'graph' => "{$mod}|ds_items_by_day"
                );
            }
            break;

    }
    return ($out) ? $out : false;
}

//---------------------------------------------------------
// ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "order" button for item index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_order_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // See if this module has registered for item order support
    $_tm = jrCore_get_registered_module_features('jrCore', 'item_order_support');
    if (!isset($_tm[$module])) {
        return false;
    }
    if ($test_only) {
        return true;
    }
    $_args['module'] = $module;
    return smarty_function_jrCore_item_order_button($_args, $smarty);
}

/**
 * Return "create" button for an item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_create_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module'] = $module;
    if (!isset($_args['profile_id'])) {
        $_args['profile_id'] = $_item['_profile_id'];
    }
    return smarty_function_jrCore_item_create_button($_args, $smarty);
}

/**
 * Return "update" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_update_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module']     = $module;
    $_args['profile_id'] = $_item['_profile_id'];
    $_args['item_id']    = $_item['_item_id'];
    return smarty_function_jrCore_item_update_button($_args, $smarty);
}

/**
 * Return "delete" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_delete_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module']     = $module;
    $_args['profile_id'] = $_item['_profile_id'];
    $_args['item_id']    = $_item['_item_id'];
    return smarty_function_jrCore_item_delete_button($_args, $smarty);
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Verify Module items
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Make sure our tools are executable
    foreach (array('ffmpeg') as $file) {
        $file = APP_DIR . "/modules/jrCore/tools/{$file}";
        if (is_file($file) && !is_executable($file)) {
            @chmod($file, 0755);
        }
    }

    // Cleanup old precache
    if (jrCore_db_table_exists('jrCore', 'precache')) {
        $tbl = jrCore_db_table_name('jrCore', 'worker');
        $req = "TRUNCATE TABLE {$tbl}";
        jrCore_db_query($req);
        $tbl = jrCore_db_table_name('jrCore', 'precache');
        $req = "TRUNCATE TABLE {$tbl}";
        jrCore_db_query($req);
    }

    // Switch to NEW temp value table from OLD temp table
    if (jrCore_db_table_exists('jrCore', 'temp') && jrCore_db_table_exists('jrCore', 'tempvalue')) {

        // We have not been migrated yet - insert values and drop old table
        $cnt = 0;
        $tbl = jrCore_db_table_name('jrCore', 'temp');
        while (true) {
            $req = "SELECT * FROM {$tbl} GROUP BY temp_module, temp_key ORDER BY temp_updated DESC LIMIT 2500";
            $_rt = jrCore_db_query($req, 'NUMERIC');
            if ($_rt && is_array($_rt)) {
                $_dl = array();
                $_in = array();
                foreach ($_rt as $_t) {
                    $mod   = jrCore_db_escape($_t['temp_module']);
                    $key   = jrCore_db_escape($_t['temp_key']);
                    $_in[] = "('{$mod}','{$key}','" . $_t['temp_updated'] . "','" . jrCore_db_escape($_t['temp_value']) . "')";
                    $_dl[] = "(temp_module = '{$mod}' AND temp_key = '{$key}')";
                }
                $tbv = jrCore_db_table_name('jrCore', 'tempvalue');
                $req = "INSERT INTO {$tbv} (temp_module,temp_key,temp_updated,temp_value) VALUES " . implode(',', $_in) . ' ON DUPLICATE KEY UPDATE temp_updated = UNIX_TIMESTAMP()';
                $cnt += jrCore_db_query($req, 'COUNT');

                // Remove
                $req = "DELETE FROM {$tbl} WHERE " . implode(' OR ', $_dl);
                jrCore_db_query($req);
            }
            else {
                break;
            }
        }
        if ($cnt > 0) {
            $req = "TRUNCATE TABLE {$tbl}";
            jrCore_db_query($req);
            if ($cnt && $cnt > 0) {
                jrCore_logger('INF', "migrated " . jrCore_number_format($cnt) . " unique temp keys to new format");
            }
        }
    }

    // Migrate BBCode settings from jrForum to jrCore
    $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
    $req = "UPDATE {$tbl} SET `value` = REPLACE(`value`, 'jrForum_format_string_bbcode', 'jrCore_format_string_bbcode') WHERE `name` = 'active_formatters' AND `value` LIKE '%jrForum_format_string_bbcode%'";
    jrCore_db_query($req);

    return $_data;
}

/**
 * Repair Modules
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_repair_module_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods;
    foreach ($_mods as $mod_dir => $_inf) {
        if (jrCore_is_datastore_module($mod_dir)) {
            jrCore_db_repair_datastore_items($mod_dir);
        }
    }

    // Migrate Table engines
    jrCore_db_change_table_engine('jrCore', 'cache', 'InnoDB', true);
    jrCore_db_change_table_engine('jrCore', 'form_session', 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'log_debug', 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'play_key', 'InnoDB');
    jrCore_db_change_table_engine('jrCore', 'queue', 'MyISAM');

    // Clean up our pending table of any items that should no longer be
    // pending or are for modules that are no longer in the system
    jrCore_verify_pending_items();

    return $_data;
}

/**
 * Reset Marketplace cache
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_run_view_function_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // If we are doing a marketplace update - reset cache
    if (isset($_post['module']) && $_post['module'] == 'jrMarket' && isset($_post['option']) && $_post['option'] == 'validate_modules' && jrUser_is_admin()) {
        jrCore_delete_all_cache_entries();
    }
    return $_data;
}

/**
 * Set Media Play Keys in HTML pages
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return mixed
 */
function jrCore_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    // Update user location
    $_SESSION['session_updated'] = time();

    // Reset caches for any profile_id's that were changed during process
    jrCore_process_exit_delete_profile_cache();

    // Replace emoji
    $_data = jrCore_replace_emoji($_data);

    // Replace media play keys
    return jrCore_media_set_play_key($_data);
}

/**
 * Run on process exit and used for cleanup/inserting
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_process_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    // Our core process exit listener handles core level cleanup
    // and tasks that should happen after a process shutdown
    // NOTE: client has disconnected at this point!

    if (jrCore_is_view_request() && !jrCore_is_ajax_request()) {

        jrCore_set_flag('jrcore_logger_system_user_active', 1);

        // Run maintenance checks
        // Note: the maintenance calls must come BEFORE we check if queues are
        // active since maintenance listeners can create new Queue entries
        if (jrCore_minute_maintenance_check()) {
            // If we return TRUE out of MINUTE maintenance, it can be time for HOURLY maintenance
            if (jrCore_hourly_maintenance_check()) {
                // And if we return true out of HOURLY maintenance, it can be time for DAILY maintenance
                jrCore_daily_maintenance_check();
            }
        }

        // Check if Queues are enabled ...
        if (!isset($_conf['jrCore_queues_active']) || $_conf['jrCore_queues_active'] == 'on') {

            // They are - get modules that have registered Queue Workers ...
            // jrCore_register_queue_worker('Module', 'QueueName', 'Module_WorkerFunction', <int:Queue Entries to Process>, <int:Max Simultaneous Workers>, <int:Worker Queue Timeout>, <int(1-9):Worker Priority>);
            if ($_tmp = jrCore_get_flag('jrcore_register_queue_worker')) {

                // And see if we have any queue entries
                if ($_qn = jrCore_get_ready_queues()) {

                    // Do we have a MAXIMUM number of worker processes that can be running at any one time, system wide?
                    $max = 12;
                    if (isset($_conf['jrCore_max_system_queue_workers']) && jrCore_checktype($_conf['jrCore_max_system_queue_workers'], 'number_nn')) {
                        $max = (int) $_conf['jrCore_max_system_queue_workers'];
                    }
                    if ($max > 0) {
                        // How many queues are being worked RIGHT NOW
                        if (jrCore_get_active_worker_count() >= $max) {
                            // We are AT or OVER the number of queues allowed to be worked system wide - EXIT
                            jrCore_process_exit_delete_profile_cache();
                            jrCore_process_exit_delete_profile_skin_cache();
                            return $_data;
                        }
                    }

                    // Conversions and other queue-based work can take a long time to run
                    set_time_limit(0);

                    foreach ($_tmp as $priority => $_modules) {
                        foreach ($_modules as $mod => $_queue) {
                            foreach ($_queue as $qname => $qdat) {

                                // Only process entries for queue we actually have
                                if (!isset($_qn["{$mod}_{$qname}"])) {
                                    continue;
                                }

                                $func = $qdat[0]; // Queue Function that is going to be run
                                if (!function_exists($func)) {
                                    jrCore_logger('MAJ', "registered queue worker function: {$func} for module: {$mod} does not exist");
                                    continue;
                                }
                                // How many Queue Entries can this worker work before exiting?
                                $qcnt = intval($qdat[1]);
                                if ($qcnt === 0) {
                                    $qcnt = 1000000; // "0" means "all" 0 set this to a high number here
                                }
                                while ($qcnt > 0) {

                                    if (jrCore_queues_are_active()) {

                                        // Maximum number of workers that can be running at one time
                                        $maxw = (isset($qdat[2])) ? intval($qdat[2]) : 1;

                                        // Worker Timeout (in seconds)
                                        $tout = (isset($qdat[3]) && is_numeric($qdat[3])) ? intval($qdat[3]) : 3600;

                                        if (jrCore_queue_worker_count($mod, $qname, $tout, $maxw) < $maxw) {

                                            // We are under the max allowed workers for this queue
                                            // and have been assigned a worker queue slot
                                            $_tmp = jrCore_queue_get($mod, $qname, null, null, $tout);
                                            if ($_tmp && isset($_tmp['queue_id'])) {

                                                // We found a queue entry - pass it on to the worker
                                                $ret = $func($_tmp['queue_data']);

                                                // Our queue workers can return:
                                                // 1) TRUE - everything is good, delete queue entry
                                                // 2) # - indicates we should "hide" the queue entry for # number of seconds before allowing another worker to pick it up - default is 10 seconds
                                                // 3) FALSE - an issue was encountered processing the queue
                                                // 4) EXIT - force exit of queue worker loop
                                                if ($ret === true) {

                                                    // We are done working - release our slot
                                                    jrCore_queue_release_worker_slot($mod, $qname, 1);

                                                    // We successfully processed our queue entry - delete it
                                                    jrCore_queue_delete($_tmp['queue_id']);

                                                }
                                                elseif ($ret === 'EXIT') {
                                                    // Forced exit by worker - NO CHANGE to queue depth
                                                    jrCore_queue_release_worker_slot($mod, $qname);
                                                    break;
                                                }
                                                else {

                                                    // This queue is going to be re-worked - no change in queue depth
                                                    jrCore_queue_release_worker_slot($mod, $qname);

                                                    $sec = 10;
                                                    if (jrCore_checktype($ret, 'number_nn')) {
                                                        $sec = (int) $ret;
                                                    }
                                                    jrCore_queue_release($_tmp['queue_id'], $sec);

                                                }
                                                $qcnt--;
                                                jrCore_db_close();
                                            }
                                            else {
                                                // We did NOT get a queue - release
                                                jrCore_queue_release_worker_slot($mod, $qname);
                                                break;
                                            }
                                        }
                                        else {
                                            // We are over our max allowed worker processes for this queue - next queue
                                            break;
                                        }
                                    }
                                    else {
                                        // Queues are NOT active - do not start another worker - return
                                        break 4;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Delete and reset profile caches + skin cache
    jrCore_process_exit_delete_profile_cache();
    jrCore_process_exit_delete_profile_skin_cache();
    return $_data;
}

/**
 * Keep jrCore cache directory clean during daily maintenance
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return mixed
 */
function jrCore_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // We will delete any old upload directories not accessed in 24 hours
    $old = (time() - 86400);
    $cdr = jrCore_get_module_cache_dir('jrCore');
    if (!is_dir($cdr)) {
        jrCore_logger('CRI', 'Unable to open jrCore cache dir for cleaning');
        return true;
    }
    $c = 0;
    $f = opendir($cdr);
    if ($f) {
        while ($file = readdir($f)) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir("{$cdr}/{$file}")) {
                $_tmp = stat("{$cdr}/{$file}");
                if (isset($_tmp['mtime']) && $_tmp['mtime'] < $old) {
                    jrCore_delete_dir_contents("{$cdr}/{$file}");
                    @rmdir("{$cdr}/{$file}");
                    $c++;
                }
            }
        }
        closedir($f);
    }

    // Delete debug and error logs that have not been written to in 3 days
    $old = (time() - 259200);
    foreach (array('error_log', 'debug_log') as $log) {
        $log_file = APP_DIR . "/data/logs/{$log}";
        if (is_file($log_file) && filemtime($log_file) < $old) {
            unlink($log_file);
        }
    }

    // Cleanup Recycle Bin
    if (isset($_conf['jrCore_recycle_bin_expire']) && jrCore_checktype($_conf['jrCore_recycle_bin_expire'], 'number_nz')) {

        // Delete expired recycle bin
        $old = ($_conf['jrCore_recycle_bin_expire'] * 86400);
        $tbl = jrCore_db_table_name('jrCore', 'recycle');
        $req = "SELECT r_module AS module, r_profile_id AS profile_id, r_item_id AS item_id, r_data AS data FROM {$tbl} WHERE r_time < (UNIX_TIMESTAMP() - {$old})";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {

            // Cleanup
            $req = "DELETE FROM {$tbl} WHERE r_time < (UNIX_TIMESTAMP() - {$old})";
            $cnt = jrCore_db_query($req, 'COUNT');
            if ($cnt && $cnt > 0) {

                // Remove media
                foreach ($_rt as $_i) {
                    $_tm = json_decode($_i['data'], true);
                    if ($_tm && is_array($_tm) && isset($_tm['rb_item_media'])) {
                        $_fl = jrCore_get_media_files($_i['profile_id'], 'rb_*');
                        if ($_fl && is_array($_fl)) {
                            foreach ($_fl as $_file) {
                                $name = basename($_file['name']);
                                if (strpos($name, "rb_{$_i['module']}_{$_i['item_id']}_") === 0) {
                                    jrCore_delete_media_file($_i['profile_id'], $name);
                                }
                            }
                        }
                    }
                }

                // Trigger event for any modules that may need to manually clean up
                $_args = array(
                    '_items' => $_rt
                );
                jrCore_trigger_event('jrCore', 'expire_recycle_bin', $_args);
            }
        }
    }

    return true;
}

/**
 * Cleanup from files uploaded via {jrCore_upload_button}
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_db_create_item_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (jrCore_is_automatic_upload_handling_enabled() && isset($_post['upload_module']) && $_post['upload_module'] == $_args['module'] && isset($_post['upload_token']) && isset($_post['upload_field'])) {
        // Save any uploaded media file
        if (jrCore_is_uploaded_media_file($_post['upload_module'], $_post['upload_field'], $_data['_profile_id'])) {
            $_data['_item_id'] = $_args['_item_id'];
            if (jrCore_save_media_file($_post['upload_module'], $_post['upload_field'], $_data['_profile_id'], $_args['_item_id'], $_post['upload_field'], $_data)) {
                // Clean up any file uploads
                $dir = jrCore_get_upload_temp_directory($_post['upload_token']);
                if (is_dir($dir)) {
                    jrCore_delete_dir_contents($dir);
                    rmdir($dir);
                }
            }
        }
    }
    return $_data;
}

/**
 * Log 404 not found if configured
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_404_not_found_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf, $_post;
    if (isset($_conf['jrCore_log_404']) && $_conf['jrCore_log_404'] == 'on') {
        $uri = '/';
        if (isset($_data['_uri'])) {
            $uri = jrCore_strip_html($_data['_uri']);
        }
        // If this is a request for an old JS or CSS cache page, ignore
        if (strpos($uri, 'cart?fcsid=') || (strpos($uri, '/data/cache/') === 0 && (strpos($uri, '.js') || strpos($uri, '.css') || strpos(' ' . $uri, '.well-known')))) {
            return $_data;
        }
        $_er = array(
            '_post'    => $_post,
            'referrer' => (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'none',
            'client'   => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown'
        );
        jrCore_logger('MIN', '404 Page not found: ' . $uri, $_er);
    }
    return $_data;
}

/**
 * Updated module - ensure latest repair.php is setup
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_updated_module_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['module_directory']) && $_data['module_directory'] == 'jrCore') {
        // Make sure we are running the latest copy of the repair.php.html script
        @copy(APP_DIR . '/modules/jrCore/root/repair.php.html', APP_DIR . '/repair.php.html');
        jrCore_validate_module_schema('jrCore');
    }
    return $_data;
}
