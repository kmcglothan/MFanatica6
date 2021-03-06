<?php
/**
 * Jamroom Disqus Comments module
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
 * config
 */
function jrDisqus_config()
{
    // Disqus Site Name
    $_tmp = array(
        'name'     => 'site_name',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'printable',
        'label'    => 'disqus shortname',
        'help'     => 'This is the short name that can be found in Disqus in your site identity section. It was created when when you signed up for Disqus',
        'order'    => 1
    );
    jrCore_register_setting('jrDisqus', $_tmp);

    // use unique identifier
    $_tmp = array(
        'name'     => 'identifier',
        'type'     => 'checkbox',
        'default'  => 'on',
        'upgrade'  => 'off',
        'validate' => 'onoff',
        'label'    => 'use unique identifier',
        'help'     => 'If this option is checked, a unique identifier will be used when embedding comments. If you are upgrading from Disqus module version 1.0.5 or older, it is recommended to leave this option off or comments may not appear in the correct location.',
        'order'    => 2
    );
    jrCore_register_setting('jrDisqus', $_tmp);

    return true;
}
