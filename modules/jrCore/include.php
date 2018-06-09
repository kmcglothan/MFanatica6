<?php
/**
 * Jamroom System Core module
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
    return array(
        'name'        => 'System Core',
        'url'         => 'core',
        'version'     => '6.1.7',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Provides low level functionality for all system operations',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2857/system-core',
        'category'    => 'core',
        'license'     => 'mpl',
        'locked'      => true,
        'activate'    => true
    );
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

    // Load up config file and default values for some items
    jrCore_load_config_file_and_defaults();

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
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'advanced_config', array('Advanced Config', 'Add or Delete advanced config options in the config.php file'));

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
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'lightbox-2.9.0.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jquery.livesearch.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'fileuploader.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', APP_DIR . "/modules/jrCore/contrib/jplayer/jquery.jplayer.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', APP_DIR . "/modules/jrCore/contrib/jplayer/jquery.jplayer.playlist.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', APP_DIR . "/modules/jrCore/contrib/sweetalert/sweetalert.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', "jquery.sortable.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jrCore_admin.js', 'admin');
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
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'sweetalert.css');

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
    jrCore_register_system_plugin('jrCore', 'cache', 'mysql', 'Core Data Cache (default)');

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
    jrCore_register_event_trigger('jrCore', 'db_truncate_datastore', 'Fired when a DataStore is emptied (truncated)');
    jrCore_register_event_trigger('jrCore', 'db_delete_datastore', 'Fired when a DataStore is deleted');
    jrCore_register_event_trigger('jrCore', 'db_create_item_data', 'Fired with data before creating a new DataStore item');
    jrCore_register_event_trigger('jrCore', 'db_create_item', 'Fired with item_id and data before adding keys to new DataStore item');
    jrCore_register_event_trigger('jrCore', 'db_create_item_exit', 'Fired with _item_id and data after creating new DataStore item');
    jrCore_register_event_trigger('jrCore', 'db_update_item', 'Fired when a DataStore item is updated');
    jrCore_register_event_trigger('jrCore', 'db_get_item', 'Fired when a DataStore item is retrieved');
    jrCore_register_event_trigger('jrCore', 'db_delete_item', 'Fired when a DataStore item is deleted');
    jrCore_register_event_trigger('jrCore', 'db_delete_keys', 'Fired when specific keys are deleted from a DataStore item');
    jrCore_register_event_trigger('jrCore', 'db_delete_key_from_all_items', 'Fired when a key is deleted from all items in a DataStore');
    jrCore_register_event_trigger('jrCore', 'db_search_items', 'Fired with an array of DataStore items that matched the search criteria  - check _items array');
    jrCore_register_event_trigger('jrCore', 'db_search_cache_check', 'Fired before cache check in DataStore search with unique Cache Key in _args');
    jrCore_register_event_trigger('jrCore', 'db_search_params', 'Fired when doing a DataStore search for search params');
    jrCore_register_event_trigger('jrCore', 'db_search_simple_keys', 'Fired when doing a DataStore search for keys that do not span multiple value rows');
    jrCore_register_event_trigger('jrCore', 'db_search_count_query', 'Fired with SQL Query and Query Time for COUNT query used in pagination');
    jrCore_register_event_trigger('jrCore', 'db_search_query', 'Fired with SQL Query and Query Time for main _items query');
    jrCore_register_event_trigger('jrCore', 'db_search_results', 'Fired with results from jrCore_db_search_items - including cached');
    jrCore_register_event_trigger('jrCore', 'db_query_init', 'Fired in jrCore_db_query() with query to be run');
    jrCore_register_event_trigger('jrCore', 'db_query_exit', 'Fired in jrCore_db_query() with query results');
    jrCore_register_event_trigger('jrCore', 'db_verify_table', 'Fired in jrCore_db_verify_table() with schema as array');
    jrCore_register_event_trigger('jrCore', 'db_connect', 'Fired in jrCore_db_connect() with connection configuration');
    jrCore_register_event_trigger('jrCore', 'db_connect_error', 'Fired in jrCore_db_connect() when unable to connect to server');
    jrCore_register_event_trigger('jrCore', 'display_order', 'Fired with entries during the display_order magic view');
    jrCore_register_event_trigger('jrCore', 'upload_prepare', 'Fired when a file is uploaded and before it is saved');
    jrCore_register_event_trigger('jrCore', 'upload_saved', 'Fired when an uploaded file is saved to the filesystem');
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
    jrCore_register_event_trigger('jrCore', 'media_allowed_referrer', 'Fired when checking if referrer is allowed in media stream_file');
    jrCore_register_event_trigger('jrCore', 'get_media_function', 'Fired in media functions to get active media system function');
    jrCore_register_event_trigger('jrCore', 'module_view', 'Fired when a module view is going to be processed');
    jrCore_register_event_trigger('jrCore', 'module_activated', 'Fired when a module has been activated from the info tab');
    jrCore_register_event_trigger('jrCore', 'module_deactivated', 'Fired when a module has been made inactive from the info tab');
    jrCore_register_event_trigger('jrCore', 'module_deleted', 'Fired when a module has been deleted from the info tab');
    jrCore_register_event_trigger('jrCore', 'skin_deleted', 'Fired when a skin has been deleted from the info tab');
    jrCore_register_event_trigger('jrCore', 'parse_url', 'Fired when the current URL has been parsed into $_url');
    jrCore_register_event_trigger('jrCore', 'is_local_url', 'Fired when checking if a URL is a local URL');
    jrCore_register_event_trigger('jrCore', 'get_current_url', 'Fired when getting current URL for user');
    jrCore_register_event_trigger('jrCore', 'get_local_referrer', 'Fired when getting referring URL for user');
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
    jrCore_register_event_trigger('jrCore', 'view_results', 'Fired just before results from a view are displayed');
    jrCore_register_event_trigger('jrCore', 'results_sent', 'Fired just after results from a view are displayed');
    jrCore_register_event_trigger('jrCore', '404_not_found', 'Fired when a URL results in a 404 not found');
    jrCore_register_event_trigger('jrCore', 'tpl_404', 'Fired when a template can not be found');
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
    jrCore_register_event_trigger('jrCore', 'queue_worker_can_work', 'Fired when checking if a process can become a queue worker');
    jrCore_register_event_trigger('jrCore', 'can_be_queue_worker', 'Fired to check if current process can become a queue worker');
    jrCore_register_event_trigger('jrCore', 'release_queue_worker_slot', 'Fired when a queue worker is releasing a queue slot');
    jrCore_register_event_trigger('jrCore', 'decrement_queue_depth', 'Fired when the depth of a queue is decremented');
    jrCore_register_event_trigger('jrCore', 'check_for_dead_queue_workers', 'Fired when a check is made for dead workers');
    jrCore_register_event_trigger('jrCore', 'queue_worker_count', 'Fired when getting count of active queue workers');
    jrCore_register_event_trigger('jrCore', 'all_events', 'Fired when any other event trigger is fired');
    jrCore_register_event_trigger('jrCore', 'admin_tabs', 'Fired when tabs are created for modules in the ACP');
    jrCore_register_event_trigger('jrCore', 'skin_tabs', 'Fired when tabs are created for skins in the ACP');
    jrCore_register_event_trigger('jrCore', 'empty_recycle_bin', 'Fired when the Recycle Bin is emptied');
    jrCore_register_event_trigger('jrCore', 'restore_recycle_bin_item', 'Fired when an item is restored from the Recycle Bin');
    jrCore_register_event_trigger('jrCore', 'expire_recycle_bin', 'Fired when expired items are deleted from the Recycle Bin');
    jrCore_register_event_trigger('jrCore', 'dashboard_tabs', 'Fired when the tab bar is created for the Dashboard');
    jrCore_register_event_trigger('jrCore', 'exclude_item_detail_buttons', 'Fired with button info on Item Detail pages');
    jrCore_register_event_trigger('jrCore', 'exclude_item_index_buttons', 'Fired with button info on Item Index pages');
    jrCore_register_event_trigger('jrCore', 'create_master_css', 'Fired when creating the site master CSS file');
    jrCore_register_event_trigger('jrCore', 'create_master_javascript', 'Fired when creating the site master Javascript files');
    jrCore_register_event_trigger('jrCore', 'master_css_src', 'Fired when creating URL to site master CSS file');
    jrCore_register_event_trigger('jrCore', 'master_javascript_src', 'Fired when creating URL to site master Javascript file');
    jrCore_register_event_trigger('jrCore', 'global_config_created', 'Fired when the Global Config is first created');
    jrCore_register_event_trigger('jrCore', 'global_config_updated', 'Fired when the Global Config is updated');
    jrCore_register_event_trigger('jrCore', 'delete_config_cache', 'Fired when the Core config cache is reset');

    // If the tracer module is installed, we have a few events for it
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrCore', 'download_file', 'A user downloads a file');
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrCore', 'stream_file', 'A user streams a file');

    // Check for install routine
    if (defined('IN_JAMROOM_INSTALLER')) {
        return true;
    }

    // See if our master config has defined the active skin - it needs to be part of our cache key if it has
    $skn = (isset($_conf['jrCore_active_skin'])) ? $_conf['jrCore_active_skin'] : '';
    $key = md5("jrcore_conf_and_mods_{$skn}_{$_conf['jrCore_base_url']}");
    jrCore_set_flag('jrcore_config_and_modules_key', $key);
    jrCore_set_flag('jrcore_in_module_init', 1);

    // If we are part of a cluster, our config and settings can be cached by the Cloud Core
    // as a JSON file - if we have that, load that in place instead of going to the DB
    if (!$_conf['jrCore_custom_domain_loaded'] && is_file(APP_DIR . '/data/config/config_cache.php')) {
        if ($_tmp = file_get_contents(APP_DIR . '/data/config/config_cache.php')) {
            if ($_tmp = json_decode($_tmp, true)) {
                // We are cached
                $_conf = array_merge($_tmp['_conf'], $_conf);
                $_mods = $_tmp['_mods'];
                $_urls = $_tmp['_urls'];
                // Module setup
                foreach ($_mods as $_md) {
                    if ($_md['module_directory'] != 'jrCore' && $_md['module_active'] == '1') {
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
                $_conf = jrCore_trigger_event('jrCore', 'global_config_created', $_conf);
            }
        }
    }

    if (count($_mods) === 0) {
        $_rt = _jrCore_mysql_is_cached('jrCore', $key, false);
        if (!$_rt) {
            // Init $_conf, $_mods and $_urls
            jrCore_init_conf_mods_and_urls();
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
                if ($_md['module_directory'] != 'jrCore' && $_md['module_active'] == '1') {
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
        $_conf = jrCore_trigger_event('jrCore', 'global_config_created', $_conf);
    }
    jrCore_delete_flag('jrcore_in_module_init');

    // Set our timezone...
    date_default_timezone_set($_conf['jrCore_system_timezone']);

    // Initialize active skin...
    $func = "{$_conf['jrCore_active_skin']}_skin_init";
    if (!function_exists($func)) {
        require_once APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/include.php";
        if (function_exists($func)) {
            $func();
        }
    }
    ob_end_clean();

    // Core event listeners - must come after $_mods
    jrCore_register_event_listener('jrCore', 'view_results', 'jrCore_view_results_listener');
    jrCore_register_event_listener('jrCore', 'results_sent', 'jrCore_results_sent_listener');
    jrCore_register_event_listener('jrCore', 'process_exit', 'jrCore_process_exit_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrCore_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'repair_module', 'jrCore_repair_module_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrCore_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'db_create_item_exit', 'jrCore_db_create_item_exit_listener');
    jrCore_register_event_listener('jrCore', 'run_view_function', 'jrCore_run_view_function_listener');
    jrCore_register_event_listener('jrCore', '404_not_found', 'jrCore_404_not_found_listener');
    jrCore_register_event_listener('jrCore', 'parse_url', 'jrCore_parse_url_listener');

    jrCore_register_event_listener('jrUser', 'session_started', 'jrCore_session_started_listener');
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

    $_tmp = array(
        'title'  => 'Create Bundle Button',
        'icon'   => 'plus',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_bundle_index_button', 'jrCore', 'jrCore_bundle_create_button', $_tmp);
    $_tmp['active'] = 'off';
    jrCore_register_module_feature('jrCore', 'item_bundle_list_button', 'jrCore', 'jrCore_bundle_create_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_bundle_detail_button', 'jrCore', 'jrCore_bundle_create_button', $_tmp);

    $_tmp = array(
        'title'  => 'Update Bundle Button',
        'icon'   => 'gear',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_bundle_list_button', 'jrCore', 'jrCore_bundle_update_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_bundle_detail_button', 'jrCore', 'jrCore_bundle_update_button', $_tmp);

    $_tmp = array(
        'title'  => 'Delete Bundle Button',
        'icon'   => 'trash',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_bundle_list_button', 'jrCore', 'jrCore_bundle_delete_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_bundle_detail_button', 'jrCore', 'jrCore_bundle_delete_button', $_tmp);

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

    // Delete media files worker
    jrCore_register_queue_worker('jrCore', 'db_delete_item_media', 'jrCore_db_delete_item_media_worker', 0, 4, 3600, LOW_PRIORITY_QUEUE);

    // Minute/Hourly/Daily maintenance worker
    jrCore_register_queue_worker('jrCore', 'minute_maintenance', 'jrCore_minute_maintenance_worker', 0, 1, 55, HIGH_PRIORITY_QUEUE);
    jrCore_register_queue_worker('jrCore', 'hourly_maintenance', 'jrCore_hourly_maintenance_worker', 0, 1, 3500, LOW_PRIORITY_QUEUE);
    jrCore_register_queue_worker('jrCore', 'daily_maintenance', 'jrCore_daily_maintenance_worker', 0, 1, 86000, LOW_PRIORITY_QUEUE);

    // Test Queues Worker
    jrCore_register_queue_worker('jrCore', 'test_queue_system', 'jrCore_test_queue_system_worker', 0, 4, 10);

    // Trigger our process_init event
    jrCore_trigger_event('jrCore', 'process_init', array());
    return true;
}

// Include Library
require_once APP_DIR . '/modules/jrCore/lib/mysql.php';
require_once APP_DIR . '/modules/jrCore/lib/datastore.php';
require_once APP_DIR . '/modules/jrCore/lib/module.php';
require_once APP_DIR . '/modules/jrCore/lib/media.php';
require_once APP_DIR . '/modules/jrCore/lib/checktype.php';
require_once APP_DIR . '/modules/jrCore/lib/smarty.php';
require_once APP_DIR . '/modules/jrCore/lib/cache.php';
require_once APP_DIR . '/modules/jrCore/lib/page.php';
require_once APP_DIR . '/modules/jrCore/lib/form.php';
require_once APP_DIR . '/modules/jrCore/lib/skin.php';
require_once APP_DIR . '/modules/jrCore/lib/util.php';
require_once APP_DIR . '/modules/jrCore/lib/misc.php';
require_once APP_DIR . '/modules/jrCore/lib/widget.php';
require_once APP_DIR . '/modules/jrCore/lib/queue.php';

//-------------------------
// EVENT LISTENERS
//-------------------------

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
    // Switch to NEW temp value table from OLD temp table
    if (jrCore_db_table_exists('jrCore', 'temp') && jrCore_db_table_exists('jrCore', 'tempvalue')) {

        // We have not been migrated yet - insert values and drop old table
        $cnt = 0;
        $tbl = jrCore_db_table_name('jrCore', 'temp');
        while (true) {
            $req = "SELECT * FROM {$tbl} GROUP BY temp_module, temp_key ORDER BY temp_updated DESC LIMIT 100";
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

                // Cleanup
                $req = "DELETE FROM {$tbl} WHERE " . implode(' OR ', $_dl);
                jrCore_db_query($req);

            }
            else {
                break;
            }
        }
        if ($cnt && $cnt > 0) {
            jrCore_logger('INF', "migrated " . jrCore_number_format($cnt) . " unique temp keys to new format");
        }
        $req = "DROP TABLE {$tbl}";
        jrCore_db_query($req);
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
 * @param mixed $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return mixed
 */
function jrCore_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    // Update user location
    if (isset($_SESSION)) {
        $_SESSION['session_updated'] = time();
    }

    // Replace emoji
    $_data = jrCore_replace_emoji($_data);

    // Replace Page title is set within template
    if (!jrCore_get_flag('jrcore_page_title_set_in_template')) {
        // We are displaying a CACHED page (flag is NOT set)
        if ($ttl = strpos($_data, '- :__page_title:')) {
            list($title,) = explode(':', substr($_data, ($ttl + 16)), 2);
            $_data = preg_replace("~<title>.*</title>~i", "<title>{$title}</title>", $_data);
        }
    }

    // Replace media play keys
    return jrCore_media_set_play_key($_data);
}

/**
 * Reset profile caches after results
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return mixed
 */
function jrCore_results_sent_listener($_data, $_user, $_conf, $_args, $event)
{
    // Reset caches for any profile_id's that were changed during process
    jrCore_process_exit_delete_profile_cache();
    return $_data;
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
    if (jrCore_is_view_request() && !jrCore_is_ajax_request() && jrCore_can_be_queue_worker()) {

        // Set flag used to tell Activity Log to show [system] instead of [user_name]
        jrCore_set_flag('jrcore_logger_system_user_active', 1);

        // Run maintenance check
        jrCore_maintenance_check();

        // And are queue workers enabled?
        if (jrCore_queues_are_active()) {

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

            // Get modules that have registered Queue Workers ...
            // jrCore_register_queue_worker('Module', 'QueueName', 'WorkerFunction', int:Queue Entries to Process, int:Max Simultaneous Workers, int:Worker Queue Timeout, int(1-9):Worker Priority);
            if ($_tmp = jrCore_get_flag('jrcore_register_queue_worker')) {

                // And see if we have any queue entries
                if ($_qn = jrCore_get_ready_queues()) {

                    // We have queue entries to work
                    // Conversions and other queue-based work can take a long time to run
                    set_time_limit(0);

                    foreach ($_tmp as $priority => $_modules) {
                        foreach ($_modules as $mod => $_queue) {
                            foreach ($_queue as $qname => $qdat) {

                                // Only process entries for queue we actually have
                                if (!isset($_qn["{$mod}_{$qname}"])) {
                                    continue;
                                }

                                // Queue Function that is going to be run
                                $func = $qdat[0];
                                if (!function_exists($func)) {
                                    jrCore_logger('MAJ', "registered queue worker function: {$func} for module: {$mod} does not exist");
                                    continue;
                                }

                                // Maximum number of workers that can be running at one time on this queue
                                $maxw = (isset($qdat[2])) ? intval($qdat[2]) : 1;

                                // Worker Timeout (in seconds) - how long this worker can work a single queue entry
                                $tout = (isset($qdat[3]) && is_numeric($qdat[3])) ? intval($qdat[3]) : 3600;

                                // Can this process become a Queue Worker?
                                if ($wpid = jrCore_queue_worker_can_work($mod, $qname, $tout, $maxw)) {

                                    // Yes - how many Queue Entries can this worker work before exiting?
                                    // NOTE: queue_worker slot for this queue has increased by 1 now
                                    $rlws = false;
                                    $qcnt = intval($qdat[1]);
                                    if ($qcnt === 0) {
                                        $qcnt = 1000000; // "0" means "all" - set this to a high number here
                                    }
                                    if ($_qn["{$mod}_{$qname}"] <= $qcnt) {
                                        // No need to run more queue checks than we have queue entries
                                        $qcnt = intval($_qn["{$mod}_{$qname}"]);
                                    }
                                    while ($qcnt > 0) {

                                        // Are we still active?
                                        if (jrCore_queues_are_active()) {

                                            // We are under the max allowed workers for this queue
                                            // and have been assigned a worker queue slot - get a queue entry
                                            $_tmp = jrCore_queue_get($mod, $qname, $wpid, null, $tout, $maxw);
                                            if ($_tmp && isset($_tmp['queue_id'])) {

                                                // Pass the queue entry to the registered queue function
                                                $ret = $func($_tmp['queue_data']);

                                                // Our queue function can return:
                                                // 1) TRUE - everything is good, delete queue entry
                                                // 3) FALSE - an issue was encountered processing the queue
                                                // 2) # - indicates we should sleep the queue entry for # number of seconds - default is an additional 30 seconds for EACH retry
                                                // 4) EXIT - force exit of queue worker loop
                                                if ($ret === true) {

                                                    // We are done working - decrement queue depth
                                                    // NOTE: worker moves on to the next queue here here so we do not decrement worker count
                                                    jrCore_queue_decrement_queue_depth($mod, $qname);

                                                    // We successfully processed our queue entry - delete it
                                                    jrCore_queue_delete($_tmp['queue_id']);
                                                    $rlws = true;

                                                }
                                                elseif ($ret === 'EXIT') {

                                                    // Forced exit by worker - NO CHANGE to queue depth
                                                    jrCore_queue_release($_tmp['queue_id']);
                                                    jrCore_queue_release_worker_slot($mod, $qname);
                                                    break 4;

                                                }
                                                elseif ($ret === 'THROTTLED') {

                                                    // "THROTTLED" is a special return condition from the Mail worker that
                                                    // tells the core queue system to sleep the queue entry for an additional
                                                    // 60 seconds, but to NOT increment the queue_count
                                                    jrCore_queue_release($_tmp['queue_id'], 60, null, false);
                                                    $rlws = true;

                                                }
                                                else {

                                                    // If this queue has failed 10 times, we have a problem - delete an exit
                                                    if ($_tmp['queue_count'] >= 9) {

                                                        // We are done working - decrement queue depth
                                                        // NOTE: worker moves on to the next queue here so we do not decrement worker count
                                                        jrCore_queue_decrement_queue_depth($mod, $qname);

                                                        // Delete this queue entry
                                                        jrCore_queue_delete($_tmp['queue_id']);

                                                        // Let someone know
                                                        jrCore_logger('MAJ', "deleted queue_id {$_tmp['queue_id']} in {$mod}/{$qname} queue - failed 10 times", $_tmp);
                                                        $rlws = true;

                                                    }
                                                    else {
                                                        // Failed to work queue - set sleep for retry
                                                        if (jrCore_checktype($ret, 'number_nn')) {
                                                            $sec = (int) $ret;
                                                        }
                                                        else {
                                                            // Depending on the number of times this queue has
                                                            // been worked, we increment by 30 seconds each time
                                                            $sec = (($_tmp['queue_count'] + 1) * 30);
                                                        }
                                                        jrCore_queue_release($_tmp['queue_id'], $sec);
                                                        $rlws = true;
                                                    }

                                                }
                                                $qcnt--;

                                            }
                                            else {

                                                // We did NOT get a queue - release slot and move to next queue
                                                jrCore_queue_release_worker_slot($mod, $qname);
                                                $rlws = false;
                                                break;

                                            }
                                        }
                                        else {

                                            // Queues are NOT active - do not start another worker - release slot and exit
                                            jrCore_queue_release_worker_slot($mod, $qname);
                                            break 4;

                                        }
                                    }
                                    if ($rlws) {
                                        // We worked a queue and this is our last run - Release worker slot
                                        jrCore_queue_release_worker_slot($mod, $qname);
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
    // Cleanup Recycle Bin
    if (isset($_conf['jrCore_recycle_bin_expire']) && jrCore_checktype($_conf['jrCore_recycle_bin_expire'], 'number_nz')) {

        // Delete expired recycle bin
        $old = ($_conf['jrCore_recycle_bin_expire'] * 86400);
        $tbl = jrCore_db_table_name('jrCore', 'recycle');
        $req = "SELECT r_id, r_module AS module, r_profile_id AS profile_id, r_item_id AS item_id, r_data AS data FROM {$tbl} WHERE r_time < (UNIX_TIMESTAMP() - {$old})";
        $_rt = jrCore_db_query($req, 'r_id');
        if ($_rt && is_array($_rt)) {

            // Delete RB entries
            $req = "DELETE FROM {$tbl} WHERE r_id IN(" . implode(',', array_keys($_rt)) . ')';
            jrCore_db_query($req);

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
 * 1) Full page caching for logged out users (excluding bots)
 * 2) Bring in Admin specific JS for admin users
 * 3) Reset form sessions on cancel for forms that request it
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_session_started_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods, $_post;
    if (!jrUser_is_logged_in()) {
        if (isset($_conf['jrCore_full_page']) && $_conf['jrCore_full_page'] == 'on' && isset($_post['module'])) {
            // We only do full page caching for skin templates, indexes and profiles
            if (!isset($_mods["{$_post['module']}"])) {
                if ($out = jrCore_is_cached('jrCore', jrCore_get_full_page_cache_key(), false, true)) {
                    jrCore_send_response_and_detach($out);
                    $_SERVER['jr_is_ajax_request']            = 1;  // Set this so we don't do a Queue Check on exit here
                    $_SERVER['jr_is_full_page_cache_request'] = 1;  // For listeners to know we are dealing with a full page cache request
                    jrCore_trigger_event('jrCore', 'process_exit', $_post);
                    jrCore_trigger_event('jrCore', 'process_done', $_post);
                    exit;
                }
            }
        }
    }

    // Add in admin javascript
    elseif (jrUser_is_admin()) {
        $key = "jrCore_{$_conf['jrCore_active_skin']}_javascript_version";
        $sum = (isset($_conf[$key])) ? $_conf[$key] : false;
        $cdr = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);
        $prt = jrCore_get_server_protocol();
        if ($prt && $prt === 'https' || $_conf['jrUser_force_ssl'] == 'on') {
            $ver = "S{$sum}-admin.js";
        }
        else {
            $ver = "{$sum}-admin.js";
        }
        if (is_file("{$cdr}/{$ver}")) {
            $_js = array('source' => "{$_conf['jrCore_base_url']}/data/cache/{$_conf['jrCore_active_skin']}/{$ver}");
            jrCore_create_page_element('javascript_href', $_js);
        }
    }

    // Reset form session
    if (isset($_post['_rfs']) && jrCore_checktype($_post['_rfs'], 'md5')) {
        if ($_form = jrCore_form_get_session($_post['_rfs'])) {
            if (isset($_form['form_user_id']) && $_form['form_user_id'] == $_data['_user_id']) {
                jrCore_form_delete_session($_post['_rfs']);
            }
        }
        if (isset($_SESSION['reset_form_referrer_url'])) {
            unset($_SESSION['reset_form_referrer_url']);
        }
        $url = jrCore_strip_url_params(jrCore_get_current_url(), array('_rfs'));
        jrCore_location($url);
    }

    return $_data;
}

/**
 * Use full page caching for bots
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_parse_url_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods, $_post;
    if (!isset($_COOKIE) && isset($_conf['jrCore_full_page']) && $_conf['jrCore_full_page'] == 'on') {
        // We only do full page caching for skin templates, indexes and profiles
        if (!isset($_mods["{$_post['module']}"])) {
            if ($out = jrCore_is_cached('jrCore', jrCore_get_full_page_cache_key(), false, true)) {
                jrCore_send_response_and_detach($out);
                $_SERVER['jr_is_ajax_request']            = 1;  // Set this so we don't do a Queue Check on exit here
                $_SERVER['jr_is_full_page_cache_request'] = 1;  // For listeners to know we are dealing with a full page cache request
                jrCore_trigger_event('jrCore', 'process_exit', $_post);
                jrCore_trigger_event('jrCore', 'process_done', $_post);
                exit;
            }
        }
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
