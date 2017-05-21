<?php
/**
 * Jamroom Video module
 *
 * copyright 2017 The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
 * Jamroom Video Module - m4v Video File Support plugin
 * Copyright 2003 - 2012 by the Jamroom Network - all rights reserved
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

function jrVideo_m4v_decode($input_file, $_options, $error_file)
{
    return $input_file;
}

function jrVideo_m4v_encode($input_file, $_options, $error_file)
{
    $ffmpeg = jrVideo_get_ffmpeg_command();

    ob_start();
    system("{$ffmpeg} -y -i \"{$input_file}\" -threads " . jrVideo_get_ffmpeg_thread() . " -vcodec libx264 -vprofile high -preset veryfast -b:v 400k -maxrate 400k -bufsize 1000k -acodec libfaac -ac 2 -ar 48000 -ab 96k \"{$input_file}.m4v\" >/dev/null 2>{$error_file}", $ret);
    ob_end_clean();
    return "{$input_file}.m4v";
}


