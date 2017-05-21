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
 * BBCode URL plugin
 * @param $text
 * @return mixed
 */
function jrCore_bbcode_url($text)
{
    if (stripos($text, '[url')) {

        // Make sure we are balanced...
        $_opn = preg_split("/\[url/i", $text);
        $_cls = preg_split(",\[/url\],i", $text);
        if ($_opn && $_cls && count($_opn) === count($_cls)) {

            $_tmp = explode("\n", $text);
            if ($_tmp && is_array($_tmp)) {
                $temp = '';
                $_rep = array();
                foreach ($_tmp as $line) {
                    if (stripos(' ' . $line, '[url')) {
                        // We've found our URL
                        $line = str_replace('[URL', '[url', $line);
                        $_url = explode('[url', $line);
                        foreach ($_url as $k => $part) {
                            if (stripos($part, '[/url]')) {
                                $part = str_replace('[/URL]', '[/url]', $part);
                                // We have found our URL
                                $uniq = substr(md5(microtime() . mt_rand()), 0, 8);
                                if (strpos($part, '=') === 0) {
                                    // This URL is defined in the TAG
                                    // =http://local.jamroom.net/Jamroom5/forum/update/id=197]CLICK ME[/url]
                                    $url = trim(str_replace('"', '', substr($part, 1, (strpos($part, ']') - 1))));
                                    if (jrCore_checktype($url, 'url')) {
                                        $_nam                = explode(']', $part);
                                        $name                = substr($_nam[1], 0, strpos($_nam[1], '['));
                                        $_rep["%%{$uniq}%%"] = '<a href="' . $url . '" target="_blank"><span class="bbcode_url">' . $name . '</span></a>';
                                        $temp .= "%%{$uniq}%%" . substr($part, stripos($part, '/url]') + 5);
                                    }
                                }
                                else {
                                    // Normal URL
                                    // ]http://local.jamroom.net/Jamroom5/forum/update/id=197[/url] ... after
                                    $part = substr($part, 1);
                                    $url  = trim(str_replace('"', '', substr($part, 0, strpos($part, '['))));
                                    if (jrCore_checktype($url, 'url')) {
                                        $_rep["%%{$uniq}%%"] = '<a href="' . $url . '" target="_blank"><span class="bbcode_url">' . $url . '</span></a>';
                                        $temp .= "%%{$uniq}%%" . substr($part, stripos($part, '/url]') + 5);
                                    }
                                }
                            }
                            else {
                                $temp .= $part;
                            }
                        }
                    }
                    else {
                        $temp .= "{$line}\n";
                    }
                }
                $text = $temp;
                if (count($_rep) > 0) {
                    if ($_tmp = jrCore_get_flag('jrcore_bbcode_replace_blocks')) {
                        $_rep = array_merge($_tmp, $_rep);
                    }
                    jrCore_set_flag('jrcore_bbcode_replace_blocks', $_rep);
                }
            }
        }
    }
    return $text;
}
