<?php
/**
 * Jamroom 5 Spectrum module
 *
 * copyright 2003 - 2015
 * by Ultrajam
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * Ultrajam
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
 * Jamroom 5 Spectrum module
 *
 * copyright 2014 - 2015
 * by Ultrajam
 *
 * Jamroom 5 ujSpectrum module
 *
 * copyright 2014 by SteveX @ Ultrajam - All Rights Reserved
 * http://www.ultrajam.net
 *
 * This module uses the spectrum jQuery plugin
 * https://github.com/bgrins/spectrum
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function ujSpectrum_meta() {
    $_tmp = array(
        'name'        => 'Spectrum',
        'url'         => 'spectrum',
        'version'     => '1.0.1',
        'license'     => 'mpl',
        'developer'   => 'Ultrajam, &copy;' . strftime('%Y'),
        'description' => 'Provides a full-featured color picker form field.',
        'support'     => 'http://www.jamroom.net/ultrajam',
        'category'    => 'forms'
    );
    return $_tmp;
}

/**
 * init
 */
function ujSpectrum_init()
{
    // register the custom form fields
    jrCore_register_module_feature('jrCore','form_field','ujSpectrum','spectrum');

    // append to form designer options field help text
    jrCore_register_event_listener('jrCore', 'form_field_create', 'ujSpectrum_form_field_create_listener');

    return true;
}


/**
 * Append to help text of form designer options field
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function ujSpectrum_form_field_create_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['name']) && $_data['name'] == 'options' && isset($_data['sublabel']) && $_data['sublabel'] == 'see <strong>help</strong> for what is allowed here') {
        $_data['help'] = $_data['help'].'<br><br><strong>spectrum color picker</strong> - the options field can be used to configure the color picker. For example, you could use <strong>data-show-input=true data-allow-empty=false data-show-alpha=true data-show-palette=true data-show-initial=true data-show-buttons=false data-preferred-format=rgb</strong>. See the module docs and http://bgrins.github.io/spectrum/ for details.';
    }
    return $_data;
}


/**
 * form_field display
 */
function ujSpectrum_form_field_spectrum_display($_field,$_att = null)
{
    global $_conf;

	// going to have options set for a config setting or form designer field
    if (isset($_field['options']) && strlen($_field['options']) > 0) {
        $_field['options'] = trim($_field['options']);
    }
	// will work as attributes for module items, add css and js
    $tmp = jrCore_get_flag('jrcore_spectrum_js_included');
    if (!$tmp) {
        $_js = array('source' => "{$_conf['jrCore_base_url']}/modules/ujSpectrum/contrib/spectrum/spectrum.js");
        jrCore_create_page_element('javascript_href',$_js);
        $_js = array('source' => "{$_conf['jrCore_base_url']}/modules/ujSpectrum/contrib/spectrum/spectrum.css");
        jrCore_create_page_element('css_href',$_js);
        jrCore_set_flag('jrcore_spectrum_js_included',1);
    }
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $cls = 'form_spectrum spectrum' . jrCore_form_field_get_hilight($_field['name']);
    if (isset($_field['class']{0})) {
        $cls .= ' ' . $_field['class'];
    }
    if (isset($_field['value']) && strlen($_field['value']) > 0) {
        $val = $_field['value'];
    }
    elseif (isset($_field['default']) && strlen($_field['default']) > 0) {
        $val = $_field['default'];
    } else {
    	$val = '#ffff00';
    }
    $htm = '<input id="'. $_field['name'] .'" class="'. $cls .'" name="'. $_field['name'] .'" tabindex="'. $idx .'" value="' . $val . '" ';
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' '. $key .'="'. $attr .'"';
        }
    }
    if (isset($_field['options']) && !is_array($_field['options']) && strlen($_field['options']) > 0) {
        // JSON encoded options = not in use
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
        // straight
        elseif (strlen($_field['options']) > 5) {
			$data = $_field['options'];
        }
    }
    if (isset($_field['options']) && is_array($_field['options'])) {
        foreach ($_field['options'] as $k => $v) {
            if (strlen($k) === 0||strlen($v) === 0) {
                continue;
            }
            else {
                $data .= $k.'="' . $v . '" ';
            }
        }
    }
    $htm .= $data.'>';
    $_js = array("$('#{$_field['name']}').spectrum({ color: '{$val}' });");
    jrCore_create_page_element('javascript_ready_function',$_js);
    $_field['html']     = $htm;
    $_field['type']     = 'spectrum';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page',$_field);
    return true;
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function ujSpectrum_form_field_spectrum_attributes()
{
    return 	array('size','disabled','onfocus','onblur','onchange','canceltext','data-color','data-flat','data-show-input','data-show-initial','data-allow-empty','data-show-alpha','data-disabled','data-local-storage-key','data-show-palette','data-show-palette-only','data-show-selection-palette','data-clickout-fires-change','data-cancel-text','data-choose-text','data-container-class-name','data-replacer-class-name','data-preferred-format','data-max-selection-size','data-palette','data-selection-palette');
}

