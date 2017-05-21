<?php
/**
 * Jamroom 5 Sitemap Generator module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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

//------------------------------
// create
//------------------------------
function view_jrSitemap_create($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSitemap');
    jrCore_page_banner("Create XML Site Map");

    // Form init
    $_tmp = array(
        'submit_value'  => 'create site map',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Are you sure you want to create a new XML site map? This could take some time to run, so please be patient.',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the site map is created'
    );
    jrCore_form_create($_tmp);

    jrCore_page_note("Please be patient while the Sitemap is generated - on large systems this could take a few minutes.<br><br>Your SiteMap will be available at: <a href=\"{$_conf['jrCore_base_url']}/sitemap.xml\" target=\"_blank\"><u>{$_conf['jrCore_base_url']}/sitemap.xml</u></a>", false);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrSitemap_create_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    jrSitemap_create_sitemap(true);

    jrCore_form_delete_session();
    jrCore_form_modal_notice('complete', 'The XML Site Map has been created');
    exit;
}

//------------------------------
// default request for site map
//------------------------------
function view_jrSitemap_default($_post, &$_user, &$_conf)
{
    if (strpos($_post['option'], 'sitemap') === 0 && strpos($_post['option'], '.xml')) {
        if ($file = jrCore_confirm_media_file_is_local(0, $_post['option'], null, true)) {
            header("Content-Type: text/xml; charset=utf-8");
            echo file_get_contents($file);
            exit;
        }
    }
    jrCore_page_not_found();
}
