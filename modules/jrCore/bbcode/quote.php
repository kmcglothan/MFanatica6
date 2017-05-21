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
 * BBCode Quote plugin
 * @param $string
 * @return mixed
 */
function jrCore_bbcode_quote($string)
{
    if (stripos(' ' . $string, '[quote')) {

        $_opn = preg_split("/\[quote/i", $string);
        $_cls = preg_split(",\[/quote\],i", $string);
        if (count($_opn) !== count($_cls)) {
            return $string;
        }

        // [quote], [quote=brian], [quote =  brian], [quote="brian"], [quote = "brian"], [quote="more than one word"]
        $_rp    = array(
            "[/quote]\r" => '[/quote]',
            "[/quote]\n" => '[/quote]',
            "</p>\n"     => '</p>',
            "\r\n"       => ' ~~!~~newline~~!~~ ',
            "\r"         => ' ~~!~~newline~~!~~ ',
            "\t"         => ' ~~!~~tabchar~~!~~ ',
            '[quote]'    => '[quote] ',
            '[/quote]'   => ' [/quote] ',
            ']'          => '] '
        );
        $_ep    = array(
            "</p>\n"             => '</p>',
            '~~!~~newline~~!~~ ' => "\n",
            '~~!~~tabchar~~!~~ ' => "\t"
        );
        $string = str_replace(array_keys($_rp), $_rp, $string);
        // Take care of openings
        $_rep = array();
        $open = 0;
        $name = 0;
        $text = '';
        $temp = '';
        foreach (explode(" ", $string) as $word) {
            if (stripos(' ' . $word, '[quote]')) {
                // Not quoting a specific user
                $_lng = jrUser_load_lang_strings();
                $temp .= '<div class="bbcode_quote"><span class="bbcode_quote_user">' . $_lng['jrCore'][96] . '</span>:';
                $open++;
            }
            elseif (stripos(' ' . $word, '[quote=')) {
                // We have a named quote
                $word = str_replace('"', '', $word);
                list($begn, $user) = explode('=', $word);
                if (strpos($user, ']')) {
                    // Single word user name
                    $temp .= '<div class="bbcode_quote"><span class="bbcode_quote_user">' . str_replace(array(']', '"', '&quot;'), '', $user) . '</span>:';
                }
                else {
                    $temp .= '<div class="bbcode_quote"><span class="bbcode_quote_user">' . str_replace(array(']', '"', '&quot;'), '', $user) . ' ';
                    $name++;
                }
                if (strlen($begn) > 0) {
                    $text .= substr($begn, 0, strpos($begn, '[quote'));
                }
                $open++;
            }
            elseif ($name > 0 && strpos($word, '"')) {
                // End of user
                $temp .= str_replace(array(']', '"', '&quot;'), '', $word) . '</span>:';
                $name--;
            }
            elseif ($open > 0 && stripos(' ' . $word, '[/quote]')) {
                // We are at the end of our quote
                $temp .= '</div>';
                $open--;
                if ($open === 0) {
                    // We will replace with "real" code on output via a display listener
                    $uniq                = substr(md5(microtime() . mt_rand()), 0, 8);
                    $_rep["%%{$uniq}%%"] = jrCore_string_to_url(nl2br(str_replace(array_keys($_ep), $_ep, $temp)));
                    $text .= "%%{$uniq}%%";
                    $temp = '';
                    $name = 0;
                }
            }
            else {
                if ($open > 0) {
                    $temp .= $word . ' ';
                }
                else {
                    $text .= $word . ' ';
                }
            }
        }
        if (count($_rep) > 0) {
            if ($_tmp = jrCore_get_flag('jrcore_bbcode_replace_blocks')) {
                $_rep = array_merge($_tmp, $_rep);
            }
            jrCore_set_flag('jrcore_bbcode_replace_blocks', $_rep);
        }
        $string = str_replace(array_keys($_ep), $_ep, $text);
    }
    return $string;
}
