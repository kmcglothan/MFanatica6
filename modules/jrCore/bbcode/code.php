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
 * @copyright 2014 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

use Highlight\Highlighter;

/**
 * Convert [code] blocks in text to hilighted code areas
 * @param $string string to check for bbcode code tags
 * @return string
 */
function jrCore_bbcode_code($string)
{
    if (stripos(' ' . $string, '[code]')) {

        // Make sure we are balanced
        $otags = substr_count(strtolower($string), '[code]');
        $ctags = substr_count(strtolower($string), '[/code]');
        if ($otags == $ctags) {

            // Bring in scrivo
            if (!jrCore_get_flag('jrCore_scrivo_include')) {
                require_once APP_DIR . '/modules/jrCore/contrib/scrivo/Highlighter.php';
                require_once APP_DIR . '/modules/jrCore/contrib/scrivo/JsonRef.php';
                require_once APP_DIR . '/modules/jrCore/contrib/scrivo/Language.php';
                jrCore_set_flag('jrCore_scrivo_include', 1);
            }
            $hl = new Highlight\Highlighter();
            $_l = array('bash', 'php', 'javascript', 'css', 'xml');
            if (isset($_conf['jrCore_bbcode_languages']) && strlen($_conf['jrCore_bbcode_languages']) > 0) {
                $_l = explode(',', $_conf['jrCore_bbcode_languages']);
            }
            $hl->setAutodetectLanguages($_l);

            // We have code blocks in this text - let's break it up into pieces
            $_rep   = array(
                "[/code]\r" => '[/code]',
                "[/code]\n" => '[/code]'
            );
            $string = str_ireplace(array_keys($_rep), $_rep, $string);
            $_str   = preg_split("/code\]/i", $string);
            if ($_str && is_array($_str)) {

                $text = '';
                $code = '';
                $test = false;
                $_rep = array();
                foreach ($_str as $k => $block) {

                    if (!$test && substr($block, -1) == '[') {
                        // We've found the piece BEFORE a code open section
                        $text .= jrCore_entity_string(substr($block, 0, (strlen($block) - 1)));
                        $test = true;
                    }
                    elseif ($test) {

                        if (substr($block, -1) == '[') {
                            // We've found [code] within [code]
                            $code .= $block . 'code]';
                        }
                        else {

                            $uniq = substr(md5(microtime() . mt_rand()), 0, 8);

                            // We can create our code block now - we found the closing [/code]
                            $code .= substr($block, 0, (strlen($block) - 2));
                            $code = $hl->highlight('php', $code);
                            $_crp = array(
                                "\n</span>" => '</span>',
                                "\r</span>" => '</span>'
                            );
                            $html = str_replace(array_keys($_crp), $_crp, $code->value);
                            $temp = '<div class="bbcode_code"><pre class="hljs ' . $code->language . '">' . $html . '</pre></div>';

                            // We will replace with "real" code on output via a display listener
                            $_rep["%%{$uniq}%%"] = $temp;
                            $text .= "%%{$uniq}%%";
                            $test = false;
                            $code = '';
                        }
                    }
                    else {
                        // $text .= jrCore_entity_string($block);
                        $text .= $block;
                        $test = false;
                    }
                }
                $string = $text;
                if (count($_rep) > 0) {
                    if ($_tmp = jrCore_get_flag('jrcore_bbcode_replace_blocks')) {
                        $_rep = array_merge($_tmp, $_rep);
                    }
                    jrCore_set_flag('jrcore_bbcode_replace_blocks', $_rep);
                }
            }
        }
        else {
            // Imbalanced - convert HTML
            $string = jrCore_entity_string($string);
        }
    }
    return $string;
}
