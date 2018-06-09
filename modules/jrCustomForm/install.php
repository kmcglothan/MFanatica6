<?php
/**
 * Jamroom Simple Custom Forms module
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Custom Form Install
 */
function jrCustomForm_install()
{
    // Create sample "contact_us" form
    $tbl = jrCore_db_table_name('jrCustomForm', 'form');
    $req = "INSERT INTO {$tbl} (form_created,form_updated,form_name,form_title,form_message,form_unique,form_login)
            VALUES (UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'contact_us','Contact Us','Please enter the message you would like to send and we will get back to you ASAP.','off','on')";
    $fid = jrCore_db_query($req, 'INSERT_ID');
    if (isset($fid) && jrCore_checktype($fid, 'number_nz')) {

        // Message
        $_field = array(
            'name'     => 'form_content',
            'type'     => 'textarea',
            'label'    => 'Your Message',
            'help'     => 'Let us know what is on your mind',
            'validate' => 'printable',
            'group'    => 'all',
            'required' => true
        );
        jrCore_verify_designer_form_field('jrCustomForm', 'contact_us', $_field);

        // Email (non-logged in users)
        $_field = array(
            'name'     => 'form_email',
            'type'     => 'text',
            'label'    => 'Your Email Address',
            'help'     => 'Please enter a valid Email Address so we know how to contact you',
            'validate' => 'email',
            'group'    => 'visitor',
            'required' => true
        );
        jrCore_verify_designer_form_field('jrCustomForm', 'contact_us', $_field);

        // Activate the new fields
        $tbl = jrCore_db_table_name('jrCore', 'form');
        $req = "UPDATE {$tbl} SET `active` = '1' WHERE `module` = 'jrCustomForm' AND `view` = 'contact_us' AND `name` IN('form_content','form_email')";
        jrCore_db_query($req);

        // Register form with designer so it can be modified
        jrCore_register_module_feature('jrCore', 'designer_form', 'jrCustomForm', 'contact_us');
    }
    return true;
}
