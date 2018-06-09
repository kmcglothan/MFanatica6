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
 * @package View Functions
 * @copyright 2014 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrCore_show_activity_log
 * @param $_post array Posted parameters
 * @param $_user array Viewing User
 * @param $_conf array Global Config
 * @param $from string "dashboard" or empty
 * @return null
 */
function jrCore_show_activity_log($_post, $_user, $_conf, $from = '')
{
    jrCore_master_log_tabs('activity');
    $url = jrCore_get_module_url('jrCore');
    // construct our query
    $tbl = jrCore_db_table_name('jrCore', 'log');
    $tbd = jrCore_db_table_name('jrCore', 'log_debug');
    $req = "SELECT l.*, d.log_log_id FROM {$tbl} l LEFT JOIN {$tbd} d ON d.log_log_id = l.log_id ";
    if (isset($_post['eo']) && $_post['eo'] == '1') {
        $req .= "WHERE log_priority != 'inf' ";
        $mod = 'AND';
        $num = null;
    }
    else {
        $mod = 'WHERE';
        $num = jrCore_db_number_rows('jrCore', 'log');
    }
    $_ex = false;
    $add = '';
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_post['search_string'] = trim(urldecode($_post['search_string']));
        $str                    = jrCore_db_escape($_post['search_string']);
        $req                    .= "{$mod} (l.log_text LIKE '%{$str}%' OR l.log_ip LIKE '%{$str}%' OR l.log_priority LIKE '%{$str}%') ";
        $_ex                    = array('search_string' => $_post['search_string']);
        $add                    = '/search_string=' . urlencode($_post['search_string']);
        $num                    = false;
    }
    $req .= 'ORDER BY l.log_id DESC';

    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC', $num);

    $bu = "{$_conf['jrCore_base_url']}/{$url}/activity_log";
    if ($from && $from == 'dashboard') {
        $bu = "{$_conf['jrCore_base_url']}/{$url}/dashboard/activity";
    }

    // start our html output
    $eo = '';
    if (isset($_post['eo']) && $_post['eo'] == '1') {
        $buttons = jrCore_page_button('eo', 'all entries', "jrCore_window_location('{$bu}')");
        $eo      = '/eo=1';
    }
    else {
        $buttons = jrCore_page_button('eo', 'priority only', "jrCore_window_location('{$bu}/eo=1')");
    }
    $buttons .= jrCore_page_button('download', 'download', "jrCore_confirm('Download the activity log?', 'Please be patient - the CSV file could be large and take a minute to create', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/activity_log_download')})");
    if (jrUser_is_master()) {
        $buttons .= jrCore_page_button('delete', 'empty', "jrCore_confirm('Delete the Activity Log?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/activity_log_delete_all') })");
    }
    jrCore_page_banner('activity log', $buttons);
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$bu}{$eo}{$add}");

    $dat = array();
    if (jrUser_is_master()) {
        $dat[1]['title'] = '&nbsp;';
        $dat[1]['width'] = '2%;';
        $dat[2]['title'] = 'date';
        $dat[2]['width'] = '4%;';
    }
    else {
        $dat[2]['title'] = 'date';
        $dat[2]['width'] = '6%;';
    }
    $dat[3]['title'] = 'IP';
    $dat[3]['width'] = '5%;';
    $dat[4]['title'] = 'text';
    $dat[5]['title'] = '&nbsp;';
    $dat[5]['width'] = '2%;';
    jrCore_page_table_header($dat);
    unset($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        // LOG LINE
        $murl = jrCore_get_module_url('jrCore');
        $curl = jrCore_get_module_url('jrUser');
        foreach ($_rt['_items'] as $k => $_log) {

            $dat = array();
            if (jrUser_is_master()) {
                $dat[1]['title'] = jrCore_page_button("d{$_log['log_id']}", 'X', "jrCore_delete_activity_log({$_log['log_id']})");
            }
            $dat[2]['title'] = jrCore_format_time($_log['log_created']);
            $dat[2]['class'] = 'center nowrap';
            $dat[3]['title'] = "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$curl}/whois/{$_log['log_ip']}','{$_log['log_ip']}',900,600,'yes');\">" . $_log['log_ip'] . '</a>';
            if (isset($_post['search_string']{0})) {
                $dat[4]['title'] = jrCore_hilight_string($_log['log_text'], $_post['search_string']);
            }
            else {
                $dat[4]['title'] = $_log['log_text'];
            }
            $dat[4]['class'] = "log-inf log-{$_log['log_priority']} word-break";
            if (isset($_log['log_log_id']) && $_log['log_log_id'] > 0) {
                $dat[5]['title'] = jrCore_page_button("r{$k}", '?', "popwin('{$_conf['jrCore_base_url']}/{$murl}/log_debug/{$_log['log_id']}','debug',900,600,'yes');");
                $dat[5]['class'] = "log-inf log-{$_log['log_priority']}";
            }
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt, $_ex);
    }
    else {
        $dat = array();
        if (!empty($_post['search_string'])) {
            $dat[1]['title'] = '<p>There were no Activity Logs found to match your search criteria</p>';
        }
        else {
            $dat[1]['title'] = '<p>There does not appear to be any Activity Logs</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return true;
}

/**
 * Show the Skin Style Editor
 * @param $skin string Skin name we are editing
 * @param $_post array Posted values
 * @param $_user array User array
 * @param $_conf array Global Config array
 * @return mixed
 */
function jrCore_show_skin_style($skin, $_post, $_user, $_conf)
{
    global $_mods;
    jrCore_page_skin_tabs($skin, 'style');

    // What are our available tab options?
    $_op = array(
        'simple'   => 'Color and Font',
        'padding'  => 'Padding and Margin',
        'advanced' => 'Advanced',
        'extra'    => 'Untagged'
    );

    // Do we have custom CSS for this skin?
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
    $_cc = jrCore_db_query($req, 'SINGLE');
    if ($_cc && is_array($_cc) && isset($_cc['skin_custom_css']) && strlen($_cc['skin_custom_css']) > 2) {
        $_op['changes'] = 'View Changes';
    }
    else {
        // Our changes tab will not show after a RESET so redirect
        if (isset($_post['section']) && $_post['section'] == 'changes') {
            $url = jrCore_get_current_url();
            $url = jrCore_strip_url_params($url, array('section'));
            jrCore_location($url);
        }
    }

    // What CSS Rules are aligned with each option?
    // "advanced" is the default if a CSS rule is NOT defined here
    // Define the CSS params that are SIMPLE CSS params
    $_or = array(
        'simple'  => array(
            'background-color' => 1,
            'color'            => 1,
            'font-family'      => 1,
            'font-size'        => 1,
            'font-weight'      => 1,
            'text-transform'   => 1
        ),
        'padding' => array(
            'padding'        => 1,
            'padding-top'    => 1,
            'padding-right'  => 1,
            'padding-bottom' => 1,
            'padding-left'   => 1,
            'margin'         => 1,
            'margin-top'     => 1,
            'margin-right'   => 1,
            'margin-bottom'  => 1,
            'margin-left'    => 1
        ),
    );

    // Default to simple section
    if (!isset($_post['section']) || strlen($_post['section']) === 0) {
        $section = 'advanced';
    }
    else {
        $section = $_post['section'];
    }

    // Get files
    $_tm   = false;
    $found = false;
    $ffile = array();
    if ($section != 'changes') {
        $_files = glob(APP_DIR . "/skins/{$skin}/css/*.css");
        if (!$_files || !is_array($_files)) {
            jrCore_notice_page('error', 'There do not appear to be any CSS files for this skin!');
            return false;
        }
        $_md = jrCore_skin_meta_data($skin);
        $ttl = (isset($_md['title'])) ? $_md['title'] : $skin;
        $_fl = array(
            $ttl => array()
        );
        foreach ($_files as $full_file) {
            $tmp = file_get_contents($full_file);
            if ($section == 'extra' || strpos($tmp, '@title')) {
                $nam             = basename($full_file);
                $_fl[$ttl][$nam] = $nam;
            }
        }

        // We also need to add in any module CSS files so they can be tweaked
        $_tm = jrCore_get_registered_module_features('jrCore', 'css');
        if ($_tm) {
            foreach ($_tm as $mod => $_v) {
                foreach ($_v as $full_file => $ignore) {
                    if (!strpos($full_file, '/')) {
                        $full_file = APP_DIR . "/modules/{$mod}/css/{$full_file}";
                    }
                    if (!is_file($full_file)) {
                        // file no longer exists
                        continue;
                    }
                    if ($section == 'extra' || strpos(file_get_contents($full_file), '@title')) {
                        $nam             = basename($full_file);
                        $ttl             = $_mods[$mod]['module_name'];
                        $_fl[$ttl][$nam] = $nam;
                        $_files[]        = $full_file;
                    }
                }
            }
        }

        // See if we were given a selector
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            foreach ($_files as $full_file) {
                $tmp = file_get_contents($full_file);
                if (strpos($tmp, '@title') || $section != 'simple') {
                    $_cs = jrCore_parse_css_file($full_file, $section);
                    if ($_cs && is_array($_cs) && count($_cs) > 0) {
                        foreach ($_cs as $rule => $opts) {
                            if ($rule == $_post['search_string'] || $rule == ".{$_post['search_string']}" || $rule == "#{$_post['search_string']}" || strpos($rule, "{$_post['search_string']} ") === 0 || strpos($rule, ".{$_post['search_string']} ") === 0 || strpos($rule, "#{$_post['search_string']} ") === 0 || (isset($opts['title']) && stripos(' ' . $opts['title'], $_post['search_string'])) || strpos(json_encode($opts), $_post['search_string'])) {
                                if (!$found) {
                                    $found = array();
                                }
                                $found[$rule] = $opts;
                                $ffile[$rule] = basename($full_file);
                            }
                        }
                    }
                }
            }
            if ($found && is_array($found)) {
                $_op['search']    = 'Search Results';
                $_post['section'] = 'search';
                $section          = 'search';
            }
        }
    }

    $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/style/skin={$_post['skin']}";
    if (isset($_post['file']) && strlen($_post['file']) > 2) {
        $url .= '/file=' . jrCore_entity_string($_post['file']);
    }
    $_tb = array();
    foreach ($_op as $tab => $title) {
        $_tb[$tab] = array(
            'label' => $title,
            'url'   => "{$url}/section={$tab}"
        );
    }

    $_tb[$section]['active'] = true;
    jrCore_page_tab_bar($_tb);

    $url = jrCore_get_module_url('jrCore');

    // Do we have custom CSS for this skin?
    if ($_cc && is_array($_cc) && isset($_cc['skin_custom_css']) && strlen($_cc['skin_custom_css']) > 2) {
        $btn = jrCore_page_button('reset', 'Reset Skin Style', "jrCore_confirm('Reset the CSS for this Skin?', 'This will revert this skin to the default CSS', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_reset/skin={$_post['skin']}') })");
    }
    else {
        $btn = jrCore_page_button('reset', 'Reset Skin Style', 'disabled');
    }

    $btn   .= '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$url}/skin_admin/style/skin=' + $(this).val() + '/section=" . $section . "')\">";
    $_tmpm = jrCore_get_skins();
    foreach ($_tmpm as $skin_dir => $_skin) {
        $_mta = jrCore_skin_meta_data($skin_dir);
        $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
        if ($skin_dir == $_post['skin']) {
            $btn .= '<option value="' . $_post['skin'] . '" selected> ' . $name . "</option>\n";
        }
        else {
            $btn .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
        }
    }
    $btn .= '</select>';

    jrCore_page_banner('Style Editor', $btn);
    jrCore_get_form_notice();

    if ($section == 'changes') {

        if ($_cc && is_array($_cc) && isset($_cc['skin_custom_css']) && strlen($_cc['skin_custom_css']) > 2) {
            jrCore_page_notice('success', 'Based on your changes, the following custom CSS is overriding this skin\'s default CSS:');
            $_tmp = json_decode($_cc['skin_custom_css'], true);
            jrCore_page_custom('<div class="item fixed-width p20">' . trim(jrCore_format_custom_css($_tmp, true)) . '</div>');
        }
        else {
            jrCore_page_notice('error', 'No Custom CSS has been created for this skin');
        }

    }
    else {

        $ssubm = false;
        if (isset($_post['search_string']) && !$found) {
            $ssubm = true;
        }

        // See if we have been given a file to edit - if not, use first in list
        if (!isset($_post['file']{0})) {
            $_post['file'] = reset($_fl);
            $_post['file'] = basename(reset($_post['file']));
        }

        $full_file = APP_DIR . "/skins/{$skin}/css/{$_post['file']}";
        if (!is_file($full_file) && $_tm) {
            // See if this is a module CSS file...
            foreach ($_tm as $mod => $_v) {
                foreach ($_v as $ff => $ignore) {
                    if ($ff == $_post['file']) {
                        $full_file = APP_DIR . "/modules/{$mod}/css/{$ff}";
                        break 2;
                    }
                }
            }
        }
        if (!is_file($full_file)) {
            jrCore_page_notice('error', 'Unable to open CSS file - please try again');
            jrCore_page_set_no_header_or_footer();
            return jrCore_page_display(true);
        }

        if (!$found) {
            $_tmp = jrCore_parse_css_file($full_file, $section);
        }
        else {
            $_tmp = $found;
        }
        if ($section != 'advanced' && $_tmp && is_array($_tmp)) {
            foreach ($_tmp as $name => $_inf) {
                $frl = false;
                if (isset($_inf['rules']) && is_array($_inf['rules'])) {
                    foreach ($_inf['rules'] as $rule => $val) {
                        if (isset($_or[$section]) && !isset($_or[$section][$rule])) {
                            continue;
                        }
                        $frl = true;
                    }
                }
                if (!$frl) {
                    unset($_tmp[$name]);
                }
            }
        }

        // Now we have the "base" CSS - we next need to load in the customizations
        // from the database if they have any
        $tbl = jrCore_db_table_name('jrCore', 'skin');
        $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($skin) . "'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        $_cr = array();
        if ($_rt && is_array($_rt) && strlen($_rt['skin_custom_css']) > 3) {
            $_new = json_decode($_rt['skin_custom_css'], true);
            $_rep = array('#', '"', "'", 'px', '%', 'em');
            if ($_new && is_array($_new)) {
                foreach ($_new as $cname => $_cinf) {
                    if (isset($_tmp[$cname])) {
                        // See what has changed
                        foreach ($_cinf as $r => $t) {
                            if (isset($_tmp[$cname]['rules'][$r])) {
                                $one = trim(str_replace($_rep, '', $_tmp[$cname]['rules'][$r]));
                                $two = trim(str_replace($_rep, '', $t));
                                if ($one != $two) {
                                    if (!isset($_cr[$cname])) {
                                        $_cr[$cname] = array();
                                    }
                                    $_cr[$cname][$r] = 1;
                                }
                            }
                        }
                        $_tmp[$cname]['rules'] = array_merge($_tmp[$cname]['rules'], $_cinf);
                    }
                }
            }
        }

        $subm = true;
        if (!$_tmp || !is_array($_tmp) || count($_tmp) === 0) {
            $subm = false;
        }
        elseif (!$ssubm) {

            // Form init
            $_fld = array(
                'submit_value' => 'save changes',
                'action'       => "skin_admin_save/style/skin={$skin}/section={$section}"
            );
            jrCore_form_create($_fld);

            $_fld = array(
                'name'  => 'file',
                'type'  => 'hidden',
                'value' => $_post['file']
            );
            jrCore_form_field_create($_fld);

            if ($found && is_array($found)) {
                $tkey = md5($_post['search_string']);
                $_ttt = array(
                    'found'         => $found,
                    'search_string' => $_post['search_string']
                );
                jrCore_set_temp_value('jrCore', $tkey, $_ttt);
                $_fld = array(
                    'name'  => 'search_key',
                    'type'  => 'hidden',
                    'value' => $tkey
                );
                jrCore_form_field_create($_fld);
            }

        }

        // Style Jumper...
        if (isset($_fl) && is_array($_fl) && count($_fl) > 1) {
            if ($section != 'search') {
                // Make sure $_fl contains our file...
                $fnf = false;
                foreach ($_fl as $m => $_f) {
                    if (isset($_f["{$_post['file']}"])) {
                        $fnf = true;
                        break;
                    }
                }
                if (!$fnf) {
                    $_fl["{$_post['file']}"] = $_post['file'];
                }
                $_fld = array(
                    'name'     => 'file',
                    'label'    => 'style section',
                    'type'     => 'select',
                    'options'  => $_fl,
                    'value'    => $_post['file'],
                    'onchange' => "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/style/skin={$skin}/file='+ $(this).val() + '/section={$section}')"
                );
                jrCore_form_field_create($_fld);
            }
            $val = null;
            if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
                if ($found) {
                    $val = jrCore_entity_string($_post['search_string']);
                    jrCore_set_form_notice('success', "Showing Selector, Rule and Value matches for: <strong>{$val}</strong>", false);
                    jrCore_get_form_notice();
                }
                else {
                    $_tmp = array();
                }
            }
            jrCore_page_search('selector search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/style/skin={$_post['skin']}/file={$_post['file']}/section={$section}", null, false);
            jrCore_page_divider();
        }

        if (!$subm || $ssubm) {
            if ($ssubm) {
                jrCore_set_form_notice('error', 'There were no CSS selectors found to match your search');
            }
            else {
                jrCore_set_form_notice('error', "There are no CSS Rules found in the {$_op[$section]} section for this file");
            }
            jrCore_get_form_notice();
        }

        $color_opts = '<option value="transparent">transparent</option>';
        // Generate web safe colors
        $cs = array('00', '33', '66', '99', 'CC', 'FF');
        for ($i = 0; $i < 6; $i++) {
            for ($j = 0; $j < 6; $j++) {
                for ($k = 0; $k < 6; $k++) {
                    $c          = $cs[$i] . $cs[$j] . $cs[$k];
                    $color_opts .= "<option value=\"{$c}\">#{$c}</option>\n";
                }
            }
        }

        // Padding/margins
        $_pixels = array(
            '0 auto' => '0 auto',
            'auto'   => 'auto',
            '0'      => '0',
        );
        foreach (range(0, 50) as $pix) {
            $_pixels["{$pix}px"] = "{$pix}px";
        }

        // Width/Height
        $_width_perc = array(
            'auto' => 'auto',
            '0'    => '0'
        );
        foreach (range(1, 100) as $pix) {
            $_width_perc["{$pix}%"] = "{$pix}%";
        }

        $_width_pix = array(
            'auto' => 'auto',
            '0'    => '0'
        );
        foreach (range(1, 600, 1) as $pix) {
            $_width_pix["{$pix}px"] = "{$pix}px";
        }

        $_css_opts = array();

        // Our fonts
        $_css_opts['font-family'] = array(
            'Arial'                => 'Arial',
            'Arial Black'          => 'Arial Black',
            'Courier New'          => 'Courier New',
            'Georgia'              => 'Georgia',
            'Impact'               => 'Impact',
            'monospace'            => 'monospace',
            'Times New Roman'      => 'Times New Roman',
            'Trebuchet MS'         => 'Trebuchet MS',
            'Verdana'              => 'Verdana',
            'MS Sans Serif,Geneva' => 'sans-serif'
        );

        // Our sizes
        $_css_opts['font-size'] = array();
        foreach (range(8, 96) as $pix) {
            $_css_opts['font-size']["{$pix}px"] = "{$pix}px";
        }
        foreach (range(.1, 1, .1) as $pix) {
            $_css_opts['font-size']["{$pix}em"] = "{$pix}em";
        }

        // Weights
        $_css_opts['font-weight'] = array(
            'normal'  => 'normal',
            'bold'    => 'bold',
            'bolder'  => 'bolder',
            'lighter' => 'lighter',
            'inherit' => 'inherit'
        );

        // Style
        $_css_opts['font-style'] = array(
            'normal' => 'normal',
            'italic' => 'italic'
        );

        // Variant
        $_css_opts['font-variant'] = array(
            'normal'     => 'normal',
            'small-caps' => 'small-caps'
        );

        // Text-Transform
        $_css_opts['text-transform'] = array(
            'none'       => 'none',
            'capitalize' => 'capitalize',
            'uppercase'  => 'uppercase',
            'lowercase'  => 'lowercase',
            'inherit'    => 'inherit'
        );

        // Text-Align
        $_css_opts['text-align'] = array(
            'left'    => 'left',
            'right'   => 'right',
            'center'  => 'center',
            'justify' => 'justify',
            'inherit' => 'inherit'
        );

        // Vertical-Align
        $_css_opts['vertical-align'] = array(
            'baseline'    => 'baseline',
            'sub'         => 'sub',
            'super'       => 'super',
            'text-top'    => 'text-top',
            'text-bottom' => 'text-bottom',
            'top'         => 'top',
            'middle'      => 'middle',
            'bottom'      => 'bottom'
        );

        // Text-Decoration
        $_css_opts['text-decoration'] = array(
            'none'         => 'none',
            'underline'    => 'underline',
            'overline'     => 'overline',
            'line-through' => 'line-through',
            'blink'        => 'blink',
            'inherit'      => 'inherit'
        );

        // Opacity
        $_css_opts['opacity'] = array(
            '0.05' => '0.05',
            '0.1'  => '0.1',
            '0.15' => '0.15',
            '0.2'  => '0.2',
            '0.25' => '0.25',
            '0.3'  => '0.3',
            '0.35' => '0.35',
            '0.4'  => '0.4',
            '0.45' => '0.45',
            '0.5'  => '0.5',
            '0.55' => '0.55',
            '0.6'  => '0.6',
            '0.65' => '0.65',
            '0.7'  => '0.7',
            '0.75' => '0.75',
            '0.8'  => '0.8',
            '0.85' => '0.85',
            '0.9'  => '0.9',
            '0.95' => '0.95',
            '1.0'  => '1.0'
        );

        // Float
        $_css_opts['float'] = array(
            'left'  => 'left',
            'right' => 'right',
            'none'  => 'none'
        );

        // Display
        $_css_opts['display'] = array(
            'none'         => 'none',
            'block'        => 'block',
            'inline'       => 'inline',
            'inline-block' => 'inline-block',
            'table'        => 'table',
            'table-cell'   => 'table-cell',
            'table-row'    => 'table-row'
        );

        // $_tmp will now contain what we are editing
        if (isset($_tmp) && is_array($_tmp)) {

            $r_id = 0;
            $key  = false;
            foreach ($_tmp as $name => $_inf) {

                if ($found && is_array($found) && !isset($found[$name])) {
                    continue;
                }

                // Skip .row and .col[1-12]
                if (strpos(' ' . $name, '.row ') || strpos(' ' . $name, '.col')) {
                    continue;
                }

                // Process each rule...
                $_out = array();
                if (isset($_inf['rules']) && is_array($_inf['rules'])) {

                    foreach ($_inf['rules'] as $rule => $val) {

                        // Check for multiple value rules..
                        if (substr_count(strtolower($val), 'px') > 1) {
                            continue;
                        }

                        $val = str_replace(array('"', "'"), '', $val);

                        // Pass this in as a hidden form field so we can line them back up on submission
                        $key = 'jrse' . ++$r_id;
                        if (stripos($val, '!important')) {
                            // We don't deal with !important here
                            $val = trim(str_ireplace('!important', '', $val));
                            $hid = '<input type="hidden" name="' . $key . '_s" value="' . $name . '~' . $rule . '"><input type="hidden" name="' . $key . '_add_important" value="on">';
                        }
                        else {
                            $hid = '<input type="hidden" name="' . $key . '_s" value="' . $name . '~' . $rule . '">';
                        }

                        if ($section != 'advanced' && isset($_or[$section]) && !isset($_or[$section][$rule])) {
                            continue;
                        }

                        // Our tag is used to let the user know what they are changing
                        $tag = $rule;

                        // See what we are doing
                        switch ($rule) {

                            //------------------------
                            // other
                            //------------------------
                            case 'opacity':
                            case 'float':
                            case 'vertical-align':
                            case 'display':
                                $opts = array();
                                foreach ($_css_opts[$rule] as $fcss => $fname) {
                                    if (isset($fcss) && $fcss == $val) {
                                        $opts[] = '<option selected value="' . $fcss . '">' . $fname . '</option>';
                                    }
                                    else {
                                        $opts[] = '<option value="' . $fcss . '">' . $fname . '</option>';
                                    }
                                }
                                $_out[] = $hid . '<p class="style-label">' . $rule . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                                break;

                            //------------------------
                            // background-color
                            //------------------------
                            /** @noinspection PhpMissingBreakStatementInspection */
                            case 'color':
                                $tag = 'font-color';
                            // Note: fall through is on purpose
                            case 'border-color':
                            case 'border-top-color':
                            case 'border-right-color':
                            case 'border-bottom-color':
                            case 'border-left-color':
                            case 'background-color':
                                if (stripos(' ' . $val, 'rgb')) {
                                    continue 2;
                                }
                                // Show color selector
                                if ($val == 'transparent') {
                                    $color_opts .= "<option value=\"" . str_replace('#', '', $val) . "\" selected>{$val}</option>";
                                }
                                else {
                                    $color_opts .= "<option value=\"" . strtoupper(str_replace('#', '', $val)) . "\" selected>{$val}</option>";
                                }
                                if (isset($_cr[$name][$rule])) {
                                    $tag = '<i>' . $tag . '</i>';
                                }
                                $_out[] = $hid . '<p class="style-label">' . $tag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . $color_opts . '</select>';
                                $_tmp   = jrCore_get_flag('style_color_picker');
                                if (!$_tmp) {
                                    $_tmp = array();
                                }
                                $_tmp[] = array('$(\'#' . $key . '\').colourPicker();');
                                jrCore_set_flag('style_color_picker', $_tmp);
                                break;

                            //------------------------
                            // fonts
                            //------------------------
                            /** @noinspection PhpMissingBreakStatementInspection */
                            case 'font-family':
                                // Our "current" selection could be a compound font family - i.e.
                                // Open Sans,Tahoma,sans-serif
                                // in this case we need to make sure it is a choice in our $_css_opts
                                if (strpos($val, ',')) {
                                    $_css_opts['font-family'][$val] = $val;
                                }
                            // Note: fall through is on purpose
                            case 'font-size':
                            case 'font-weight':
                            case 'font-style':
                            case 'font-variant':
                            case 'text-transform':
                            case 'text-align':
                            case 'text-decoration':
                                $opts = array();
                                foreach ($_css_opts[$rule] as $fcss => $fname) {
                                    switch ($rule) {
                                        case 'font-family':
                                            $style = ' style="font-family:' . $fcss . '"';
                                            break;
                                        default:
                                            $style = '';
                                            break;
                                    }
                                    if (isset($fcss) && $fcss == $val) {
                                        $opts[] = '<option value="' . $fcss . '" ' . $style . ' selected>' . $fname . '</option>';
                                    }
                                    else {
                                        $opts[] = '<option value="' . $fcss . '" ' . $style . '>' . $fname . '</option>';
                                    }
                                }
                                // Show font family select
                                $rtag = $rule;
                                if (isset($_cr[$name][$rule])) {
                                    $rtag = '<i>' . $rule . '</i>';
                                }
                                $_out[] = "\n" . $hid . '<p class="style-label">' . $rtag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                                break;

                            //------------------------
                            // border-style
                            //------------------------
                            case 'border-style':
                            case 'border-top-style':
                            case 'border-right-style':
                            case 'border-bottom-style':
                            case 'border-left-style':
                                $opts = array();
                                $_brd = array('none', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset');
                                foreach ($_brd as $v) {
                                    if (isset($v) && $v == $val) {
                                        $opts[] = '<option selected value="' . $v . '">' . $v . '</option>';
                                    }
                                    else {
                                        $opts[] = '<option value="' . $v . '">' . $v . '</option>';
                                    }
                                }
                                // Show select
                                $rtag = $rule;
                                if (isset($_cr[$name][$rule])) {
                                    $rtag = '<i>' . $rule . '</i>';
                                }
                                $_out[] = $hid . '<p class="style-label">' . $rtag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                                break;

                            //------------------------
                            // padding/margin/border
                            //------------------------
                            case 'border-width':
                            case 'border-top-width':
                            case 'border-right-width':
                            case 'border-bottom-width':
                            case 'border-left-width':
                            case 'border-radius':
                            case 'border-top-left-radius':
                            case 'border-top-right-radius':
                            case 'border-bottom-left-radius':
                            case 'border-bottom-right-radius':
                            case 'padding':
                            case 'padding-top':
                            case 'padding-bottom':
                            case 'padding-left':
                            case 'padding-right':
                            case 'margin':
                            case 'margin-top':
                            case 'margin-bottom':
                            case 'margin-left':
                            case 'margin-right':
                            case 'top':
                            case 'right':
                            case 'left':
                            case 'bottom':
                            case 'line-height':
                                // See if we need to INCREASE our size-array
                                if (!isset($_pixels[$val])) {

                                    // See if this is a double value - i.e. "0 auto"
                                    if (stripos($val, 'auto') && strpos($val, ' ')) {
                                        $hid = '<input type="hidden" name="' . $key . '_s" value="' . $name . '~' . $rule . '"><input type="hidden" name="' . $key . '_add_auto" value="on">';
                                        $val = substr($val, 0, strpos($val, ' '));
                                    }
                                    else {
                                        $_pixels[$val] = $val;
                                    }

                                    // Make sure the value we are set AT is selected - even if not in array
                                    $tmp_val = intval($val);
                                    if (jrCore_checktype($tmp_val, 'number_nz') && $tmp_val > 50) {
                                        foreach (range(51, $tmp_val) as $tnum) {
                                            $_pixels["{$tnum}px"] = "{$tnum}px";
                                        }
                                        foreach (range(($tmp_val + 1), ($tmp_val + 25)) as $tnum) {
                                            $_pixels["{$tnum}px"] = "{$tnum}px";
                                        }
                                        natcasesort($_pixels);
                                    }

                                }
                                $opts = array();
                                foreach ($_pixels as $size) {
                                    if (isset($size) && $size == $val) {
                                        $opts[] = '<option selected value="' . $size . '">' . $size . '</option>';
                                    }
                                    else {
                                        $opts[] = '<option value="' . $size . '">' . $size . '</option>';
                                    }
                                }
                                $rtag = $rule;
                                if (isset($_cr[$name][$rule])) {
                                    $rtag = '<i>' . $rule . '</i>';
                                }
                                $_out[] = $hid . '<p class="style-label">' . $rtag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                                break;

                            case 'width':
                            case 'height':
                            case 'min-width';
                            case 'min-height';
                                $opts = array();
                                if (strpos($val, '%')) {
                                    if (!in_array($val, $_width_perc)) {
                                        $_width_perc[] = $val;
                                        sort($_width_perc, SORT_NUMERIC);
                                    }
                                    foreach ($_width_perc as $size) {
                                        if (isset($size) && $size == $val) {
                                            $opts[] = '<option selected value="' . $size . '">' . $size . '</option>';
                                        }
                                        else {
                                            $opts[] = '<option value="' . $size . '">' . $size . '</option>';
                                        }
                                    }
                                }
                                else {
                                    // Make sure the value we HAVE is always set
                                    if (!in_array($val, $_width_pix)) {
                                        $_width_pix[] = $val;
                                        sort($_width_pix, SORT_NUMERIC);
                                    }
                                    foreach ($_width_pix as $size) {
                                        if (isset($size) && $size == $val) {
                                            $opts[] = '<option selected value="' . $size . '">' . $size . '</option>';
                                        }
                                        else {
                                            $opts[] = '<option value="' . $size . '">' . $size . '</option>';
                                        }
                                    }
                                }
                                $rtag = $rule;
                                if (isset($_cr[$name][$rule])) {
                                    $rtag = '<i>' . $rule . '</i>';
                                }
                                $_out[] = $hid . '<p class="style-label">' . $rtag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                                break;

                        }
                    }
                }
                if (isset($_out) && is_array($_out) && count($_out) > 0) {
                    $rst = '';
                    if (isset($_cr[$name])) {
                        // We had some customizations in this element
                        $rst = '<div class="style-reset">' . jrCore_page_button("r{$key}", 'reset', "jrCore_confirm('Reset CSS element?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/css_reset_save/skin={$skin}/tag=" . urlencode($name) . "')})") . '</div>';
                    }
                    $_field = array(
                        'name'  => $key,
                        'type'  => 'custom',
                        'html'  => '<div class="style-box">' . implode('<br>', $_out) . '</div>' . $rst,
                        'label' => $_inf['title'],
                        'help'  => $_inf['help']
                    );
                    if ($name != $_inf['title']) {
                        $_field['sublabel'] = $name;
                    }
                    else {
                        $_field['label'] = '<span style="text-transform:none">' . $_field['label'] . '</span>';
                    }
                    if (isset($ffile[$name])) {
                        if (isset($_field['sublabel'])) {
                            $_field['sublabel'] .= '<br>';
                        }
                        $_field['sublabel'] .= 'file: ' . $ffile[$name];
                    }
                    jrCore_form_field_create($_field);
                }
            }
        }
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Image replacer and customization form
 * @param $type string Type module|image
 * @param $skin string Skin name
 * @param $_post array Post data
 * @param $_user array User info
 * @param $_conf array Global Config
 * @return mixed
 */
function jrCore_show_skin_images($type, $skin, $_post, $_user, $_conf)
{
    global $_mods;
    // Generate our output
    if ($type == 'module') {
        jrCore_page_admin_tabs($skin, 'images');
        $action = "admin_save/images/module={$skin}";
    }
    else {
        jrCore_page_skin_tabs($skin, 'images');
        $action = "skin_admin_save/images/skin={$skin}";
    }

    $version = '1';
    if ($type == 'module') {
        // Setup our module jumper
        $_mds = array();
        foreach ($_mods as $mod_dir => $_info) {
            if (jrCore_module_is_active($mod_dir) && is_dir(APP_DIR . "/modules/{$mod_dir}/img")) {
                $_fl = glob(APP_DIR . "/modules/{$mod_dir}/img/*{.png,.jpg,.gif}", GLOB_BRACE);
                if ($_fl && is_array($_fl)) {
                    $_mds[] = $mod_dir;
                }
            }
        }
        $subtitle = jrCore_get_module_jumper('mod_select', $_post['module'], "jrCore_window_location('{$_conf['jrCore_base_url']}/'+ $(this).val() + '/admin/images')", $_mds);
        $version  = $_mods[$skin]['module_version'];
    }
    else {
        $url      = jrCore_get_module_url('jrCore');
        $subtitle = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$url}/skin_admin/images/skin='+ $(this).val())\">";
        $_tmpm    = jrCore_get_skins();
        foreach ($_tmpm as $skin_dir => $_skin) {
            if (is_dir(APP_DIR . "/skins/{$skin_dir}/img")) {
                $_mta = jrCore_skin_meta_data($skin_dir);
                $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
                if ($skin_dir == $_post['skin']) {
                    $subtitle .= '<option value="' . $_post['skin'] . '" selected> ' . $name . "</option>\n";
                    $version  = $_mta['version'];
                }
                else {
                    $subtitle .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
                }
            }
        }
    }
    $subtitle .= '</select>';

    jrCore_page_banner('Images', $subtitle);
    // See if we are disabled
    if (!jrCore_module_is_active($_post['module'])) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }
    jrCore_get_form_notice();

    if (!isset($_conf["jrCore_{$skin}_custom_images"])) {
        // Custom image container (per skin)
        $_tmp = array(
            'name'     => "{$skin}_custom_images",
            'default'  => '',
            'type'     => 'hidden',
            'required' => 'on',
            'validate' => 'false',
            'label'    => "{$skin} custom images",
            'help'     => 'this hidden field holds the names of images that have been customized'
        );
        jrCore_register_setting('jrCore', $_tmp);
        $_conf["jrCore_{$skin}_custom_images"] = '';
    }

    // Form init
    $_tmp = array(
        'submit_value'     => 'save changes',
        'action'           => $action,
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $dat             = array();
    $dat[1]['title'] = 'default';
    $dat[1]['width'] = '30%';
    $dat[2]['title'] = 'active';
    $dat[2]['width'] = '5%';
    $dat[3]['title'] = 'custom';
    $dat[3]['width'] = '30%';
    $dat[4]['title'] = 'upload custom';
    $dat[4]['width'] = '35%';
    jrCore_page_table_header($dat);

    // Get any custom images
    $_cust = (isset($_conf["jrCore_{$skin}_custom_images"]{2})) ? json_decode($_conf["jrCore_{$skin}_custom_images"], true) : array();

    // Get all of our actual template files...
    // See if we are doing a module or a skin...
    if ($type == 'module') {
        $t_url = 'modules';
        $t_tag = 'mod_';
        $_imgs = glob(APP_DIR . "/modules/{$skin}/img/*.{png,jpg,jpeg,gif,ico}", GLOB_BRACE);
        $u_tag = 'mod';
        // If this is the core - move file_type images to the bottom
        if ($skin == 'jrCore') {
            $_one = array();
            $_two = array();
            foreach ($_imgs as $img) {
                if (strpos($img, '/file_type_')) {
                    $_two[] = $img;
                }
                else {
                    $_one[] = $img;
                }
            }
            $_imgs = array_merge($_one, $_two);
        }
    }
    else {
        $t_url = 'skins';
        $t_tag = '';
        $_imgs = glob(APP_DIR . "/skins/{$skin}/img/*.{png,jpg,jpeg,gif,ico}", GLOB_BRACE);
        $u_tag = 'skin';
    }
    $curl = jrCore_get_module_url('jrCore');
    $iurl = jrCore_get_module_url('jrImage');

    $plg = jrCore_get_active_media_system();
    $fnc = "_{$plg}_media_get_custom_image_url";

    $_is = array();
    if (is_array($_imgs)) {
        foreach ($_imgs as $k => $full_file) {
            $dat = array();
            $img = basename($full_file);
            if ($type == 'skin' && strpos($img, 'screenshot') === 0) {
                continue;
            }
            if ($type == 'module' && $img == 'install_logo.png') {
                continue;
            }
            $_is = getimagesize($full_file);
            $url = "{$_conf['jrCore_base_url']}/{$t_url}/{$skin}/img/{$img}?v={$version}";

            $w = $_is[0];
            $h = $_is[1];
            $l = false;
            if ($h >= 100) {
                $w = (($w / $h) * 100);
                $h = 100;
                $l = true;
                // See if our width is greater than 200 here...
                if ($w > 200) {
                    $h = (($h / $w) * 200);
                    $w = 200;
                }
            }
            elseif ($w > 100) {
                $h = (($h / $w) * 100);
                $w = 100;
                $l = true;
            }
            elseif ($w < 10) {
                $h = (($h / $w) * 10);
                $w = 10;
            }
            if ($l) {
                $dat[1]['title'] = "<a href=\"{$url}\" data-lightbox=\"images\" title=\"{$img}\"><img src=\"{$url}\" class=\"acp-default-img\" height=\"{$h}\" width=\"{$w}\" alt=\"{$img}\" title=\"{$img}\"></a>";
            }
            else {
                $dat[1]['title'] = "<img src=\"{$url}\" class=\"acp-default-img\" height=\"{$h}\" width=\"{$w}\" alt=\"{$img}\" title=\"{$img}\">";
            }
            $dat[1]['class'] = 'center';
            if (jrCore_file_extension($img) == 'png' && jrImage_is_alpha_png($full_file)) {
                $dat[1]['class'] .= ' transparent_image';
            }

            if (isset($_cust[$img])) {
                $chk = '';
                if (isset($_cust[$img][1]) && $_cust[$img][1] == 'on') {
                    $chk = ' checked="checked"';
                }
                $dat[2]['title'] = '<input type="hidden" name="name_' . $k . '_active" value="off"><input type="checkbox" name="name_' . $k . '_active" class="form-checkbox"' . $chk . '>';
                $dat[2]['class'] = 'center';
            }
            else {
                $dat[2]['title'] = '&nbsp;';
            }

            if (isset($_cust[$img])) {

                // Create our image URL
                if (function_exists($fnc)) {
                    $url = $fnc("{$t_tag}{$skin}_{$img}");
                    $w   = 100;
                    $h   = '';
                    $l   = true;
                }
                else {
                    $_is = getimagesize(APP_DIR . "/data/media/0/0/{$t_tag}{$skin}_{$img}"); // GOOD
                    $w   = $_is[0];
                    $h   = $_is[1];
                    $l   = false;
                    if ($h >= 100) {
                        $w = (($w / $h) * 100);
                        $h = 100;
                        $l = true;
                        // See if our width is greater than 200 here...
                        if ($w > 200) {
                            $h = (($h / $w) * 200);
                            $w = 200;
                        }
                    }
                    elseif ($w > 100) {
                        $h = (($h / $w) * 100);
                        $w = 100;
                        $l = true;
                    }
                    elseif ($w < 10) {
                        $h = (($h / $w) * 10);
                        $w = 10;
                    }
                    $h   = ' height="' . $h . '"';
                    $url = "{$_conf['jrCore_base_url']}/data/media/0/0/{$t_tag}{$skin}_{$img}";
                }
                $dat[3]['title'] = '<div style="width:120px;display:inline-block">';
                if ($l) {
                    $dat[3]['title'] = "<a href=\"{$url}\" data-lightbox=\"images\" title=\"{$img}\"><img src=\"{$url}?r=" . mt_rand() . "\" {$h} width=\"{$w}\" class=\"acp-custom-img\" alt=\"{$img}\" title=\"{$img}\"></a>";
                }
                else {
                    $dat[3]['title'] = "<img src=\"{$url}?r=" . mt_rand() . "\" {$h} width=\"{$w}\" class=\"acp-custom-img\" alt=\"{$img}\" title=\"{$img}\">";
                }
                $dat[3]['title'] .= "</div><br>" . jrCore_page_button("d{$k}", 'delete', "jrCore_confirm('Delete this Custom Image?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$curl}/skin_image_delete_save/{$u_tag}={$skin}/name={$img}')} )");
                $dat[3]['class'] = 'center';
                if (jrCore_file_extension($img) == 'png' && jrImage_is_alpha_png(APP_DIR . "/data/media/0/0/{$t_tag}{$skin}_{$img}")) {
                    $dat[3]['class'] .= ' transparent_image';
                }

                unset($_cust[$img]);
            }
            else {
                $dat[3]['title'] = '&nbsp;';
                $dat[3]['class'] = 'center';
            }
            $dat[4]['title'] = '<input type="hidden" name="name_' . $k . '" value="' . $img . '"><input type="file" name="file_' . $k . '"><br><span class="sublabel"><strong>' . $img . '</strong> - <strong>' . $_is[0] . ' x ' . $_is[1] . ' pixels</strong></span>';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();

        // Check for any custom images left over - not part of the skin
        if (isset($_cust) && is_array($_cust) && count($_cust) > 0) {

            if (isset($k)) {
                $k++;
            }
            else {
                $k = 0;
            }
            jrCore_page_divider();

            $dat             = array();
            $dat[1]['title'] = 'custom image';
            $dat[1]['width'] = '30%';
            $dat[2]['title'] = 'options';
            $dat[2]['width'] = '40%';
            $dat[3]['title'] = 'upload new custom image';
            $dat[3]['width'] = '30%';
            jrCore_page_table_header($dat);

            $dir = jrCore_get_media_directory(0);
            $num = 0;
            foreach ($_cust as $img => $size) {

                $dat = array();

                // Create our image URL
                if (function_exists($fnc)) {
                    $url = $fnc("{$t_tag}{$skin}_{$img}");
                    $w   = 100;
                    $h   = '';
                    $l   = true;
                }
                else {
                    $_is = getimagesize("{$dir}/{$t_tag}{$skin}_{$img}");
                    $w   = $_is[0];
                    $h   = $_is[1];
                    $l   = false;
                    if (isset($w) && $w > 100) {
                        $h = (($h / $w) * 100);
                        $w = 100;
                        $l = true;
                    }
                    elseif (isset($h) && $h > 100) {
                        $w = (($w / $h) * 100);
                        $h = 100;
                        $l = true;
                    }
                    elseif (isset($w) && $w < 10) {
                        $h = (($h / $w) * 10);
                        $w = 10;
                    }
                    $h   = ' height="' . $h . '"';
                    $url = "{$_conf['jrCore_base_url']}/{$iurl}/img/{$type}/{$skin}/{$img}";
                }

                $dat[1]['title'] = '<div style="width:120px;display:inline-block;vertical-align:middle;">';
                if ($l) {
                    $css_width       = "{$w}px";
                    $dat[1]['title'] .= "<a href=\"{$url}\" data-lightbox=\"images\" title=\"{$img}\"><img src=\"{$url}?r=" . mt_rand() . "\" {$h} width=\"{$w}\" class=\"acp-default-img\" alt=\"{$img}\" title=\"{$img}\" style=\"width:{$css_width};margin-bottom:6px\"></a>";
                }
                else {
                    $dat[1]['title'] .= "<img src=\"{$url}?r=" . mt_rand() . "\"{$h} width=\"{$w}\" class=\"acp-default-img\" alt=\"{$img}\" title=\"{$img}\" style=\"width:{$w}px;margin-bottom:6px\">";
                }

                $dat[1]['title'] .= '</div>';
                $dat[1]['class'] = 'center';
                $embed           = '<br><br><strong>Template Code (no wrap):</strong><br><div id="debug_log" style="width:390px;padding:0;word-wrap:break-word">{jrCore_image ' . $type . '=&quot;' . $skin . '&quot; image=&quot;' . $img . '&quot}</div>';
                $dat[2]['title'] = jrCore_page_button("d{$num}", 'delete image', "jrCore_confirm('Delete this Custom Image?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$curl}/skin_image_delete_save/{$u_tag}={$skin}/name={$img}')})") . $embed;
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = '<input type="hidden" name="name_' . $k . '" value="' . $img . '"><input type="file" name="file_' . $k . '"><br><span class="sublabel" style="word-wrap:break-word"><strong>' . str_replace('_', '', $img) . '</strong> - <strong>' . $_is[0] . ' x ' . $_is[1] . '</strong></span>';
                jrCore_page_table_row($dat);
                $num++;
                $k++;
            }
            jrCore_page_table_footer();
        }
    }

    // Upload new image
    $imax = array_keys(jrImage_get_allowed_image_sizes());
    $imax = end($imax);
    $_tmp = array(
        'name'       => "new_images",
        'type'       => 'file',
        'label'      => 'additional images',
        'help'       => 'Upload custom images for use in your templates',
        'text'       => 'Select Images to Upload',
        'extensions' => 'png,gif,jpg,jpeg',
        'multiple'   => true,
        'required'   => false,
        'max'        => $imax
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Display Available templates for editing
 * @param $skin string Skin directory
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_skin_templates($skin, $_post, $_user, $_conf)
{
    unset($_SESSION['template_cancel_url']);

    jrCore_page_skin_tabs($skin, 'templates');

    $murl  = jrCore_get_module_url('jrCore');
    $temp  = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$murl}/skin_admin/templates/skin='+ $(this).val())\">";
    $_tmpm = jrCore_get_skins();
    foreach ($_tmpm as $skin_dir => $_skin) {
        $_mta = jrCore_skin_meta_data($skin_dir);
        $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
        if ($skin_dir == $_post['skin']) {
            $temp .= '<option value="' . $skin_dir . '" selected> ' . $name . "</option>\n";
        }
        else {
            $temp .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
        }
    }
    $temp .= '</select>';
    jrCore_page_banner('Templates', $temp);

    // See if we have a search string
    $_tpls = glob(APP_DIR . "/skins/{$skin}/*.tpl");
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        // Search through templates
        foreach ($_tpls as $k => $full_file) {
            $temp = file_get_contents($full_file);
            if (!stristr(' ' . $temp, $_post['search_string'])) {
                unset($_tpls[$k]);
            }
        }
    }

    // Get templates from database to see if we have customized any of them
    $tbl = jrCore_db_table_name('jrCore', 'template');
    $req = "SELECT template_id, template_module, template_updated, template_user, template_active, template_name, MD5(template_body) AS template_md5 FROM {$tbl} WHERE template_module = '" . jrCore_db_escape($skin) . "'";
    $_tp = jrCore_db_query($req, 'template_name');

    // See if we have any customized templates that are NOT active
    if (isset($_tpls) && is_array($_tpls)) {
        foreach ($_tpls as $full_file) {
            $tpl_name = basename($full_file);
            if (isset($_tp[$tpl_name]) && isset($_tp[$tpl_name]['template_active']) && $_tp[$tpl_name]['template_active'] != '1') {
                jrCore_set_form_notice('error', "The highlighted customized templates are <b>NOT ACTIVE!</b><br>To activate the customized templates and see your changes check the &quot;active&quot; box and save", false);
                break;
            }
        }
    }

    jrCore_get_form_notice();
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/templates/skin={$skin}");

    // Form init
    $_tmp = array(
        'submit_value'     => 'save changes',
        'action'           => "skin_admin_save/templates/skin={$skin}",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'template';
    $dat[1]['width'] = '60%';
    $dat[2]['title'] = 'active';
    $dat[2]['width'] = '5%';
    $dat[3]['title'] = 'updated';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'compare';
    $dat[5]['width'] = '3%';
    $dat[6]['title'] = 'reset';
    $dat[6]['width'] = '2%';
    jrCore_page_table_header($dat);

    // Get all of our actual template files...
    $_skn = jrCore_get_skin_db_info($skin);
    if (isset($_tpls) && is_array($_tpls)) {

        // Get the skin cloned from if this is a cloned skin
        $cls = '';
        $_cl = false;
        $url = jrCore_get_module_url('jrCore');
        if (strpos($skin, 'jr') !== 0) {
            if ($_skn && isset($_skn['skin_cloned_from']) && strlen($_skn['skin_cloned_from']) > 0 && is_dir(APP_DIR . "/skins/{$_skn['skin_cloned_from']}")) {
                // Go through and get existing files that we
                $cls = "/version={$_skn['skin_cloned_from']}";
                $_tm = glob(APP_DIR . "/skins/{$_skn['skin_cloned_from']}/*.tpl");
                if ($_tm && is_array($_tm)) {
                    $_cl = array();
                    $_rp = array(
                        $_skn['skin_cloned_from']                        => $skin,
                        strtolower($_skn['skin_cloned_from'])            => strtolower($skin),
                        strtoupper($_skn['skin_cloned_from'])            => strtoupper($skin),
                        substr($_skn['skin_cloned_from'], 2)             => substr($skin, 2),
                        strtolower(substr($_skn['skin_cloned_from'], 2)) => strtolower(substr($skin, 2)),
                        strtoupper(substr($_skn['skin_cloned_from'], 2)) => strtoupper(substr($skin, 2)),
                    );
                    foreach ($_tm as $file) {
                        $name       = basename($file);
                        $file       = strtr(file_get_contents($file), $_rp);
                        $_cl[$name] = md5($file);
                    }
                }
            }
        }

        // Go through templates on file system
        foreach ($_tpls as $full_file) {
            $dat             = array();
            $tpl_name        = basename($full_file);
            $dat[1]['title'] = $tpl_name;
            if (isset($_tp[$tpl_name])) {
                $checked = '';
                if (isset($_tp[$tpl_name]['template_active']) && $_tp[$tpl_name]['template_active'] == '1') {
                    $checked         = ' checked="checked"';
                    $dat[2]['class'] = 'center';
                }
                else {
                    // Are there changes in this template?  If so hilight the active column
                    $dat[2]['class'] = 'center error';
                }
                $chk_name        = str_replace('.tpl', '', $tpl_name);
                $dat[2]['title'] = '<input type="hidden" name="' . $chk_name . '_template_active" value="off"><input type="checkbox" name="' . $chk_name . '_template_active" class="form-checkbox"' . $checked . '>';
                $dat[3]['title'] = jrCore_format_time($_tp[$tpl_name]['template_updated']) . '<br>' . $_tp[$tpl_name]['template_user'];
                $dat[3]['class'] = 'center nowrap';
            }
            else {
                $dat[2]['title'] = '&nbsp;';
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = '&nbsp;';
            }

            if (isset($_tp[$tpl_name])) {

                $dat[4]['title'] = jrCore_page_button("modify-{$tpl_name}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/skin={$skin}/id=" . $_tp[$tpl_name]['template_id'] . "')");
                // Are we different from our clone?
                if ($_cl && isset($_cl[$tpl_name])) {
                    if (md5($_tp[$tpl_name]['template_md5']) != $_cl[$tpl_name]) {
                        $dat[5]['title'] = jrCore_page_button("compare-{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_compare/skin={$skin}/id=" . $_tp[$tpl_name]['template_id'] . "{$cls}')");
                    }
                    else {
                        $dat[5]['title'] = jrCore_page_button("compare-{$tpl_name}", 'compare', 'disabled');
                    }
                }
                else {
                    $dat[5]['title'] = jrCore_page_button("compare-{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_compare/skin={$skin}/id=" . $_tp[$tpl_name]['template_id'] . "')");
                }
                $dat[6]['title'] = jrCore_page_button("reset-{$tpl_name}", 'reset', "jrCore_confirm('Reset Template?', 'Do you want to reset this template to the skin default?', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_reset_save/skin={$skin}/id=" . $_tp[$tpl_name]['template_id'] . "')})");
            }
            else {

                $dat[4]['title'] = jrCore_page_button("modify-{$tpl_name}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/skin={$skin}/template={$tpl_name}')");
                // Are we different from our clone?
                if ($_cl && isset($_cl[$tpl_name])) {
                    if (md5_file(APP_DIR . "/skins/{$skin}/{$tpl_name}") != $_cl[$tpl_name]) {
                        $dat[5]['title'] = jrCore_page_button("compare-{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_compare/skin={$skin}/id=" . urlencode($tpl_name) . "{$cls}')");
                    }
                    else {
                        $dat[5]['title'] = jrCore_page_button("compare-{$tpl_name}", 'compare', 'disabled');
                    }
                }
                else {
                    $dat[5]['title'] = jrCore_page_button("compare-{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_compare/skin={$skin}/id=" . urlencode($tpl_name) . "')");
                }
                $dat[6]['title'] = jrCore_page_button("reset-{$tpl_name}", 'reset', 'disabled');
            }
            jrCore_page_table_row($dat);
            if (isset($_cl[$tpl_name])) {
                unset($_cl[$tpl_name]);
            }
        }
        jrCore_page_table_footer();

        // Any templates left over?
        if ($_cl && count($_cl) > 0) {
            // We have left over templates
            $dat             = array();
            $dat[1]['title'] = 'new templates not in this skin';
            $dat[1]['width'] = '95%';
            $dat[2]['title'] = 'import';
            $dat[2]['width'] = '5%';
            jrCore_page_table_header($dat);
            foreach ($_cl as $tpl => $md5) {
                $dat             = array();
                $dat[1]['title'] = $tpl;
                $dat[2]['title'] = jrCore_page_button("import-{$tpl}", 'import', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_import_save/skin={$skin}/tpl=" . urlencode($tpl) . "')");
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
        }

        if ($_skn && isset($_skn['skin_cloned_from']) && strlen($_skn['skin_cloned_from']) > 0 && is_dir(APP_DIR . "/skins/{$_skn['skin_cloned_from']}")) {
            $dat             = array();
            $dat[1]['title'] = 'create a new template in this skin';
            $dat[1]['width'] = '95%';
            $dat[2]['title'] = 'create';
            $dat[2]['width'] = '5%';
            jrCore_page_table_header($dat);

            $dat             = array();
            $dat[1]['title'] = '<input type="text" id="new_template_name" name="new_template_name" class="form_text" placeholder="template_name.tpl">';
            $dat[2]['title'] = jrCore_page_button("create-template", 'create', "var v=$('#new_template_name').val(); if (v.length > 0) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_create_save/skin={$skin}/tpl=' + v) }");
            jrCore_page_table_row($dat);
            jrCore_page_table_footer();
        }

    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There were no templates found to match your search criteria!</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
        jrCore_page_table_footer();
    }

    // Save Template Updates - this small hidden field needs to be here
    // otherwise the form will not work - this is due to the fact the checkbox
    // elements in the table were created outside of jrCore_form_field_create
    $_tmp = array(
        'name'     => "save_template_updates",
        'type'     => 'hidden',
        'required' => 'true',
        'validate' => 'onoff',
        'value'    => 'on'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Show info about a skin
 * @param $skin string skin directory
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_skin_info($skin, $_post, $_user, $_conf)
{
    // Generate our output
    jrCore_page_skin_tabs($skin, 'info');
    $murl = jrCore_get_module_url('jrCore');
    $temp = '';
    if ($skin != $_conf['jrCore_active_skin'] && (!is_dir(APP_DIR . "/skins/{$skin}") || is_writable(APP_DIR . "/skins/{$skin}"))) {
        $temp = jrCore_page_button('delete-skin', 'delete skin', "jrCore_confirm('Delete this Skin?', 'Do you want to remove this skin from your site?', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/skin_admin_save/info/skin={$skin}/skin_delete=on')})");
    }
    $temp .= '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$murl}/skin_admin/info/skin='+ $(this).val())\">";
    $_sel = jrCore_get_skins();
    foreach ($_sel as $skin_dir => $_skin) {
        $_mta = jrCore_skin_meta_data($skin_dir);
        $titl = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
        if ($skin_dir == $skin) {
            $temp .= '<option value="' . $skin_dir . '" selected> ' . $titl . "</option>\n";
        }
        else {
            $temp .= '<option value="' . $skin_dir . '"> ' . $titl . "</option>\n";
        }
    }
    $temp .= '</select>';

    $_mta = jrCore_skin_meta_data($skin);
    $name = (isset($_mta['title'])) ? $_mta['title'] : $skin;
    jrCore_page_banner($name, $temp);

    $_opt = array('description', 'version', 'developer');
    $icon = jrCore_get_skin_icon_html($skin, 164);
    $temp = '<div id="info_box"><table><tr><td class="item" style="width:10%">' . $icon . '</td>';

    foreach ($_opt as $k => $key) {
        if (isset($_mta[$key])) {
            $_tmp[] = '<strong>' . $key . ':</strong> <span>' . $_mta[$key] . "</span>";
        }
    }

    // See if this module has a readme associated with it
    $type = 'Open Source';
    if (strtolower($_mta['license']) == 'jcl') {
        $type = '&#9734; Premium &#9734;';
    }
    $text   = "{$type} &nbsp;&bull;&nbsp; <a href=\"{$_conf['jrCore_base_url']}/{$murl}/license/skin={$skin}\" onclick=\"popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/license/skin={$skin}','license',800,500,'yes');return false\"><span style=\"text-decoration:underline;\">View License</span></a>";
    $_tmp[] = '<strong>license:</strong> <span>' . $text . "</span>";

    // See if this module has a readme associated with it
    if (is_file(APP_DIR . "/modules/{$skin}/readme.html")) {
        $text   = "<a href=\"{$_conf['jrCore_base_url']}/skins/{$skin}/readme.html\" onclick=\"popwin('{$_conf['jrCore_base_url']}/skins/{$skin}/readme.html','readme',800,500,'yes');return false\"><span style=\"text-decoration:underline;\">Click to View Skin Notes</span></a>";
        $_tmp[] = '<strong>notes:</strong> <span>' . $text . "</span>";
    }

    // Skin Directory
    $_tmp[] = '<strong>directory:</strong> <span>' . $skin . "</span>";

    $temp .= '<td class="item" style="width:90%">' . implode('<br>', $_tmp) . '</td></tr>';

    // Check for screen shots
    $_img = array();
    foreach (range(1, 4) as $n) {
        if (is_file(APP_DIR . "/skins/{$skin}/img/screenshot{$n}.jpg")) {
            $_img[] = "{$_conf['jrCore_base_url']}/skins/{$skin}/img/screenshot{$n}.jpg";
        }
    }
    $icnt = count($_img);
    if ($icnt > 0) {
        $col = 12;
        switch ($icnt) {
            case 2:
                $col = 6;
                break;
            case 3:
                $col = 4;
                break;
            case 4:
                $col = 3;
                break;
        }
        $temp .= '<tr><td class="item p5" colspan="2"><div class="row">';
        foreach ($_img as $k => $shot) {
            $temp .= "<div class=\"col{$col}\"><div class=\"p5\"><a href=\"{$shot}?_v={$_mta['version']}\" data-lightbox=\"screenshots\" title=\"screenshot " . ($k + 1) . "\"><img src=\"{$shot}?_v={$_mta['version']}\" class=\"img_scale\" alt=\"screenshot " . ($k + 1) . "\"></a></div></div>";
        }
        $temp .= '</div></td></tr>';
    }
    $temp .= '</table></div>';
    jrCore_page_custom($temp);

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'action'       => "skin_admin_save/info/skin={$skin}"
    );
    jrCore_form_create($_tmp);

    // Active Skin
    $act = 'off';
    if (isset($_conf['jrCore_active_skin']) && $_conf['jrCore_active_skin'] == $skin) {
        $act = 'on';
    }
    $_tmp = array(
        'name'     => 'skin_active',
        'label'    => 'set as active skin',
        'help'     => "If you would like to use this skin for your site, check this option and save.",
        'type'     => 'checkbox',
        'value'    => $act,
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Parse a Jamroom CSS File and return an array of CSS items
 * @param $file string File to parse
 * @param $section string Active section
 * @return mixed
 */
function jrCore_parse_css_file($file, $section)
{
    if (!is_file($file)) {
        return false;
    }
    $_tmp = file($file);
    if (!$_tmp || !is_array($_tmp)) {
        return false;
    }
    $_out = array();

    // Characters we strip from title and help lines
    $ignore_next_item = false;
    $_strip           = array('@title', '@help', '/*', '*/');
    foreach ($_tmp as $line) {

        $line = trim($line);
        if (strpos(' ' . $line, '@media')) {
            jrCore_set_flag('jrcore_css_media_enabled', trim(rtrim($line, '{')));
            continue;
        }

        // End comment on separate line
        if (strlen($line) < 1 || strpos($line, '*') === 0 || strpos(' ' . $line, '@ignore')) {
            continue;
        }

        // Comment
        elseif (strpos($line, '/*') === 0) {
            if (!strpos($line, '@') && $section != 'extra') {
                continue;
            }
            // We have a comment with info..
            if (strpos($line, '@title')) {
                $title = trim(str_replace($_strip, '', $line));
            }
            elseif (strpos($line, '@help')) {
                $help = trim(str_replace($_strip, '', $line));
            }
            elseif (strpos($line, '@ignore')) {
                $ignore_next_item = true;
            }
            continue;
        }

        // Element/Class/ID - begin
        elseif (strpos($line, '{') && !strpos($line, '{$jamroom') && !strpos($line, '_img_url}/')) {
            if ((!isset($title) && $section != 'extra') || $ignore_next_item) {
                continue;
            }
            if (isset($title) && $section == 'extra') {
                continue;
            }
            $name = trim(substr($line, 0, strpos($line, '{')));
            if ($section == 'extra' && !isset($title)) {
                $title = $name;
                $help  = false;
            }
            if (!$ignore_next_item) {
                $_out[$name] = array(
                    'title' => isset($title) ? $title : '',
                    'help'  => isset($help) ? $help : '',
                    'rules' => array()
                );
            }
        }

        // Element/Class/ID - end
        elseif (strpos($line, '}') === 0) {
            if ($ignore_next_item) {
                $ignore_next_item = false;
                continue;
            }
            if (!isset($title)) {
                continue;
            }
            if (isset($name)) {
                unset($name);
            }
            if (isset($title)) {
                unset($title);
            }
            if (isset($help)) {
                unset($help);
            }
        }

        // Rules
        elseif (isset($name) && strpos($line, ':')) {
            if ($ignore_next_item) {
                continue;
            }
            if (!isset($title)) {
                continue;
            }
            list($rule, $value) = explode(':', $line, 2);
            $rule  = trim($rule);
            $value = trim(ltrim(rtrim(trim(str_replace('!important', '', $value)), ';'), '#'));
            // If this is a "background" rule, and we only have a SINGLE value, then change to background-color
            switch ($rule) {
                case 'background':
                    if (str_word_count($value) === 1 && strlen($value) < 7) {
                        $rule = 'background-color';
                    }
                    break;
            }
            $_out[$name]['rules'][$rule] = $value;
        }
    }
    return $_out;
}

/**
 * Create a Global Config screen for a module/skins
 * @param $type string module|skin
 * @param $module string module|skin name
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @return mixed
 */
function jrCore_show_global_settings($type, $module, $_post, $_user, $_conf)
{
    global $_mods;

    // Get this module's config entries from settings
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT * FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($module) . "' AND `type` != 'hidden' ORDER BY `order` ASC, `section` ASC, `name` ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt || !is_array($_rt)) {
        $url = jrCore_get_module_url($module);
        jrCore_location("{$_conf['jrCore_base_url']}/{$url}/admin/info");
    }

    // Check for incoming highlighting
    if (isset($_post['hl']) && is_array($_post['hl'])) {
        foreach ($_post['hl'] as $fld) {
            jrCore_form_field_hilight($fld);
        }
    }
    elseif (isset($_post['hl']) && strlen($_post['hl']) > 0) {
        jrCore_form_field_hilight($_post['hl']);
    }

    // Generate our output
    $sbl = '';
    if ($type == 'module') {

        if (jrCore_module_is_active($module) && !function_exists("{$module}_config") && is_file(APP_DIR . "/modules/{$module}/config.php")) {
            require_once APP_DIR . "/modules/{$module}/config.php";
        }
        $func = "{$module}_config";
        if (function_exists($func)) {
            $func();
        }
        $func = "{$module}_config_display";
        if (function_exists($func)) {
            $func($_post, $_user, $_conf);
        }

        jrCore_page_admin_tabs($module, 'global');
        $_tb = array();
        $act = false;
        $frs = (isset($_post['section'])) ? $_post['section'] : false;
        foreach ($_rt as $k => $_set) {
            if (!isset($_set['section']) || strlen(trim($_set['section'])) === 0) {
                $_set['section']    = 'general settings';
                $_rt[$k]['section'] = 'general settings';
            }
            if (isset($_set['section']{0}) && !isset($_tb["{$_set['section']}"])) {
                $_tb["{$_set['section']}"] = array(
                    "label" => $_set['section'],
                    "url"   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/global/section=" . urlencode($_set['section'])
                );
                if (isset($_post['section']) && $_post['section'] == $_set['section']) {
                    $_tb["{$_set['section']}"]['active'] = 1;
                    $sbl                                 = $_set['section'];
                    $act                                 = true;
                }
                if (!$frs) {
                    $frs = $_set['section'];
                }
            }
        }
    }
    else {

        if (is_file(APP_DIR . "/skins/{$module}/config.php")) {
            require_once APP_DIR . "/skins/{$module}/config.php";
        }
        $func = "{$module}_skin_config";
        if (function_exists($func)) {
            $func();
        }
        $func = "{$module}_skin_config_display";
        if (function_exists($func)) {
            $func($_post, $_user, $_conf);
        }

        jrCore_page_skin_tabs($_post['skin'], 'global');
        $_tb = array();
        $act = false;
        $frs = (isset($_post['section'])) ? $_post['section'] : false;
        foreach ($_rt as $_set) {
            if (isset($_set['section']{0}) && !isset($_tb["{$_set['section']}"])) {
                $_tb["{$_set['section']}"] = array(
                    "label" => $_set['section'],
                    "url"   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/global/skin={$_post['skin']}/section=" . urlencode($_set['section'])
                );
                if (isset($_post['section']) && $_post['section'] == $_set['section']) {
                    $_tb["{$_set['section']}"]['active'] = 1;
                    $sbl                                 = $_set['section'];
                    $act                                 = true;
                }
                if (!$frs) {
                    $frs = $_set['section'];
                }
            }
        }
    }

    // Setup our module jumper
    $url = jrCore_get_module_url('jrCore');
    if ($type == 'skin') {
        $subtitle = '<select name="designer_form" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$url}/skin_admin/global/skin='+ $(this).val())\">";
        $_tmpm    = jrCore_get_skins();
        foreach ($_tmpm as $skin_dir => $_skin) {
            if (is_file(APP_DIR . "/skins/{$skin_dir}/config.php")) {
                $_mta = jrCore_skin_meta_data($skin_dir);
                $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
                if ($skin_dir == $_post['skin']) {
                    $subtitle .= '<option value="' . $_post['skin'] . '" selected> ' . $name . "</option>\n";
                }
                else {
                    $subtitle .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
                }
            }
        }
    }
    else {
        $_mds = array();
        foreach ($_mods as $mod_dir => $_info) {
            if (jrCore_module_is_active($mod_dir) && jrCore_module_has_visible_config($mod_dir)) {
                $_mds[] = $mod_dir;
            }
        }
        $subtitle = jrCore_get_module_jumper('designer_form', $_post['module'], "jrCore_window_location('{$_conf['jrCore_base_url']}/'+ $(this).val() + '/admin/global')", $_mds);
    }

    if (count($_tb) > 0) {
        // We've got sections
        if (!$act) {
            // Default to first section
            $_tb[$frs]['active'] = true;
            $sbl                 = $_tb[$frs]['label'];
        }
    }

    jrCore_page_banner("Global Config &bull; {$sbl}", $subtitle);
    jrCore_page_tab_bar($_tb);

    if ($type == 'module') {
        $action = 'admin_save/global';
    }
    else {
        $action = "skin_admin_save/global/skin={$module}";
    }

    // See if we are disabled
    if ($type == 'module' && !jrCore_module_is_active($module)) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }

    $_er = array();
    if (isset($_conf['jrCore_maintenance_mode']) && $_conf['jrCore_maintenance_mode'] == 'on') {
        $durl  = jrCore_get_module_url('jrCore');
        $_er[] = "Maintenance Mode is <strong>ENABLED</strong> - only admins can log in - change in <a href=\"{$_conf['jrCore_base_url']}/{$durl}/admin/global/section=maintenance/hl[]=maintenance_mode\"><u>Global Settings</u></a>";
    }
    if (jrCore_is_developer_mode()) {
        $durl  = jrCore_get_module_url('jrDeveloper');
        $_er[] = "Developer Mode is <strong>ENABLED</strong> - caching is disabled - change in <a href=\"{$_conf['jrCore_base_url']}/{$durl}/admin/global/hl[]=developer_mode\"><u>Global Settings</u></a>";
    }
    if (!jrCore_queues_are_active()) {
        $_er[] = "Queues are <strong>PAUSED</strong> - queue workers are not running - change in the <a href=\"{$_conf['jrCore_base_url']}/{$url}/dashboard/queue_viewer\"><u>Queue Viewer</u></a>";
    }
    if (count($_er) > 0) {
        jrCore_set_form_notice('error', implode('<br>', $_er), false);
    }
    jrCore_get_form_notice();

    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        // Form init
        $_tmp = array(
            'submit_value' => 'save changes',
            'action'       => $action
        );
        if ($type != 'module') {
            $_tmp['form_ajax_submit'] = false;
        }
        jrCore_form_create($_tmp);

        $_orig = jrCore_get_flag('jrCore_register_setting_fields');
        foreach ($_rt as $_field) {
            if (isset($_orig["{$_field['name']}"]) && is_array($_orig["{$_field['name']}"])) {
                $_field = array_merge($_field, $_orig["{$_field['name']}"]);
                if (!isset($_orig["{$_field['name']}"]['sublabel']) && isset($_field['sublabel'])) {
                    unset($_field['sublabel']);
                }
            }
            if ($frs) {
                if (isset($_field['section']) && $_field['section'] == $frs) {
                    unset($_field['section']);
                    jrCore_form_field_create($_field);
                }
            }
            else {
                jrCore_form_field_create($_field);
            }
        }
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Show the TOOLS section for a module
 * @param $module string module directory name
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_module_tools($module, $_post, $_user, $_conf)
{
    global $_mods;

    // Get registered tool views
    $_tool = jrCore_get_registered_module_features('jrCore', 'tool_view');

    // Generate our output
    jrCore_page_admin_tabs($module, 'tools');

    $_mds = array();
    foreach ($_mods as $mod_dir => $_info) {
        if (jrCore_module_is_active($mod_dir) && (isset($_tool[$mod_dir]) || jrCore_db_get_prefix($mod_dir))) {
            $_mds[] = $mod_dir;
        }
    }
    $subtitle = jrCore_get_module_jumper('module_jumper', $_post['module'], "jrCore_window_location('{$_conf['jrCore_base_url']}/'+ $(this).val() + '/admin/tools')", $_mds);

    jrCore_page_banner('Tools', $subtitle);
    if (!jrCore_module_is_active($module)) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }
    jrCore_get_form_notice();

    if ((!isset($_tool[$module]) || !is_array($_tool[$module])) && !jrCore_db_get_prefix($module)) {
        jrCore_notice_page('error', 'there are no registered tool views for this module!');
    }

    // Always do the module tools first
    $done = false;
    if ($_tool && is_array($_tool) && isset($_tool[$module])) {
        foreach ($_tool[$module] as $view => $_inf) {
            // Is this a function provided by the MODULE directly or by another module?
            if (jrCore_checktype($view, 'core_string') && !function_exists("view_{$module}_{$view}") && !$done) {
                continue;
            }
            $onc = (isset($_inf[2])) ? $_inf[2] : null;
            if (strpos($view, $_conf['jrCore_base_url']) === 0) {
                jrCore_page_tool_entry($view, $_inf[0], $_inf[1], $onc, '_blank');
            }
            else {
                jrCore_page_tool_entry("{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$view}", $_inf[0], $_inf[1], $onc);
            }
            unset($_tool[$module][$view]);
        }
        if (count($_tool[$module]) > 0) {
            $done = true;
            jrCore_page_section_header('tools provided by other modules');
            foreach ($_tool[$module] as $view => $_inf) {
                $onc = (isset($_inf[2])) ? $_inf[2] : null;
                if (strpos($view, $_conf['jrCore_base_url']) === 0) {
                    jrCore_page_tool_entry($view, $_inf[0], $_inf[1], $onc, '_blank');
                }
                else {
                    jrCore_page_tool_entry("{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$view}", $_inf[0], $_inf[1], $onc);
                }
            }
        }
    }

    // Check for DataStore browser
    if (jrCore_db_get_prefix($module) && $module != 'jrCore') {
        if (!$done) {
            jrCore_page_section_header('tools provided by other modules');
        }
        // DataStore enabled - check to see if this module is already registering a browser
        $_tmp = jrCore_get_registered_module_features('jrCore', 'tool_view');
        if (!isset($_tmp[$module]) || !isset($_tmp[$module]['browser'])) {
            jrCore_page_tool_entry("{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/browser", 'DataStore Browser', "Modify and Delete items in this module's DataStore");
        }
    }

    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Show the templates for a module
 * @param $module string module directory name
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_module_templates($module, $_post, $_user, $_conf)
{
    global $_mods;
    unset($_SESSION['template_cancel_url']);
    // Generate our output
    jrCore_page_admin_tabs($module, 'templates');

    $_mds = array();
    foreach ($_mods as $mod_dir => $_info) {
        if (jrCore_module_is_active($mod_dir) && is_dir(APP_DIR . "/modules/{$mod_dir}/templates")) {
            $_mds[] = $mod_dir;
        }
    }
    $subtitle = jrCore_get_module_jumper('designer_form', $_post['module'], "jrCore_window_location('{$_conf['jrCore_base_url']}/'+ $(this).val() + '/admin/templates')", $_mds);

    jrCore_page_banner('Templates', $subtitle);
    if (!jrCore_module_is_active($module)) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }
    jrCore_get_form_notice();

    // Get templates
    $_tpls = glob(APP_DIR . "/modules/{$module}/templates/*.tpl");

    // Get templates from database to see if we have customized any of them
    $tbl = jrCore_db_table_name('jrCore', 'template');
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $req = "SELECT template_id, template_module, template_updated, template_user, template_active, template_name, template_body FROM {$tbl} WHERE template_module = '" . jrCore_db_escape($module) . "'";
    }
    else {
        $req = "SELECT template_id, template_module, template_updated, template_user, template_active, template_name FROM {$tbl} WHERE template_module = '" . jrCore_db_escape($module) . "'";
    }
    $_tp = jrCore_db_query($req, 'template_name');

    // See if we have a search string
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        // Search through templates
        foreach ($_tpls as $k => $full_file) {
            $fname = basename($full_file);
            $found = false;

            // Match in file name
            if (stripos(' ' . $fname, $_post['search_string'])) {
                $found = true;
            }

            // Match in custom contents
            if (isset($_tp[$fname]['template_body']{0})) {
                $temp = file_get_contents($_tp[$fname]['template_body']);
                if (stristr(' ' . $temp, $_post['search_string'])) {
                    $found = true;
                }
            }

            // Match in actual file contents
            $temp = file_get_contents($full_file);
            if (stristr(' ' . $temp, $_post['search_string'])) {
                $found = true;
            }
            if (!$found) {
                unset($_tpls[$k]);
            }
        }
    }
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/templates");

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'action'       => 'admin_save/templates'
    );
    jrCore_form_create($_tmp);

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'name';
    $dat[1]['width'] = '55%';
    $dat[2]['title'] = 'active';
    $dat[2]['width'] = '5%';
    $dat[3]['title'] = 'updated';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'compare';
    $dat[5]['width'] = '3%';
    $dat[6]['title'] = 'reset';
    $dat[6]['width'] = '2%';
    jrCore_page_table_header($dat);

    // Get all of our actual template files...
    if (isset($_tpls) && is_array($_tpls) && count($_tpls) > 0) {

        $url = jrCore_get_module_url('jrCore');

        // Go through templates on file system
        foreach ($_tpls as $full_file) {
            $dat             = array();
            $tpl_name        = basename($full_file);
            $dat[1]['title'] = $tpl_name;
            $dat[1]['class'] = (isset($_post['hl']) && $_post['hl'] == $tpl_name) ? 'field-hilight' : '';
            if (isset($_tp[$tpl_name])) {
                $checked = '';
                if (isset($_tp[$tpl_name]['template_active']) && $_tp[$tpl_name]['template_active'] == '1') {
                    $checked = ' checked="checked"';
                }
                $chk_name        = str_replace('.tpl', '', $tpl_name);
                $dat[2]['title'] = '<input type="hidden" name="' . $chk_name . '_template_active" value="off"><input type="checkbox" name="' . $chk_name . '_template_active" class="form-checkbox"' . $checked . '>';
                $dat[3]['title'] = jrCore_format_time($_tp[$tpl_name]['template_updated']) . '<br>' . $_tp[$tpl_name]['template_user'];
                $dat[3]['class'] = 'center nowrap';
            }
            else {
                $dat[2]['title'] = '&nbsp;';
                $dat[3]['title'] = '&nbsp;';
            }
            $dat[2]['class'] = 'center';
            if (isset($_tp[$tpl_name])) {
                $dat[4]['title'] = jrCore_page_button("m{$tpl_name}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/id=" . $_tp[$tpl_name]['template_id'] . "')");
                $dat[5]['title'] = jrCore_page_button("c{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_compare/id=" . $_tp[$tpl_name]['template_id'] . "')");
                $dat[6]['title'] = jrCore_page_button("r{$tpl_name}", 'reset', "jrCore_confirm('Reset Template?', 'Are you sure you want to reset this template to the default?', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_reset_save/id=" . $_tp[$tpl_name]['template_id'] . "')})");
            }
            else {
                $dat[4]['title'] = jrCore_page_button("m{$tpl_name}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/template={$tpl_name}')");
                $dat[5]['title'] = jrCore_page_button("c{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_compare/id=" . urlencode($tpl_name) . "')");
                $dat[6]['title'] = jrCore_page_button("r{$tpl_name}", 'reset', 'disabled');
            }
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There were no templates found to match your search criteria!</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Save Template Updates - this small hidden field needs to be here
    // otherwise the form will not work - this is due to the fact the checkbox
    // elements in the table were created outside of jrCore_form_field_create
    $_tmp = array(
        'name'     => "save_template_updates",
        'type'     => 'hidden',
        'required' => 'true',
        'validate' => 'onoff',
        'value'    => 'on'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Show the info page for a module
 * @param $module string module directory name
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_module_info($module, $_post, $_user, $_conf)
{
    global $_mods;

    // Generate our output
    jrCore_page_admin_tabs($module, 'info');

    $murl = jrCore_get_module_url($module);
    $_mta = jrCore_module_meta_data($module);
    $temp = '';
    if (!jrCore_module_is_active($module) && (!isset($_mta['locked']) || intval($_mta['locked']) != 1) && (!is_dir(APP_DIR . "/modules/{$module}") || is_writable(APP_DIR . "/modules/{$module}"))) {
        $temp = jrCore_page_button('delete-module', 'delete module', "jrCore_confirm('Delete Module?', 'Are you sure you want to delete this module from your site?', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/admin_save/info/module_delete=on')} )");
    }

    // Setup our module jumper
    $_mds = array();
    foreach ($_mods as $mod_dir => $_info) {
        $_mds[] = $mod_dir;
    }
    $temp .= jrCore_get_module_jumper('module_jumper', $_post['module'], "jrCore_window_location('{$_conf['jrCore_base_url']}/'+ $(this).val() + '/admin/info')", $_mds);

    if (!jrCore_module_is_active($module) && file_exists(APP_DIR . "/modules/{$module}/include.php")) {
        // We have to bring in our include...
        require_once APP_DIR . "/modules/{$module}/include.php";
    }
    jrCore_page_banner($_mta['name'], $temp);

    // See if we exist
    if (!is_dir(APP_DIR . "/modules/{$module}")) {
        jrCore_page_notice('error', 'Unable to find module files - re-install or delete from system');
        jrCore_page_set_no_header_or_footer();
        return jrCore_page_display(true);
    }
    // See if we are locked
    elseif (isset($_mta['locked']) && $_mta['locked'] == '1') {
        jrCore_set_form_notice('notice', 'This module is an integral part of the Core system and cannot be disabled or removed');
    }
    // See if we are disabled
    elseif (!jrCore_module_is_active($module)) {
        jrCore_set_form_notice('error', 'This module is currently disabled');
    }

    jrCore_get_form_notice();

    // Show information about this module
    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');
    $_opt = array('description', 'version', 'requires', 'recommended', 'developer', 'license');
    $icon = jrCore_get_module_icon_html($module, 164);
    $temp = '<div id="info_box"><table><tr><td class="item" style="width:10%">' . $icon . '</td>';
    $_tmp = array();
    $sact = true;
    foreach ($_opt as $k => $key) {

        $text = '';
        switch ($key) {

            case 'requires':
                $text = '';
                if (isset($_mta['requires']{0})) {
                    $_mrq = array();
                    $_req = explode(',', $_mta[$key]);
                    foreach ($_req as $rmod) {
                        $rmod = trim($rmod);
                        $rver = false;
                        if (strpos($rmod, ':')) {
                            list($rmod, $rver) = explode(':', $rmod, 2);
                            $rmod = trim($rmod);
                            $rver = trim($rver);
                        }
                        // Module is installed and active
                        if (jrCore_module_is_active($rmod) && !$rver) {
                            $_mrq[] = $pass . '&nbsp;' . $_mods[$rmod]['module_name'];
                        }
                        // Module is installed and active - version is good
                        elseif (jrCore_module_is_active($rmod) && $rver && version_compare($_mods[$rmod]['module_version'], $rver) !== -1) {
                            $_mrq[] = $pass . '&nbsp;' . $_mods[$rmod]['module_name'] . ' ' . $rver;
                        }
                        // Module is installed and active - version is too low
                        elseif (jrCore_module_is_active($rmod) && $rver && version_compare($_mods[$rmod]['module_version'], $rver) === -1) {
                            $_mrq[] = $fail . '&nbsp;<a href="' . $_conf['jrCore_base_url'] . '/' . $_mods['jrMarket']['module_url'] . '/system_update" style="text-decoration:underline;">' . $_mods[$rmod]['module_name'] . '&nbsp;version ' . $rver . '</a>';
                            $sact   = false;
                        }
                        elseif (isset($_mods[$rmod])) {
                            $_mrq[] = $fail . '&nbsp;<a href="' . $_conf['jrCore_base_url'] . '/' . $_mods[$rmod]['module_url'] . '/admin/info" style="text-decoration:underline;">' . $_mods[$rmod]['module_name'] . '</a>';
                            $sact   = false;
                        }
                        else {
                            $_mrq[] = $fail . '&nbsp;<a href="' . $_conf['jrCore_base_url'] . '/' . $_mods['jrMarket']['module_url'] . '/browse/module/search_string=' . $rmod . '" style="text-decoration:underline;">' . $rmod . '</a>';
                            $sact   = false;
                        }
                    }
                    $text = implode(' &nbsp; ', $_mrq);
                }
                break;

            case 'recommended':
                $text = '';
                if (isset($_mta['recommended']{0})) {
                    $_mrc = array();
                    $_req = explode(',', $_mta[$key]);
                    foreach ($_req as $rmod) {
                        $rmod = trim($rmod);
                        // Module is installed and active
                        if (jrCore_module_is_active($rmod)) {
                            $_mrc[] = $_mods[$rmod]['module_name'];
                        }
                        else {
                            $_mrc[] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_mods['jrMarket']['module_url'] . '/browse/module/search_string=' . $rmod . '" style="text-decoration:underline;">' . $rmod . '</a>';
                            $sact   = false;
                        }
                    }
                    $text = implode(' &nbsp; ', $_mrc);
                }
                break;

            case 'license':
                $type = 'Open Source';
                if (isset($_mta['license']) && strtolower($_mta['license']) == 'jcl') {
                    $type = '&#9734; Premium &#9734;';
                }
                $murl = jrCore_get_module_url($module);
                $text = "{$type} &nbsp;&bull;&nbsp; <a href=\"{$_conf['jrCore_base_url']}/{$murl}/license\" onclick=\"popwin('{$_conf['jrCore_base_url']}/{$murl}/license','license',800,500,'yes');return false\"><span style=\"text-decoration:underline;\">View License</span></a>";
                break;

            default:
                if (isset($_mta[$key]) && strlen($_mta[$key]) > 0) {
                    $text = $_mta[$key];
                }
                break;
        }

        if (strlen($text) > 0) {
            $_tmp[] = '<strong>' . $key . ':</strong> <span>' . $text . "</span>";
        }
    }

    // Module Directory
    $_tmp[] = '<strong>directory:</strong> <span>' . $module . "</span>";

    // See if this module has a readme associated with it
    if (is_file(APP_DIR . "/modules/{$module}/readme.html")) {
        $text   = "<a href=\"{$_conf['jrCore_base_url']}/modules/{$module}/readme.html\" onclick=\"popwin('{$_conf['jrCore_base_url']}/modules/{$module}/readme.html','readme',800,500,'yes');return false\"><span style=\"text-decoration:underline;\">Click to View Module Notes</span></a>";
        $_tmp[] = '<strong>notes:</strong> <span>' . $text . "</span>";
    }

    $temp .= '<td class="item" style="width:90%">' . implode('<br>', $_tmp) . '</td></tr>';
    $temp .= '</table></div>';
    jrCore_page_custom($temp);

    jrCore_page_section_header('module settings');

    // Module settings
    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'action'       => 'admin_save/info'
    );
    jrCore_form_create($_tmp);

    // Module URL
    if (!isset($_mta['url_change']) || $_mta['url_change'] !== false) {
        $_tmp = array(
            'name'     => 'new_module_url',
            'label'    => 'module URL',
            'help'     => "The Module URL setting determines the URL the module will be accessed at - i.e. {$_conf['jrCore_base_url']}/<strong>{$_mods[$module]['module_url']}</strong>/",
            'type'     => 'text',
            'value'    => $_mods[$module]['module_url'],
            'validate' => 'url_name'
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        jrCore_page_custom($_mta['url'], 'module URL');
    }

    // Module Category
    $_tmp = array(
        'name'     => 'new_module_category',
        'label'    => 'module category',
        'help'     => "If you would like to change the category for this module, enter a new category here.<br><br><strong>NOTE:</strong> Category name must consist of letters, numbers and spaces only.",
        'type'     => 'text',
        'value'    => $_mods[$module]['module_category'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Module Active
    if (!isset($_mta['locked']) || $_mta['locked'] != '1') {
        if ($sact) {
            $_mr = false;
            $act = 'on';
            $tag = 'disable';
            $chk = "unchecking";
            if (!jrCore_module_is_active($module)) {
                $act = 'off';
                $tag = 'enable';
                $chk = "checking";
            }
            else {
                // We cannot deactivate ourselves if other modules depend on us
                $_mr = array();
                foreach ($_mods as $md => $_mi) {
                    if (isset($_mi['module_requires']) && strlen($_mi['module_requires']) > 0 && $_mi['module_active'] == '1' && ($_mi['module_requires'] == $module || strpos(' ' . $_mi['module_requires'], "{$module}:") || strpos(' ' . $_mi['module_requires'] . ',', "{$module},"))) {
                        $_mr[] = "<a href=\"{$_conf['jrCore_base_url']}/" . jrCore_get_module_url($md) . "/admin/info\">{$_mi['module_name']}</a>";
                    }
                }
            }
            if ($_mr && count($_mr) > 0) {
                jrCore_page_notice('notice', $_mta['name'] . " is required by the following modules and cannot be disabled:<br><strong>" . implode('<br>', $_mr) . "</strong>", false);
            }
            else {
                $_tmp = array(
                    'name'     => 'module_active',
                    'label'    => 'module active',
                    'help'     => "You can <strong>{$tag}</strong> this module by {$chk} this option and saving.",
                    'type'     => 'checkbox',
                    'value'    => $act,
                    'validate' => 'onoff'
                );
                jrCore_form_field_create($_tmp);
            }
        }
        else {
            if (isset($_mrc) && count($_mrc) > 0) {
                jrCore_page_notice('success', 'The following modules are recommended by this module:<br>' . implode('<br>', $_mrc), false);
            }
            elseif (isset($_mrq) && count($_mrq) > 0) {
                jrCore_page_notice('error', 'This module will not function properly until the following modules are installed and active:<br>' . implode('<br>', $_mrq), false);
            }
            else {
                jrCore_page_notice('error', 'This module has required dependencies that are not met');
            }

            $act = 'on';
            $tag = 'disable';
            if (!jrCore_module_is_active($module)) {
                $act = 'off';
                $tag = 'enable';
            }
            $_tmp = array(
                'name'     => 'module_active',
                'label'    => 'module active',
                'help'     => "You can <strong>{$tag}</strong> this module by checking this option and saving.",
                'type'     => 'checkbox',
                'value'    => $act,
                'validate' => 'onoff'
            );
            jrCore_form_field_create($_tmp);
        }
    }

    jrCore_form_submit('save changes', false);

    // See if we are showing developer information
    if (jrCore_is_developer_mode()) {

        // EVENTS

        // First - get any event triggers we are providing
        $_tmp = jrCore_get_flag('jrcore_event_triggers');
        $_out = array();
        if ($_tmp && is_array($_tmp)) {
            foreach ($_tmp as $k => $v) {
                if (strpos($k, "{$module}_") === 0) {
                    $name        = str_replace("{$module}_", '', $k);
                    $_out[$name] = array('desc' => $v);
                }
            }
        }

        // Next, find out how many listeners we have
        if (count($_out) > 0) {
            $_tmp = jrCore_get_flag('jrcore_event_listeners');
            if ($_tmp && is_array($_tmp)) {
                foreach ($_tmp as $k => $v) {
                    if (strpos($k, "{$module}_") === 0) {
                        $name                     = str_replace("{$module}_", '', $k);
                        $_out[$name]['listeners'] = implode('<br>', $v);
                    }
                }
            }
        }

        if (count($_out) > 0) {
            ksort($_out);

            jrCore_page_spacer();
            jrCore_page_notice('success', 'The ' . $_mods[$module]['module_name'] . ' module provides the following events:');

            $dat             = array();
            $dat[1]['title'] = 'event name';
            $dat[1]['width'] = '16%';
            $dat[2]['title'] = 'description';
            $dat[2]['width'] = '56%';
            $dat[3]['title'] = 'listeners';
            $dat[3]['width'] = '28%';
            jrCore_page_table_header($dat);

            foreach ($_out as $event => $_params) {
                $dat             = array();
                $dat[1]['title'] = $event;
                $dat[1]['class'] = 'center';
                $dat[2]['title'] = (isset($_params['desc'])) ? $_params['desc'] : '&nbsp;';
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = (isset($_params['listeners'])) ? $_params['listeners'] : '&nbsp;';
                $dat[3]['class'] = 'center';
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
        }
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Generate "Bigview" dashboard view
 * @param $_post array posted info
 * @param $_user array viewing user info
 * @param $_conf array global config
 */
function jrCore_dashboard_bigview($_post, $_user, $_conf)
{
    global $_mods;
    // See what our layout is
    $_cfg = false;
    $rows = 2;
    $cols = 4;
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_cfg = json_decode($_conf['jrCore_dashboard_config'], true);
        if (isset($_cfg['rows']) && jrCore_checktype($_cfg['rows'], 'number_nz')) {
            $rows = (int) $_cfg['rows'];
        }
        if (isset($_cfg['cols']) && jrCore_checktype($_cfg['cols'], 'number_nz')) {
            $cols = (int) $_cfg['cols'];
        }
    }

    // Our default panel setup
    $_def = array(
        0 => array(
            0 => array(
                't' => 'total profiles',
                'f' => 'jrProfile_dashboard_panels'
            ),
            1 => array(
                't' => 'signups today',
                'f' => 'jrProfile_dashboard_panels'
            ),
            2 => array(
                't' => 'users online',
                'f' => 'jrUser_dashboard_panels'
            ),
            3 => array(
                't' => 'queue depth',
                'f' => 'jrCore_dashboard_panels'
            )
        ),
        1 => array(
            0 => array(
                't' => 'memory used',
                'f' => 'jrCore_dashboard_panels'
            ),
            1 => array(
                't' => 'disk usage',
                'f' => 'jrCore_dashboard_panels'
            ),
            2 => array(
                't' => 'CPU count',
                'f' => 'jrCore_dashboard_panels'
            ),
            3 => array(
                't' => '5 minute load',
                'f' => 'jrCore_dashboard_panels'
            )
        )
    );
    foreach ($_def as $row => $_cols) {
        foreach ($_cols as $col => $_inf) {
            if (!isset($_cfg['_panels'][$row][$col])) {
                $_cfg['_panels'][$row][$col] = $_inf;
            }
        }
    }
    ksort($_cfg['_panels'], SORT_NUMERIC);

    // Get registered Graph functions
    $_tmp = jrCore_get_registered_module_features('jrGraph', 'graph_config');
    $_url = array();
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $mod => $_fnc) {
            foreach ($_fnc as $name => $_inf) {
                $_url[$name] = jrCore_get_module_url($mod);
            }
        }
    }

    $_html = array();
    $_func = array();
    $width = round((100 / $cols), 2);
    for ($r = 0; $r < $rows; $r++) {
        $dat = array();
        for ($c = 0; $c < $cols; $c++) {
            $dat[$c]['title'] = '';
            if (isset($_cfg['_panels'][$r][$c])) {
                $ttl = $_cfg['_panels'][$r][$c]['t'];
                if (strpos($_cfg['_panels'][$r][$c]['t'], 'item count')) {
                    $mod = trim(jrCore_string_field($_cfg['_panels'][$r][$c]['t'], 1));
                    if (isset($_mods[$mod])) {
                        $ttl = $_mods[$mod]['module_name'] . ' count';
                    }
                }
                $dat[$c]['title'] = '<div class="bignum_stat_cell">' . $ttl;
                $fnc              = $_cfg['_panels'][$r][$c]['f'];
                if (function_exists($fnc)) {
                    $_func[$r][$c] = $fnc($_cfg['_panels'][$r][$c]['t']);
                    $out           = $_func[$r][$c];
                    if (isset($out['graph']) && !jrCore_is_mobile_device()) {
                        $id = "g{$r}{$c}";
                        if (strpos($out['graph'], '/')) {
                            list($mu,) = explode('/', $out['graph'], 2);
                            $mu = $_url[$mu];
                        }
                        else {
                            $mu = $_url["{$out['graph']}"];
                        }
                        if (strlen($mu) > 0) {
                            $_html[] = "<div id=\"{$id}\" style=\"width:750px;height:400px;display:none;bottom:0;\"></div>";
                            if (jrCore_module_is_active('jrGraph')) {
                                $dat[$c]['title'] .= "<div class=\"bignum_stat\"><a href=\"{$_conf['jrCore_base_url']}/{$mu}/graph/{$out['graph']}\" onclick=\"jrCore_dashboard_disable_reload(60);jrGraph_modal_graph('#{$id}', '{$mu}', '{$out['graph']}', 'modal'); return false\">" . jrCore_get_icon_html('stats', 16) . '</a></div>';
                            }
                        }
                    }
                }
                $dat[$c]['title'] .= '</div>';
            }
            $dat[$c]['width'] = "{$width}%";
        }
        jrCore_page_table_header($dat, 'bigtable');

        $dat = array();
        for ($c = 0; $c < $cols; $c++) {
            if (isset($_cfg['_panels'][$r][$c])) {
                $out = false;
                if (isset($_func[$r][$c])) {
                    $out = $_func[$r][$c];
                    if ($out && is_array($out)) {
                        $dat[$c]['title'] = $out['title'];
                        if (isset($out['class'])) {
                            $dat[$c]['class'] = "bignum bignum" . ($c + 1) . " {$out['class']}";
                        }
                        else {
                            $dat[$c]['class'] = "bignum bignum" . ($c + 1);
                        }
                    }
                }
                if (!$out) {
                    $dat[$c]['title'] = '!';
                    $dat[$c]['class'] = "bignum bignum" . ($c + 1) . ' error';
                }
            }
            else {
                $dat[$c]['title'] = '?';
                $dat[$c]['class'] = "bignum bignum" . ($c + 1);
            }
            $dat[$c]['class'] .= "\" id=\"id-{$r}-{$c}";
        }
        jrCore_page_table_row($dat);
        jrCore_page_table_footer();
    }

    $html = jrCore_parse_template('dashboard_panels.tpl', array(), 'jrCore');
    jrCore_page_custom($html);

    if (is_array($_html)) {
        jrCore_page_custom(implode("\n", $_html));
    }

    if (jrUser_is_master()) {
        $_tmp = array("$('.bignum').click(function(e) { e.stopPropagation(); jrCore_dashboard_disable_reload(60); jrCore_dashboard_panel($(this).attr('id')); return false });");
        jrCore_create_page_element('javascript_ready_function', $_tmp);
    }
}

/**
 * Show Pending Items Dashboard view
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 */
function jrCore_dashboard_pending($_post, $_user, $_conf)
{
    // Get our pending items
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "SELECT * FROM {$tbl} WHERE pending_linked_item_id = 0 AND pending_module = '{$_post['m']}'";
    $_ex = false;
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_post['search_string'] = trim(urldecode($_post['search_string']));
        $str                    = jrCore_db_escape($_post['search_string']);
        $req                    .= " AND pending_data LIKE '%{$str}%'";
        $_ex                    = array('search_string' => $_post['search_string']);
    }
    $req .= ' ORDER BY pending_id ASC';

    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    // start our html output
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/pending/m={$_post['m']}");

    $dat = array();
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.pending_checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
    }
    else {
        $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" disabled>';
    }
    $dat[1]['width'] = '1%;';
    $dat[2]['title'] = 'date';
    $dat[2]['width'] = '5%;';
    $dat[3]['title'] = 'item';
    $dat[3]['width'] = '45%;';
    $dat[4]['title'] = 'profile';
    $dat[4]['width'] = '10%;';
    $dat[5]['title'] = 'user';
    $dat[5]['width'] = '10%;';
    $dat[6]['title'] = 'approve';
    $dat[6]['width'] = '3%;';
    $dat[7]['title'] = 'reject';
    $dat[7]['width'] = '3%;';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '3%;';
    jrCore_page_table_header($dat);
    unset($dat);

    $url = jrCore_get_module_url('jrCore');
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        foreach ($_rt['_items'] as $_pend) {
            $_data           = json_decode($_pend['pending_data'], true);
            $murl            = jrCore_get_module_url($_pend['pending_module']);
            $mpfx            = jrCore_db_get_prefix($_pend['pending_module']);
            $dat             = array();
            $dat[1]['title'] = '<input type="checkbox" class="form_checkbox pending_checkbox" name="' . $_pend['pending_id'] . '">';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrCore_format_time($_pend['pending_created']);
            $dat[2]['class'] = 'center';
            if (isset($_data['item']["{$mpfx}_title"]) && strlen($_data['item']["{$mpfx}_title"]) > 0) {
                $title = $_data['item']["{$mpfx}_title"];
            }
            else {
                $title = "{$_data['user']['profile_url']}/{$murl}/{$_pend['pending_item_id']}";
            }
            $dat[3]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$murl}/update/id={$_pend['pending_item_id']}\" target=\"_blank\">{$title}</a>";
            if (isset($_data['item']["{$mpfx}_pending_reason"])) {
                $dat[3]['title'] .= '<br><small>' . $_data['item']["{$mpfx}_pending_reason"] . '</small>';
            }
            $dat[4]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_data['user']['profile_url']}\">@{$_data['user']['profile_name']}</a>";
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = $_data['user']['user_name'];
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("a{$_pend['pending_id']}", 'approve', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_approve/{$_pend['pending_module']}/id={$_pend['pending_item_id']}')");
            $dat[7]['title'] = jrCore_page_button("r{$_pend['pending_id']}", 'reject', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_reject/{$_pend['pending_module']}/id={$_pend['pending_item_id']}')");
            $dat[8]['title'] = jrCore_page_button("d{$_pend['pending_id']}", 'delete', "jrCore_confirm('Delete this item?', 'No notification will be sent', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_delete/{$_pend['pending_module']}/id={$_pend['pending_item_id']}')})");
            jrCore_page_table_row($dat);
        }

        $sjs = "var v = $('input:checkbox.pending_checkbox:checked').map(function(){ return this.name; }).get().join(',')";
        $tmp = jrCore_page_button("all", 'approve checked', "{$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_approve/all/id=,'+ v)");
        $tmp .= '&nbsp;' . jrCore_page_button("delete", 'delete checked', "jrCore_confirm('Delete checked items?', '', function(){ {$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_delete/all/id='+ v )})");

        $dat             = array();
        $dat[1]['title'] = $tmp;
        jrCore_page_table_row($dat);

        jrCore_page_table_pager($_rt, $_ex);
    }
    else {
        $dat = array();
        if (!empty($_post['search_string'])) {
            $dat[1]['title'] = '<p>There were no Pending Items found to match your search criteria</p>';
        }
        else {
            $dat[1]['title'] = '<p>There are no pending items to show</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
}

/**
 * Display DS Browser
 * @param $mode string dashboard|admin where browser is being run from
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrCore_dashboard_browser($mode, $_post, $_user, $_conf)
{
    global $_mods;

    // Get modules that have registered a custom datastore browser
    $add = '';
    if (jrUser_is_master()) {
        $url = jrCore_get_module_url('jrCore');
        $url = "{$_conf['jrCore_base_url']}/{$url}/export_datastore_csv/m={$_post['module']}";
        $add .= jrCore_page_button('export', 'Export CSV', "jrCore_confirm('Download DataStore Contents?', 'Please be patient - the generated CSV file could be large and take a bit to create', function(){ jrCore_window_location('{$url}') })");
    }

    $url  = jrCore_get_current_url();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'data_browser');
    if (isset($_tmp["{$_post['module']}"])) {
        if (!isset($_post['vk'])) {
            $add .= jrCore_page_button('raw', 'view keys', "jrCore_window_location('{$url}/vk=true')");
        }
        else {
            $url = jrCore_strip_url_params($url, array('vk'));
            $add .= jrCore_page_button('raw', 'view browser', "jrCore_window_location('{$url}')");
        }
    }

    // Create a Quick Jump list for custom forms for this module
    $_mds = array();
    foreach ($_mods as $mod_dir => $_inf) {
        if (!jrCore_module_is_active($mod_dir) || $mod_dir == 'jrCore' || $mod_dir == 'jrSeamless') {
            continue;
        }
        if ($pfx = jrCore_db_get_prefix($mod_dir)) {
            $_mds[] = $mod_dir;
        }
    }
    $add .= jrCore_get_module_jumper('data_browser', $_post['module'], "jrCore_window_location('{$_conf['jrCore_base_url']}/'+ $(this).val() +'/dashboard/browser')", $_mds);

    $val = '';
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $val = $_post['search_string'];
    }

    jrCore_page_banner($_mods["{$_post['module']}"]['module_name'], $add);
    jrCore_get_form_notice();
    $vk = '';
    if (isset($_tmp["{$_post['module']}"]) && isset($_post['vk'])) {
        $vk = '/vk=true';
    }
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/browser{$vk}", $val);

    // See if this module has registered it's own Browser
    if (isset($_tmp["{$_post['module']}"]) && !isset($_post['vk'])) {
        $func = array_keys($_tmp["{$_post['module']}"]);
        $func = (string) reset($func);
        if (function_exists($func)) {
            $func($_post, $_user, $_conf);
        }
        else {
            jrCore_page_notice('error', "invalid custom browser function defined for {$_post['module']}");
        }
    }
    else {

        // get our items
        $_pr = array(
            'pagebreak'                    => (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 6,
            'page'                         => 1,
            'order_by'                     => array(
                '_item_id' => 'desc'
            ),
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true,
            'ignore_pending'               => true,
            'quota_check'                  => false,
            'privacy_check'                => false
        );
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $_pr['page'] = (int) $_post['p'];
        }
        // See we have a search condition
        $_ex = false;
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $_ex = array('search_string' => $_post['search_string']);
            // Check for passing in a specific key name for search
            if (strpos($_post['search_string'], ':')) {
                list($sf, $ss) = explode(':', $_post['search_string'], 2);
                $ss                     = trim($ss);
                $_post['search_string'] = $ss;
                if (is_numeric($ss)) {
                    $_pr['search'][] = "{$sf} = {$ss}";
                }
                else {
                    $_pr['search'][] = "{$sf} like {$ss}%";
                }
            }
            else {
                $_pr['search'][] = "% like {$_post['search_string']}";
            }
        }
        $_us = jrCore_db_search_items($_post['module'], $_pr);

        // See if we have detail pages for this module
        $view = false;
        if (is_file(APP_DIR . "/modules/{$_post['module']}/templates/item_detail.tpl")) {
            $view = true;
        }

        // Start our output
        $dat             = array();
        $dat[1]['title'] = 'ID';
        $dat[1]['width'] = '5%';
        $dat[2]['title'] = 'item data';
        $dat[2]['width'] = '78%';
        $dat[3]['title'] = 'action';
        $dat[3]['width'] = '2%';
        jrCore_page_table_header($dat);

        if (isset($_us['_items']) && is_array($_us['_items'])) {
            foreach ($_us['_items'] as $_itm) {
                $dat = array();
                switch ($_post['module']) {
                    case 'jrUser':
                        $iid = $_itm['_user_id'];
                        break;
                    case 'jrProfile':
                        $iid = $_itm['_profile_id'];
                        break;
                    default:
                        $iid = $_itm['_item_id'];
                        break;
                }
                $pfx             = jrCore_db_get_prefix($_post['module']);
                $dat[1]['title'] = $iid;
                $dat[1]['class'] = 'center';
                $_tm             = array();
                ksort($_itm);
                $master_user = false;
                $admin_user  = false;
                $_rep        = array("\n", "\r", "\n\r");
                foreach ($_itm as $k => $v) {
                    if (strpos($k, $pfx) !== 0) {
                        continue;
                    }
                    switch ($k) {
                        case '_user_id':
                        case '_profile_id':
                        case '_item_id':
                        case 'user_password':
                        case 'user_old_password':
                        case 'user_validate':
                            break;
                        /** @noinspection PhpMissingBreakStatementInspection */
                        case 'user_group':
                            switch ($v) {
                                case 'master':
                                    $master_user = true;
                                    break;
                                case 'admin':
                                    $admin_user = true;
                                    break;
                            }
                        // We fall through on purpose!
                        default:
                            if (isset($v) && is_array($v)) {
                                $v = json_encode($v);
                            }
                            if (is_numeric($v) && strlen($v) === 10) {
                                $v = jrCore_format_time($v);
                            }
                            else {
                                $v = strip_tags(str_replace($_rep, ' ', $v));
                            }
                            if (strlen($v) > 80) {
                                $v = substr($v, 0, 80) . '...';
                            }
                            if (isset($_post['search_string'])) {
                                // See if we are searching a specific field
                                if (isset($sf)) {
                                    if ($k == $sf) {
                                        $v = jrCore_hilight_string($v, str_replace('%', '', $_post['search_string']));
                                    }
                                }
                                else {
                                    $v = jrCore_hilight_string($v, str_replace('%', '', $_post['search_string']));
                                }
                            }
                            $_tm[] = "<span class=\"ds_browser_key\">{$k}:</span> <span class=\"ds_browser_value\">{$v}</span>";
                            break;
                    }
                }
                $dat[3]['title'] = '<div class="ds_browser_item">' . implode('<br>', $_tm) . '</div>';
                $_att            = array(
                    'style' => 'width:70px;'
                );

                $_btn = array();
                if ($view && isset($_itm["{$pfx}_title_url"])) {
                    $url    = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$_post['module_url']}/{$iid}/{$_itm["{$pfx}_title_url"]}";
                    $_btn[] = jrCore_page_button("v{$iid}", 'view', "jrCore_window_location('{$url}')", $_att);
                }

                $url    = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_update/id={$iid}";
                $_btn[] = jrCore_page_button("m{$iid}", 'modify', "jrCore_window_location('{$url}')", $_att);

                // Check and see if we are browsing User Accounts - if so, admin users cannot delete
                // admin or master accounts.  Master cannot delete other master accounts.
                $add = false;
                if (jrUser_is_master() && !$master_user) {
                    $add = true;
                }
                elseif (jrUser_is_admin() && !$master_user && !$admin_user) {
                    $add = true;
                }
                if ($add) {
                    $_btn[] = jrCore_page_button("d{$iid}", 'delete', "jrCore_confirm('Delete Item?', 'The item will be permanently deleted!', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_delete/id={$iid}') })", $_att);
                }
                $dat[4]['title'] = implode('<br><br>', $_btn);
                $dat[4]['class'] = 'center';
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_pager($_us, $_ex);
        }
        else {
            $dat = array();
            if (isset($_post['search_string'])) {
                $dat[1]['title'] = '<p>No Results found for your Search Criteria.</p>';
            }
            else {
                $dat[1]['title'] = '<p>No Items found in DataStore!</p>';
            }
            $dat[1]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }
    return true;
}

/**
 * Queue Viewer
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrCore_dashboard_queue_viewer($_post, $_user, $_conf)
{
    global $_mods;
    $buttons = '';
    if (jrUser_is_master()) {
        if (!jrCore_queues_are_active()) {
            $buttons .= jrCore_page_button('pause', 'resume all queues', "jrCore_confirm('Resume Queue Workers?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/queue_pause/on') })");
        }
        else {
            $buttons .= jrCore_page_button('pause', 'pause all queues', "jrCore_confirm('Pause Queue Workers?', 'Any existing workers will finish their current job and exit', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/queue_pause/off') })");
        }
    }
    $buttons .= jrCore_page_button('refresh', 'refresh', "location.reload();");

    $add = '';
    if (isset($_post['queue_name']) && strlen($_post['queue_name']) > 0) {
        $add     = ': <span style="text-transform:none">' . $_post['queue_name'] . '</span>';
        $buttons .= jrCore_page_button('all', 'all queues', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/queue_viewer')");
    }
    jrCore_page_banner("Queue Viewer{$add}", $buttons);

    if (!jrCore_queues_are_active()) {
        jrCore_set_form_notice('error', 'All Queues are currently <strong>PAUSED</strong> - press the &quot;Resume All Queues&quot; button to start the Queue workers.<br>New queue jobs will not be executed while Queues are paused', false);
    }
    if (!isset($_post['queue_name']) || strlen($_post['queue_name']) === 0) {
        jrCore_set_form_notice('success', 'Queue Workers are background processes that perform queued tasks<br>such as media conversion, notifications, cache cleanup, system backups, etc.', false);
    }
    jrCore_get_form_notice();

    // Queue Counts
    if (isset($_post['queue_name']) && strlen($_post['queue_name']) > 0) {

        // SPECIFIC QUEUE

        $page = 1;
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $page = (int) $_post['p'];
        }
        $mod = jrCore_db_escape($_post['queue_module']);
        $nam = jrCore_db_escape($_post['queue_name']);

        $num = 0;
        $tbl = jrCore_db_table_name('jrCore', 'queue_info');
        $req = "SELECT queue_depth FROM {$tbl} WHERE queue_name = '{$mod}_{$nam}'";
        $_cq = jrCore_db_query($req, 'SINGLE');
        if ($_cq && is_array($_cq) && isset($_cq['queue_depth'])) {
            $num = (int) $_cq['queue_depth'];
        }
        $tb1 = jrCore_db_table_name('jrCore', 'queue');
        $tb2 = jrCore_db_table_name('jrCore', 'queue_data');
        $req = "SELECT q.*, d.queue_data, UNIX_TIMESTAMP() AS queue_time FROM {$tb1} q JOIN {$tb2} d ON (d.queue_id = q.queue_id) WHERE q.queue_module = '{$mod}' AND q.queue_name = '{$nam}' ORDER BY q.queue_sleep ASC";
        $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC', $num);

        $dat = array();
        if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
            $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.tk_checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
        }
        else {
            $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" disabled>';
        }
        $dat[1]['width'] = '2%';
        $dat[2]['title'] = 'queue ID';
        $dat[2]['width'] = '5%';
        $dat[3]['title'] = 'worker ID';
        $dat[3]['width'] = '5%';
        $dat[4]['title'] = 'created';
        $dat[4]['width'] = '5%';
        $dat[5]['title'] = 'queue data';
        $dat[5]['width'] = '66%';
        $dat[6]['title'] = 'status';
        $dat[6]['width'] = '6%';
        $dat[7]['title'] = 'latency';
        $dat[7]['width'] = '6%';
        $dat[8]['title'] = 'reset';
        $dat[8]['width'] = '5%';
        $dat[9]['title'] = 'delete';
        $dat[9]['width'] = '5%';
        jrCore_page_table_header($dat);

        if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
            foreach ($_rt['_items'] as $k => $v) {
                $dat             = array();
                $dat[1]['title'] = '<input type="checkbox" class="form_checkbox tk_checkbox" name="' . $v['queue_id'] . '">';
                $dat[2]['title'] = $v['queue_id'];
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = (strlen($v['queue_worker']) > 0) ? $v['queue_worker'] : '-';
                $dat[3]['class'] = 'center word-break';
                $dat[4]['title'] = jrCore_format_time($v['queue_created']);
                $dat[4]['class'] = 'center';
                $dat[5]['title'] = '<div class="p5 fixed-width" style="max-height:100px;word-break:break-all;overflow:auto">' . print_r(json_decode($v['queue_data'], true), true) . '</div>';
                $cls             = '';
                $val             = (time() - $v['queue_sleep']);
                if ($val > 3600) {
                    $cls = 'log-cri ';
                }
                elseif ($val > 1200) {
                    $cls = 'log-maj ';
                }
                elseif ($val > 300) {
                    $cls = 'log-min ';
                }
                if (strlen($v['queue_worker']) > 0) {
                    $dat[6]['title'] = 'Working<br><small>' . jrCore_number_format(time() - $v['queue_started']) . ' s</small>';
                    $dat[6]['class'] = 'success center';
                    $dat[7]['title'] = '-';
                    $dat[7]['title'] = '-';
                    $cls             = '';
                }
                elseif ($v['queue_sleep'] > $v['queue_time']) {
                    $dat[6]['title'] = 'Sleeping<br><small>' . jrCore_number_format($v['queue_sleep'] - $v['queue_time']) . ' s</small>';
                    $dat[6]['class'] = 'center';
                    $dat[7]['title'] = '-';
                }
                else {
                    $dat[6]['title'] = 'Pending';
                    $dat[6]['class'] = 'center';
                    $dat[7]['title'] = jrCore_number_format((time() - $v['queue_sleep'])) . ' s';
                }
                if ($v['queue_count'] > 0) {
                    $dat[6]['title'] .= '<br><small>Attempts: ' . $v['queue_count'] . '</small>';
                }
                $dat[7]['class'] = $cls . ' center" style="text-align:center';
                if (strlen($v['queue_worker']) > 0 || $v['queue_sleep'] > $v['queue_time']) {
                    $dat[8]['title'] = jrCore_page_button("r{$v['queue_id']}", 'reset', "jrCore_confirm('Reset Queue Entry?', 'The queue entry can then be worked by a new Queue Worker', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/queue_entry_reset/id={$v['queue_id']}') })");
                }
                else {
                    $dat[8]['title'] = jrCore_page_button("r{$v['queue_id']}", 'reset', 'disabled');
                }
                $dat[9]['title'] = jrCore_page_button("d{$v['queue_id']}", 'delete', "jrCore_confirm('Delete Queue Entry?', '', function(){ jrCore_delete_queue_entry('{$v['queue_id']}') })");
                jrCore_page_table_row($dat);
            }
            $sjs             = "var v = $('input:checkbox.tk_checkbox:checked').map(function(){ return this.name; }).get().join(',')";
            $dat             = array();
            $dat[1]['title'] = jrCore_page_button("delete", 'delete checked', "jrCore_confirm('Delete Checked Entries?', '', function(){ {$sjs};jrCore_delete_queue_entry(v) })");
            jrCore_page_table_row($dat);
            jrCore_page_table_pager($_rt);
        }
        else {
            $dat             = array();
            $dat[1]['title'] = '<p>There are no queue entries in this queue</p>';
            $dat[1]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
        jrCore_page_cancel_button('referrer');

    }
    else {

        // ALL QUEUE ENTRIES

        // Worker Count
        $tbl = jrCore_db_table_name('jrCore', 'queue_info');
        $req = "SELECT queue_name AS qn, queue_workers AS qw, queue_depth as qd FROM {$tbl} ORDER BY queue_depth DESC";
        $_qw = jrCore_db_query($req, 'qn', false, null, false, null, false);

        // Reorder so Cluster entries are at bottom
        $ord = false;
        $_cl = array();
        $_lc = array();
        foreach ($_qw as $qname => $v) {
            if (strpos($qname, '_queue_server_')) {
                $_cl[$qname] = $v;
                $ord         = true;
            }
            else {
                if ($v['qd'] > 0) {
                    $_lc[$qname] = $v;
                }
            }
        }
        $_qw = array_merge($_lc, $_cl);

        $dat             = array();
        $dat[1]['title'] = '&nbsp;';
        $dat[1]['width'] = '1%';
        if (count($_lc) === 0) {
            $dat[2]['title'] = 'cluster module';
        }
        elseif ($ord) {
            $dat[2]['title'] = 'local module';
        }
        else {
            $dat[2]['title'] = 'queue module';
        }
        $dat[2]['width'] = '22%';
        $dat[3]['title'] = 'queue name';
        $dat[3]['width'] = '22%';
        $dat[4]['title'] = 'active workers';
        $dat[4]['width'] = '15%';
        $dat[5]['title'] = 'queue entries';
        $dat[5]['width'] = '15%';
        $dat[6]['title'] = 'queue latency';
        $dat[6]['width'] = '15%';
        $dat[7]['title'] = 'view entries';
        $dat[7]['width'] = '5%';
        $dat[8]['title'] = 'empty queue';
        $dat[8]['width'] = '5%';
        jrCore_page_table_header($dat);

        $k = 0;
        if ($_qw && is_array($_qw)) {

            $shw = (count($_lc) > 0) ? false : true;
            $tbl = jrCore_db_table_name('jrCore', 'queue');
            foreach ($_qw as $qname => $v) {

                // Only show Queue if we have entries
                if ($v['qd'] > 0) {

                    list($mod, $nam) = explode('_', $v['qn'], 2);

                    if (!$shw && $ord && strpos($nam, 'queue_server') === 0) {
                        $dat             = array();
                        $dat[1]['title'] = '&nbsp;';
                        $dat[1]['width'] = '1%';
                        $dat[2]['title'] = 'cluster module';
                        $dat[2]['width'] = '22%';
                        $dat[3]['title'] = 'queue name';
                        $dat[3]['width'] = '22%';
                        $dat[4]['title'] = 'active workers';
                        $dat[4]['width'] = '15%';
                        $dat[5]['title'] = 'queue entries';
                        $dat[5]['width'] = '15%';
                        $dat[6]['title'] = 'queue latency';
                        $dat[6]['width'] = '15%';
                        $dat[7]['title'] = 'view entries';
                        $dat[7]['width'] = '5%';
                        $dat[8]['title'] = 'empty queue';
                        $dat[8]['width'] = '5%';
                        jrCore_page_table_header($dat, null, true);
                        $shw = true;
                    }

                    // NOTE: It is faster for us to use individual queries here for each queue rather than
                    // use a GROUP BY in a query that gets all queue entries at once
                    $req = "SELECT MIN(queue_sleep) AS q_min FROM {$tbl} WHERE queue_started = 0 AND queue_module = '{$mod}' AND queue_name = '{$nam}' AND queue_sleep > 0";
                    $_qm = jrCore_db_query($req, 'SINGLE');

                    $dat             = array();
                    $dat[1]['title'] = jrCore_get_module_icon_html($mod, 26);
                    $dat[2]['title'] = (isset($_mods[$mod]['module_name'])) ? $_mods[$mod]['module_name'] : $mod;
                    $dat[2]['class'] = 'center';
                    $dat[3]['title'] = $nam;
                    $dat[3]['class'] = 'center';
                    $dat[4]['title'] = $v['qw'];
                    $dat[4]['class'] = 'center';
                    $dat[5]['title'] = jrCore_number_format($v['qd']);
                    $dat[5]['class'] = 'center';

                    $cls = '';
                    if (isset($v['qw']) && $v['qw'] >= $v['qd']) {
                        // All Queue entries are being worked
                        $dat[6]['title'] = '-';
                    }
                    elseif ($_qm && isset($_qm['q_min']) && $_qm['q_min'] > 0 && $_qm['q_min'] > time()) {
                        $dat[6]['title'] = 'Sleeping<br><small>' . jrCore_number_format($_qm['q_min'] - time()) . ' s</small>';
                    }
                    else {
                        if ($_qm && isset($_qm['q_min']) && $_qm['q_min'] > 0) {
                            $val = (time() - $_qm['q_min']);
                            if ($val > 3600) {
                                $cls = 'log-cri ';
                            }
                            elseif ($val > 1200) {
                                $cls = 'log-maj ';
                            }
                            elseif ($val > 300) {
                                $cls = 'log-min ';
                            }
                            $dat[6]['title'] = jrCore_number_format((time() - $_qm['q_min'])) . ' s';
                        }
                        else {
                            // Q_min is bad
                            $dat[6]['title'] = '?';
                        }
                    }
                    $dat[6]['class'] = $cls . ' center" style="text-align:center';
                    $dat[7]['title'] = jrCore_page_button("view-entries-{$k}", 'view entries', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/queue_viewer/queue_module={$mod}/queue_name={$nam}')");
                    $dat[7]['class'] = $cls . ' center';
                    $dat[8]['title'] = jrCore_page_button("empty-queue-{$k}", 'delete all', "jrCore_confirm('Delete All Entries?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/queue_empty_save/queue_module={$mod}/queue_name={$nam}') } )");
                    $dat[8]['class'] = $cls . ' center';
                    jrCore_page_table_row($dat);
                    $k++;
                }
            }
        }
        if ($k == 0) {
            $dat             = array();
            $dat[1]['title'] = '<p>There are no queue entries to show</p>';
            $dat[1]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }
    return true;
}

/**
 * Show the system Recycle Bin
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrCore_dashboard_recycle_bin($_post, $_user, $_conf)
{
    global $_mods;
    // Get all unique module entries in the Recycle Bin
    $btn = null;
    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    $req = "SELECT r_module FROM {$tbl} WHERE r_group_id = '1' GROUP BY r_module";
    $_md = jrCore_db_query($req, 'r_module', false, 'r_module');
    if ($_md && is_array($_md)) {
        $url = jrCore_strip_url_params(jrCore_get_current_url(), array('p', 'm'));
        $btn = '<select name="recycle_bin_browser" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $url . "/m='+ $(this).val())\">\n";
        if (!isset($_post['m']) || strlen($_post['m']) === 0 || !isset($_mods["{$_post['m']}"])) {
            $btn .= "<option value=\"all\" selected> All Modules</option>\n";
            unset($_post['m']);
        }
        else {
            $btn .= "<option value=\"all\"> All Modules</option>\n";
        }
        $_tmpm = array();
        foreach ($_mods as $mod_dir => $_inf) {
            if (!isset($_md[$mod_dir])) {
                continue;
            }
            if (isset($_inf['module_prefix']) && strlen($_inf['module_prefix']) > 0) {
                $_tmpm[$mod_dir] = $_inf['module_name'];
            }
        }
        asort($_tmpm);
        foreach ($_tmpm as $module => $title) {
            if (isset($_post['m']) && $module == $_post['m']) {
                $btn .= '<option value="' . $module . '" selected> ' . $title . "</option>\n";
            }
            else {
                $btn .= '<option value="' . $module . '"> ' . $title . "</option>\n";
            }
        }
        $btn .= '</select>';
    }

    $btn .= jrCore_page_button('e', 'empty recycle bin', "jrCore_confirm('Empty Recycle Bin?', 'This will permanently delete all items in the Recycle Bin', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/empty_recycle_bin') })");
    jrCore_page_banner('Recycle Bin', $btn);
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/recycle_bin");

    $dat             = array();
    $dat[1]['title'] = 'profile';
    $dat[1]['width'] = '15%';
    $dat[2]['title'] = 'module';
    $dat[2]['width'] = '15%';
    $dat[3]['title'] = 'image';
    $dat[3]['width'] = '5%';
    $dat[4]['title'] = 'item';
    $dat[4]['width'] = '46%';
    $dat[5]['title'] = 'deleted';
    $dat[5]['width'] = '10%';
    $dat[6]['title'] = 'content';
    $dat[6]['width'] = '3%';
    $dat[7]['title'] = 'restore';
    $dat[7]['width'] = '3%';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '3%';
    jrCore_page_table_header($dat);

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }

    $add = '';
    if (isset($_post['m']) && strlen($_post['m'])) {
        $add = " AND r_module = '" . jrCore_db_escape($_post['m']) . "'";
    }
    $sst = '';
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {

        // With a search string we are looking for a PROFILE NAME or an ITEM TITLE
        $_pr = array(
            'search'              => array(
                "profile_name like %{$_post['search_string']}%"
            ),
            'return_item_id_only' => true,
            'skip_triggers'       => true,
            'privacy_check'       => false
        );
        $_pr = jrCore_db_search_items('jrProfile', $_pr);

        $sst = jrCore_db_escape($_post['search_string']);
        if ($_pr && is_array($_pr)) {
            $sst = " AND (r_title LIKE '%{$sst}%' OR r_profile_id IN(" . implode(',', array_keys($_pr)) . "))";
        }
        else {
            $sst = " AND r_title LIKE '%{$sst}%'";
        }
    }

    $req = "SELECT r.* FROM {$tbl} r WHERE (r_module = 'jrProfile' OR r_group_id = '1'){$add}{$sst} ORDER BY r_id DESC";
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        // Get Profile Names
        $_pr = array();
        $_pi = array();
        foreach ($_rt['_items'] as $_r) {
            $pid       = (int) $_r['r_profile_id'];
            $_pi[$pid] = $pid;
        }
        if (count($_pi) > 0) {
            $_tm = jrCore_db_get_multiple_items('jrProfile', $_pi, array('_profile_id', 'profile_url'));
            if ($_tm && is_array($_tm)) {
                foreach ($_tm as $p) {
                    $pid       = (int) $p['_profile_id'];
                    $_pr[$pid] = $p['profile_url'];
                }
            }
        }

        $murl = jrCore_get_module_url('jrImage');
        foreach ($_rt['_items'] as $k => $_r) {

            $dat = array();
            switch ($_r['r_module']) {
                case 'jrUser':
                case 'jrProfile':
                    $dat[1]['title'] = $_r['r_title'];
                    break;
                default:
                    $dat[1]['title'] = (isset($_pr["{$_r['r_profile_id']}"])) ? "<a href=\"{$_conf['jrCore_base_url']}/" . $_pr["{$_r['r_profile_id']}"] . '">@' . $_pr["{$_r['r_profile_id']}"] . '</a>' : '?';
                    break;
            }
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_mods["{$_r['r_module']}"]['module_name'];
            $dat[2]['class'] = 'center';

            $_tmp = json_decode($_r['r_data'], true);
            // Did this item have an image?
            if (strpos($_r['r_data'], '_image_size')) {
                $pfx             = jrCore_db_get_prefix($_r['r_module']);
                $url             = $_conf['jrCore_base_url'] . '/' . $murl . '/rb_image/' . $_r['r_id'] . '/' . $_r['r_module'] . '/' . $pfx . '_image';
                $dat[3]['title'] = '<a href="' . $url . '" data-lightbox="images" title="' . jrCore_entity_string($dat[1]['title']) . '"><img style="max-width:72px" src="' . $url . '">';
            }
            else {
                $dat[3]['title'] = '-';
                $dat[3]['class'] = 'center';
            }

            if ($_r['r_title'] == '?') {
                // If this item does NOT have a title, then it could be a module that
                // adds entries to other items (comments, ratings, likes, etc.) - in
                // this case we want to show the item they are ATTACHED to
                if ($_tmp && is_array($_tmp)) {
                    $pfx = jrCore_db_get_prefix($_r['r_module']);
                    if (isset($_tmp["{$pfx}_title"])) {
                        $dat[4]['title'] = $_tmp["{$pfx}_title"];
                    }
                    elseif (isset($_tmp["{$pfx}_text"])) {
                        if (strlen($_tmp["{$pfx}_text"]) > 140) {
                            $dat[4]['title'] = substr(jrCore_strip_html($_tmp["{$pfx}_text"]), 0, 140) . '...';
                        }
                        else {
                            $dat[4]['title'] = jrCore_strip_html($_tmp["{$pfx}_text"]);
                        }
                    }
                    else {
                        $nam = '';
                        $mod = false;
                        if (isset($_tmp["{$pfx}_module"])) {
                            $mod = $_tmp["{$pfx}_module"];
                            $nam = (isset($_mods[$mod])) ? $_mods[$mod]['module_name'] : $mod;
                        }
                        $iid = false;
                        if (isset($_tmp["{$pfx}_item_id"])) {
                            $iid = (int) $_tmp["{$pfx}_item_id"];
                        }
                        if ($mod && $iid) {
                            $req = "SELECT r_title FROM {$tbl} WHERE r_module = '" . jrCore_db_escape($mod) . "' AND r_item_id = '{$iid}' LIMIT 1";
                            $_in = jrCore_db_query($req, 'SINGLE');
                            if ($_in && isset($_in['r_title'])) {
                                $dat[4]['title'] = $_in['r_title'] . '<br><small>(' . $nam . ')</small>';
                            }
                        }
                    }
                }
                if (!isset($dat[4]['title'])) {
                    $dat[4]['title'] = 'item_id: ' . $_r['r_item_id'] . ' (no title)';
                }
            }
            else {
                $dat[4]['title'] = $_r['r_title'];
            }

            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_format_time($_r['r_time']);
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("rb-content-{$k}", 'content', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/recycle_bin_item_content/id={$_r['r_id']}')");
            $dat[7]['title'] = jrCore_page_button("rb-restore-{$k}", 'restore', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/restore_recycle_bin_item/id={$_r['r_id']}')");
            $dat[8]['title'] = jrCore_page_button("rb-delete-{$_r['r_id']}", 'delete', "jrCore_delete_recyce_bin_entry({$_r['r_id']})");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat = array();
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $dat[1]['title'] = '<p>No items in the Recycle Bin matched your search</p>';
        }
        else {
            $dat[1]['title'] = '<p>There are no items in the Recycle Bin</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return true;
}

/**
 * Show the system Recycle Bin
 * @param $_item array Item to view contents for
 */
function jrCore_view_recycle_bin_content($_item)
{
    global $_conf, $_mods;
    $url = jrCore_get_module_url('jrCore');
    if (jrCore_module_is_active($_item['r_module'])) {
        $ttl = '';
        if (strlen($_item['r_title']) > 2) {
            $ttl = $_item['r_title'];
        }
        jrCore_page_banner('Recycle Bin: ' . $_mods["{$_item['r_module']}"]['module_name'], $ttl);
    }
    else {
        jrCore_page_banner('Recycle Bin Item', 'Unable to Restore - Module is not active!');
    }
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'module';
    $dat[1]['width'] = '17%';
    $dat[2]['title'] = 'profile';
    $dat[2]['width'] = '17%';
    $dat[3]['title'] = 'item';
    $dat[3]['width'] = '60%';
    $dat[4]['title'] = 'media';
    $dat[4]['width'] = '3%';
    $dat[5]['title'] = 'restore';
    $dat[5]['width'] = '3%';
    jrCore_page_table_header($dat);

    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    $req = "SELECT * FROM {$tbl} WHERE r_group_id = '{$_item['r_module']}:{$_item['r_item_id']}' ORDER BY r_group_id ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    if ($_rt && is_array($_rt)) {
        $_rt = array_merge(array($_item), $_rt);
    }
    else {
        $_rt = array($_item);
    }

    // Get Profile Names
    $_pr = array();
    $_pi = array();
    foreach ($_rt as $_r) {
        $_pi[] = (int) $_r['r_profile_id'];
    }
    if (count($_pi) > 0) {
        $_pt = jrCore_db_get_multiple_items('jrProfile', $_pi, array('_profile_id', 'profile_url'));
        if ($_pt && is_array($_pt)) {
            foreach ($_pt as $_p) {
                $_pr["{$_p['_profile_id']}"] = $_p['profile_url'];
            }
        }
    }

    $pass = jrCore_get_option_image('pass');
    foreach ($_rt as $k => $_r) {

        if ($k == 1) {
            $dat             = array();
            $dat[1]['title'] = 'The items below are all related to the item shown above (i.e. ratings, comments, etc.)<br>Restoring the Item will also restore the related items listed below.';
            $dat[1]['class'] = 'p10 center success';
            $dat[1]['width'] = '100%';
            jrCore_page_table_row($dat);
        }

        $dat             = array();
        $dat[1]['title'] = $_mods["{$_r['r_module']}"]['module_name'];
        $dat[1]['class'] = 'center';
        switch ($_r['r_module']) {
            case 'jrUser':
            case 'jrProfile':
                $dat[2]['title'] = $_r['r_title'];
                break;
            default:
                if ($_rt[0]['r_module'] == 'jrProfile' && $_r['r_profile_id'] == $_rt[0]['r_profile_id']) {
                    // This is a deleted profile - link won't work
                    $dat[2]['title'] = (isset($_pr["{$_r['r_profile_id']}"])) ? $_pr["{$_r['r_profile_id']}"] : '?';
                }
                else {
                    $dat[2]['title'] = (isset($_pr["{$_r['r_profile_id']}"])) ? "<a href=\"{$_conf['jrCore_base_url']}/" . $_pr["{$_r['r_profile_id']}"] . '">@' . $_pr["{$_r['r_profile_id']}"] . '</a>' : '?';
                }
                break;
        }
        $dat[2]['class'] = 'center';
        if ($_r['r_title'] == '?') {
            // If this item does NOT have a title, then it could be a module that
            // adds entries to other items (comments, ratings, likes, etc.) - in
            // this case we want to show the item they are ATTACHED to
            if (strlen($_r['r_data']) > 0) {
                $_tmp = json_decode($_r['r_data'], true);
                if (is_array($_tmp)) {
                    $pfx = jrCore_db_get_prefix($_r['r_module']);
                    $mod = false;
                    if (isset($_tmp["{$pfx}_module"])) {
                        $mod = $_tmp["{$pfx}_module"];
                    }
                    $iid = false;
                    if (isset($_tmp["{$pfx}_item_id"])) {
                        $iid = (int) $_tmp["{$pfx}_item_id"];
                    }
                    if ($mod && $iid) {
                        $req = "SELECT r_title FROM {$tbl} WHERE r_module = '" . jrCore_db_escape($mod) . "' AND r_item_id = '{$iid}' LIMIT 1";
                        $_in = jrCore_db_query($req, 'SINGLE');
                        if ($_in && isset($_in['r_title'])) {
                            $dat[3]['title'] = $_in['r_title'];
                        }
                    }
                }
                else {
                    $dat[3]['title'] = 'item_id: ' . $_r['r_item_id'] . ' (no title)';
                }
            }
            else {
                $dat[3]['title'] = 'item_id: ' . $_r['r_item_id'] . ' (no title)';
            }
        }
        else {
            $dat[3]['title'] = $_r['r_title'];
            if ($k == 0 && $_r['r_module'] == 'jrProfile') {
                // Get our profile name for use in this profile's items below
                $_pd                       = json_decode($_r['r_data'], true);
                $_pr["{$_r['r_item_id']}"] = $_pd['profile_url'];
            }
        }
        $dat[3]['class'] = 'center';
        if ($k == 0) {
            $dat[4]['title'] = (strpos($_r['r_data'], 'rb_item_media')) ? $pass : '-';
            $dat[4]['class'] = 'center';
            if (jrCore_module_is_active($_item['r_module'])) {
                $dat[5]['title'] = jrCore_page_button("r", 'restore item', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/restore_recycle_bin_item/id={$_item['r_id']}')");
            }
            else {
                $dat[5]['title'] = jrCore_page_button("r", 'restore item', 'disabled');
            }
        }
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
}

/**
 * Restore an item (and any associated items) from the Recycle Bin
 * @param $id int Recycle Bin ID to restore
 * @return bool
 */
function jrCore_restore_recycle_bin_item($id)
{
    // Must get a good recycle bin id
    if (!jrCore_checktype($id, 'number_nz')) {
        return 0;
    }

    // Make sure it exists
    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    $req = "SELECT * FROM {$tbl} WHERE r_id = '{$id}'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt || !is_array($_rt) || !is_array($_rt[0])) {
        jrCore_set_form_notice('error', 'invalid recycle bin id - please try again (2)');
        jrCore_location('referrer');
    }

    // Now we know the module and ID, we can restore this item an ANY other items that are part of it's group
    $req = "SELECT * FROM {$tbl} WHERE r_group_id = '{$_rt[0]['r_module']}:{$_rt[0]['r_item_id']}'";
    $_ai = jrCore_db_query($req, 'NUMERIC');
    if ($_ai && is_array($_ai) && count($_ai) > 0) {
        $_rt = array_merge($_rt, $_ai);
        unset($_ai);
    }

    // Restore Items
    $_dl = array();
    foreach ($_rt as $n => $_i) {

        $tbi = jrCore_db_table_name($_i['r_module'], 'item');
        $req = "INSERT IGNORE INTO {$tbi} (`_item_id`) VALUES (" . intval($_i['r_item_id']) . ")";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            // Do we already exist?
            $req = "SELECT `_item_id` FROM {$tbi} WHERE `_item_id` = " . intval($_i['r_item_id']);
            $_ae = jrCore_db_query($req, 'SINGLE');
            if (!$_ae || !is_array($_ae) || !isset($_ae['_item_id'])) {
                return 0;
            }
        }

        $rem = false;
        $_tm = json_decode($_i['r_data'], true);
        if ($_tm && is_array($_tm)) {
            if (isset($_tm['rb_item_media'])) {
                $rem = true;
                unset($_tm['rb_item_media']);
            }
            $_in = array();
            $_cr = array();
            foreach ($_tm as $k => $v) {
                switch ($k) {
                    case '_item_id':
                        break;
                    default:
                        if (strpos($k, '_') === 0) {
                            $_cr[$k] = $v;
                        }
                        else {
                            $_in[$k] = $v;
                        }
                        break;
                }
            }

            // Prevent item from being flagged pending
            jrCore_set_flag("jrcore_created_pending_item_{$_i['r_module']}_{$_i['r_item_id']}", 1);

            // Update
            if (isset($_cr['_delete_files'])) {
                unset($_cr['_delete_files']);
            }
            jrCore_db_update_item($_i['r_module'], $_i['r_item_id'], $_in, $_cr, false, false, false);

            // Are we recovering media?
            if ($rem) {
                $_fl = jrCore_get_media_files($_i['r_profile_id']);
                if ($_fl && is_array($_fl)) {
                    foreach ($_fl as $_file) {
                        $name = basename($_file['name']);
                        if (strpos($name, "rb_{$_i['r_module']}_{$_i['r_item_id']}_") === 0) {
                            jrCore_rename_media_file($_i['r_profile_id'], $_file['name'], substr($name, 3));
                        }
                    }
                }
            }

            // Profile Counts
            switch ($_i['r_module']) {
                case 'jrProfile':
                case 'jrUser':
                    break;
                default:
                    jrCore_db_increment_key('jrProfile', $_i['r_profile_id'], "profile_{$_i['r_module']}_item_count", 1);
                    break;
            }

            // Trigger restore event
            $_args = array(
                'module'  => $_i['r_module'],
                'item_id' => $_i['r_item_id']
            );
            jrCore_trigger_event('jrCore', 'restore_recycle_bin_item', $_in, $_args);

        }
        $_dl[] = $_i['r_id'];

    }
    // Cleanup
    if (count($_dl) > 0) {
        $req = "DELETE FROM {$tbl} WHERE r_id IN(" . implode(',', $_dl) . ")";
        return jrCore_db_query($req, 'COUNT');
    }
    return 0;
}

/**
 * Delete an item (and any associated items) from the Recycle Bin
 * @param $id int Recycle Bin ID to restore
 * @return bool
 */
function jrCore_delete_recycle_bin_item($id)
{
    // Must get a good recycle bin id
    if (!jrCore_checktype($id, 'number_nz')) {
        return false;
    }

    // Make sure it exists
    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    $req = "SELECT r_module AS module, r_profile_id AS profile_id, r_item_id AS item_id, r_data AS data FROM {$tbl} WHERE r_id = '{$id}'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt || !is_array($_rt) || !is_array($_rt[0])) {
        // Already deleted
        return true;
    }

    // Now we know the module and ID, we can delete all the items
    $req = "DELETE FROM {$tbl} WHERE (r_id = '{$id}' OR r_group_id = '{$_rt[0]['module']}:{$_rt[0]['item_id']}')";
    jrCore_db_query($req);

    // Cleanup any attached media
    if (!$_pr = jrCore_get_flag('jrprofile_media_changes')) {
        $_pr = array();
    }
    foreach ($_rt as $_item) {
        $_tm = json_decode($_item['data'], true);
        if ($_tm && is_array($_tm) && isset($_tm['rb_item_media'])) {
            $pid       = (int) $_item['profile_id'];
            $_pr[$pid] = $pid;
            if (!$_fl = jrCore_get_flag("jrCore_delete_recycle_bin_item_{$pid}")) {
                $_fl = jrCore_get_media_files($pid);
                jrCore_set_flag("jrCore_delete_recycle_bin_item_{$pid}", $_fl);
            }
            if ($_fl && is_array($_fl)) {
                foreach ($_fl as $_file) {
                    $name = basename($_file['name']);
                    if (strpos($name, "rb_{$_item['module']}_{$_item['item_id']}_") === 0) {
                        jrCore_delete_media_file($_item['profile_id'], $name);
                    }
                }
            }
        }
    }
    if (count($_pr) > 0) {
        jrCore_set_flag('jrprofile_media_changes', $_pr);
    }

    // Trigger event for any modules that may need to manually clean up
    $_args = array(
        '_items' => $_rt
    );
    jrCore_trigger_event('jrCore', 'expire_recycle_bin', $_args);
    return true;
}

/**
 * Tabs for use on the Activity Log, Debug Log and Error Log views
 * @param $active string Active Tab
 */
function jrCore_master_log_tabs($active)
{
    global $_conf, $_post;
    $_tabs                    = array();
    $_tabs['activity']        = array(
        'label' => 'activity log',
        'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/activity"
    );
    $_tabs['debug']           = array(
        'label' => 'debug log',
        'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/debug_log"
    );
    $_tabs['error']           = array(
        'label' => 'error log',
        'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/php_error_log"
    );
    $_tabs[$active]['active'] = true;
    jrCore_page_tab_bar($_tabs);
}

/**
 * Run a performance check
 */
function jrCore_run_performance_check()
{
    ini_set('max_execution_time', 3600);

    // Clean up
    jrCore_db_truncate_datastore('jrCore');

    // Start
    $beg = explode(' ', microtime());
    $beg = $beg[1] + $beg[0];
    $stt = $beg;

    $_tm = array();

    //------------------
    // CPU
    //------------------
    $a = 0;
    for ($i = 0; $i < 10000000; $i++) {
        $a += $i;
    }
    $end        = explode(' ', microtime());
    $end        = $end[1] + $end[0];
    $_tm['cpu'] = round($end - $stt, 2);
    $beg        = $end;

    //------------------
    // DATABASE
    //------------------

    $tbi = jrCore_db_table_name('jrCore', 'item'); // OK
    $tbl = jrCore_db_table_name('jrCore', 'item_key'); // OK
    $con = jrCore_db_connect();

    // Create 2000 Objects
    foreach (range(1, 2000) as $num) {
        $req = "INSERT INTO {$tbi} (`_item_id`) VALUES (0)";
        mysqli_query($con, $req) or jrCore_notice('Error', 'query error (1): ' . mysqli_error($con));
        $iid = (int) mysqli_insert_id($con);
        if ($iid > 0) {
            $mod = ($num % 2);
            $_dt = array(
                'core_num'    => $num,
                'core_title'  => "Object {$num} Title",
                'core_title2' => "Object {$num} Title2",
                'core_string' => "Object {$num} String",
                'core_number' => intval("{$num}0"),
                'core_float'  => floatval("{$num}.{$num}"),
                'core_set'    => $mod
            );
            if ($mod == 1) {
                $_dt['core_one'] = 1;
            }
            if ($num == 2) {
                $_dt['core_exists'] = 1;
            }
            if ($num == 3) {
                $_dt['core_exists'] = 2;
            }
            $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES ";
            foreach ($_dt as $k => $v) {
                $req .= "({$iid},0,'" . jrCore_db_escape($k) . "',0,'" . jrCore_db_escape($v) . "'),";
            }
            $req = substr($req, 0, strlen($req) - 1);
            mysqli_query($con, $req) or jrCore_notice('Error', 'query error (2): ' . mysqli_error($con));
        }
    }

    // Update 2000 Objects
    foreach (range(1, 2000) as $num) {
        $_dt = array(
            'core_num2'   => $num,
            'core_title3' => "Object {$num} Title",
            'core_title4' => "String: {$num}: " . jrCore_create_unique_string(490)
        );
        $req = "INSERT INTO {$tbl} (`_item_id`,`_profile_id`,`key`,`index`,`value`) VALUES ";
        foreach ($_dt as $k => $v) {
            $req .= "({$num},0,'" . jrCore_db_escape($k) . "',0,'" . jrCore_db_escape($v) . "'),";
        }
        $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
        mysqli_query($con, $req) or jrCore_notice('Error', 'query error (3): ' . mysqli_error($con));
    }

    // Search Objects
    $i = 0;
    while ($i < 2000) {
        $req = "SELECT DISTINCT(a.`_item_id`) AS _item_id FROM {$tbl} a
            LEFT JOIN {$tbl} b ON (b.`_item_id` = a.`_item_id` AND b.`key` = 'core_num')
            LEFT JOIN {$tbl} c ON (c.`_item_id` = a.`_item_id` AND c.`key` = 'core_set')
            LEFT JOIN {$tbl} d ON (d.`_item_id` = a.`_item_id` AND d.`key` = 'core_string')
            LEFT JOIN {$tbl} e ON (e.`_item_id` = a.`_item_id` AND e.`key` = 'core_title')
                WHERE a.`key` = '_updated'
                  AND b.`value` > {$i}
                  AND c.`value` > {$i}
                  AND d.`value` LIKE '%tri%'
                  AND e.`value` LIKE '%itl%'
                ORDER BY a.`value` DESC LIMIT 10";
        mysqli_query($con, $req) or jrCore_notice('Error', 'query error (4): ' . mysqli_error($con));
        $i++;
    }

    // Delete Objects
    foreach (range(1, 2000) as $num) {
        $req = "DELETE FROM {$tbi} WHERE `_item_id` = '{$num}'";
        mysqli_query($con, $req) or jrCore_notice('Error', 'query error (5): ' . mysqli_error($con));
        $req = "DELETE FROM {$tbl} WHERE `_item_id` = '{$num}'";
        mysqli_query($con, $req) or jrCore_notice('Error', 'query error (6): ' . mysqli_error($con));
    }

    $end       = explode(' ', microtime());
    $end       = $end[1] + $end[0];
    $_tm['db'] = round($end - $beg, 2);
    $beg       = $end;

    // Reset
    jrCore_db_truncate_datastore('jrCore');

    //------------------
    // FILESYSTEM
    //------------------
    clearstatcache();
    $cdr = jrCore_get_module_cache_dir('jrCore');
    foreach (range(1, 1000) as $num) {
        $str = jrCore_create_unique_string(1024);
        jrCore_write_to_file("{$cdr}/performance_test.txt", "{$num}: {$str}\n", 'append');
    }
    // Read
    $num = 0;
    while ($num < 1000) {
        file_get_contents("{$cdr}/performance_test.txt");
        $num++;
    }
    unlink("{$cdr}/performance_test.txt");  // OK

    $end          = explode(' ', microtime());
    $end          = $end[1] + $end[0];
    $_tm['fs']    = round($end - $beg, 2);
    $_tm['total'] = round($end - $stt, 2);

    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "INSERT INTO {$tbl} (p_time, p_val) VALUES (UNIX_TIMESTAMP(), '" . jrCore_db_escape(json_encode($_tm)) . "')";
    jrCore_db_query($req);

    return $_tm;
}

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
            $tbl = jrCore_db_table_name('jrCore', 'queue_info');
            $req = "SELECT SUM(queue_depth) AS c FROM {$tbl}";
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
