<?php
/**
 * Jamroom Event Calendar module
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
 * Create all form fields
 */
function jrEvent_install()
{
    // Event Title
    $_tmp = array(
        'name'     => 'event_title',
        'label'    => 2,
        'help'     => 3,
        'type'     => 'text',
        'validate' => 'not_empty',
        'active'   => true,
        'required' => true
    );
    jrCore_verify_designer_form_field('jrEvent', 'create', $_tmp);

    // Event Location
    $_tmp = array(
        'name'     => 'event_location',
        'label'    => 6,
        'help'     => 7,
        'type'     => 'text',
        'validate' => 'not_empty',
        'active'   => true,
        'required' => true
    );
    jrCore_verify_designer_form_field('jrEvent', 'create', $_tmp);

    // Event Date
    $_tmp = array(
        'name'     => 'event_date',
        'label'    => 11,
        'help'     => 12,
        'type'     => 'datetime',
        'validate' => 'date',
        'active'   => true,
        'required' => true
    );
    jrCore_verify_designer_form_field('jrEvent', 'create', $_tmp);

    // Event Description
    $_tmp = array(
        'name'     => 'event_description',
        'label'    => 4,
        'help'     => 5,
        'type'     => 'editor',
        'validate' => 'not_empty',
        'active'   => true,
        'required' => false
    );
    jrCore_verify_designer_form_field('jrEvent', 'create', $_tmp);

    // Event recurring
    $_tmp = array(
        'name'     => 'event_recurring',
        'label'    => 16,
        'help'     => 17,
        'type'     => 'select',
        'options'  => 'jrEvent_recurring_presets',
        'value'    => 'no',
        'validate' => 'not_empty',
        'active'   => false,
        'required' => false
    );
    jrCore_verify_designer_form_field('jrEvent', 'create', $_tmp);

    // Event End Date
    $_tmp = array(
        'name'     => 'event_end_date',
        'label'    => 18,
        'help'     => 67,
        'type'     => 'date',
        'validate' => 'date',
        'active'   => false,
        'required' => true
    );
    jrCore_verify_designer_form_field('jrEvent', 'create', $_tmp);

    // Event Image
    $_tmp = array(
        'name'     => 'event_image',
        'label'    => 8,
        'help'     => 9,
        'text'     => 10,
        'type'     => 'image',
        'active'   => true,
        'required' => false
    );
    jrCore_verify_designer_form_field('jrEvent', 'create', $_tmp);

    // Event Title
    $_tmp = array(
        'name'     => 'event_title',
        'label'    => 2,
        'help'     => 3,
        'type'     => 'text',
        'validate' => 'not_empty',
        'active'   => true,
        'required' => true
    );
    jrCore_verify_designer_form_field('jrEvent', 'update', $_tmp);

    // Event Location
    $_tmp = array(
        'name'     => 'event_location',
        'label'    => 6,
        'help'     => 7,
        'type'     => 'text',
        'validate' => 'not_empty',
        'active'   => true,
        'required' => true
    );
    jrCore_verify_designer_form_field('jrEvent', 'update', $_tmp);

    // Event Date
    $_tmp = array(
        'name'     => 'event_date',
        'label'    => 11,
        'help'     => 12,
        'type'     => 'datetime',
        'validate' => 'date',
        'active'   => true,
        'required' => true
    );
    jrCore_verify_designer_form_field('jrEvent', 'update', $_tmp);

    // Event Description
    $_tmp = array(
        'name'     => 'event_description',
        'label'    => 4,
        'help'     => 5,
        'type'     => 'editor',
        'validate' => 'not_empty',
        'active'   => true,
        'required' => false
    );
    jrCore_verify_designer_form_field('jrEvent', 'update', $_tmp);

    // Event Image
    $_tmp = array(
        'name'     => 'event_image',
        'label'    => 8,
        'help'     => 9,
        'text'     => 10,
        'type'     => 'image',
        'active'   => true,
        'required' => false
    );
    jrCore_verify_designer_form_field('jrEvent', 'update', $_tmp);
}
