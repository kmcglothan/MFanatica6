<?php
/**
 * Jamroom Documentation module
 *
 * copyright 2018 The Jamroom Network
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
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * config
 */
function jrDocs_config()
{
    // Allowed File Types
    $_tmp = array(
        'name'     => 'allowed_file_types',
        'default'  => 'zip,doc,txt,pdf',
        'type'     => 'text',
        'label'    => 'allowed file types',
        'help'     => 'Enter a comma separated list of file types you would like to allow in the Downloadable File section',
        'validate' => 'not_empty',
        'order'    => 1
    );
    jrCore_register_setting('jrDocs', $_tmp);

    // Enable Editor
    $_tmp = array(
        'name'     => 'editor',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'enable editor',
        'help'     => 'Check this option to enable the WYSIWYG editor for the &quot;Text&quot; and &quot;Text and Image&quot; documentation sections',
        'order'    => 2
    );
    jrCore_register_setting('jrDocs', $_tmp);

    // Show Table of Contents
    $_tmp = array(
        'name'     => 'show_toc',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'show table of contents',
        'help'     => 'Check this option to show the Table of Contents tab in the documentation section.  If unchecked, the tab bar will not show.',
        'order'    => 3
    );
    jrCore_register_setting('jrDocs', $_tmp);

    // Show Related Docs
    $_tmp = array(
        'name'     => 'show_related',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'show related documents',
        'help'     => 'If this option is checked, a &quot;related documents&quot; popover will show for tagged keywords in the documents.<br><br><b>Requires the Item Tags module be installed and active.</b>',
        'order'    => 4
    );
    jrCore_register_setting('jrDocs', $_tmp);

    return true;
}
