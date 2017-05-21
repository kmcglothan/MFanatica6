<?php
/**
 * Jamroom 5 Geo Flags module
 *
 * copyright 2003 - 2014
 * by b360
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *

// make sure we are not being called directly
defined('APP_DIR') or exit();

 * meta
 */
function myFlags_meta()
{
    $_tmp = array(
        'name'        => 'Geo Flags',
        'url'         => 'geo_flags',
        'version'     => '1.0.1',
        'developer'   => 'B360, &copy;' . strftime('%Y'),
        'description' => 'Add Flags for GeoIp',
        'category'    => 'tools',
        'requires'    => 'jrGeo',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function myFlags_init()
{
    return true;
}

function smarty_function_myFlags_flag($params, $smarty)
{
    global $_conf;

    if (jrCore_module_is_active('jrGeo')) {

        $params = array(
            'template' => "%country_code%"
        );
        $rt = smarty_function_jrGeo_location($params, $smarty);
        $cc = strtolower($rt);
        if (is_file("{$_conf['jrCore_base_dir']}/modules/myFlags/img/{$cc}.png")) {
            $image_file = "{$_conf['jrCore_base_url']}/modules/myFlags/img/{$cc}.png";
            return '<img src="' . $image_file . '" alt="' . $cc . '">';
        }
        else {
            return 'Unknown Location';
        }
    }
    return '';
}
