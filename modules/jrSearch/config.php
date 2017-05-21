<?php
/**
 * Jamroom Search module
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
 * config
 */
function jrSearch_config()
{
    // Additional Search Fields
    $_tmp = array(
        'name'     => 'search_fields',
        'type'     => 'textarea',
        'default'  => '',
        'validate' => 'searchable_field',
        'label'    => 'additional search fields',
        'help'     => 'If you would like to have additional DataStore fields available for search, enter the DataStore field name, one per line.<br><br><strong>Example:</strong><br>If you have created a custom User Profiles field via the Form Designer called &quot;profile_location&quot; you would enter <strong>profile_location</strong> on a line by itself to enable that field to be searched.<br><br>You may also include an optional <strong>weight</strong> value that will make the search field have more impact on the search results:<br><br>profile_location,10<br><br>Would set the weight for the profile_location field to 10, which means a search match on that field would be 10 times more important than the default value of 1.',
        'section'  => 'search options',
        'order'    => 1
    );
    jrCore_register_setting('jrSearch', $_tmp);

    // Search for partial matches
    $_tmp = array(
        'name'     => 'partial',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'include partial matches',
        'help'     => 'If a search query returns no exact results, do you want to run a second query to find sub string matches?',
        'section'  => 'search options',
        'order'    => 2
    );
    jrCore_register_setting('jrSearch', $_tmp);

    // Optimize search queries
    $_tmp = array(
        'name'     => 'optimize',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'optimize queries',
        'help'     => 'This option enables a query optimizer that can speed up some search queries.  It is recommended to leave this enabled unless you notice inconsistent result pagination.',
        'section'  => 'search options',
        'order'    => 3
    );
    jrCore_register_setting('jrSearch', $_tmp);

    // Log no results
    $_tmp = array(
        'name'     => 'log_no_result',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'log no results',
        'help'     => 'If this option is checked, and a search term returns no results, the search term will be logged to the Activity Log',
        'section'  => 'search options',
        'order'    => 4
    );
    jrCore_register_setting('jrSearch', $_tmp);

    // Search method
    $_opt = array(
        'both'    => 'Default',
        'boolean' => 'Keyword Only',
        'matural' => 'Natural Language Only'
    );
    $_tmp = array(
        'name'     => 'method',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'both',
        'validate' => 'not_empty',
        'label'    => 'search method',
        'help'     => 'The Search Method controls how a search term will be processed:<br><br><b>Default:</b> If the search query is 5 words or longer, and contains no Keyword operators (AND, OR, NOT) it will be treated as a Natural Language search, otherwise it will run as a Keyword search.<br><br><b>Keyword Only:</b> The search term will be treated as separate keywords (which can be manipulated using AND, OR and NOT).<br><br><b>Natural Language Only:</b> The search term will be treated as a question or phrase which may return more relevant results.',
        'section'  => 'search options',
        'order'    => 5
    );
    jrCore_register_setting('jrSearch', $_tmp);

    // Search index limit
    $_tmp = array(
        'name'     => 'index_limit',
        'type'     => 'text',
        'default'  => '4',
        'validate' => 'number_nz',
        'label'    => 'index result count',
        'help'     => 'How many results from each module that has matching results should be shown on the results page?',
        'section'  => 'display options',
        'order'    => 10
    );
    jrCore_register_setting('jrSearch', $_tmp);

    // Search index limit
    $_tmp = array(
        'name'     => 'result_limit',
        'type'     => 'text',
        'default'  => '12',
        'validate' => 'number_nz',
        'label'    => 'module result count',
        'help'     => 'When viewing the search results for an individual module, how many results should be shown on each page?',
        'section'  => 'display options',
        'order'    => 11
    );
    jrCore_register_setting('jrSearch', $_tmp);

    // Unused setting
    jrCore_delete_setting('jrSearch', 'fulltext');

    return true;
}

/**
 * Make sure full text indexing is not in progress
 * @param $_post array Posted data
 * @param $_user array Viewing user info
 * @param $_conf array Global Config
 * @return bool
 */
function jrSearch_config_display($_post, $_user, $_conf)
{
    // See if we have an index going on
    $_tm = jrCore_get_active_queue_info();
    if (isset($_tm['jrSearch']['search_index'])) {
        $button = jrCore_page_button('refresh', 'refresh', 'location.reload();');
        jrCore_set_form_notice('error', 'Full Text Indexing is currently in progress.<br>Avoid making additional changes until you press Refresh and this message no longer appears.<br><br>' . $button, false);
    }
    return true;
}
