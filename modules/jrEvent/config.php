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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * config
 */
function jrEvent_config()
{
    $_tmp = array(
        'name'     => 'show_past',
        'default'  => 'on',
        'type'     => 'select',
        'options'  => jrEvent_get_past_event_times(),
        'label'    => 'show past events',
        'help'     => 'Select how you want to handle Events that are past their start time',
        'order'    => 10,
        'validate' => 'not_empty'
    );
    jrCore_register_setting('jrEvent', $_tmp);

    $_tmp = array(
        'name'     => 'notification_time',
        'default'  => 24,
        'type'     => 'text',
        'label'    => 'Notification Time',
        'help'     => 'If the Attending button is allowed in a quota, a notification is sent to attendees this number of hours before the event time. Enter zero to disable notifications.',
        'order'    => 20,
        'validate' => 'number_nn'
    );
    jrCore_register_setting('jrEvent', $_tmp);

    $_opts = array(
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday'
    );
    $_tmp = array(
        'name'     => 'calendar_start_day',
        'default'  => 0,
        'type'     => 'select',
        'options'  => $_opts,
        'label'    => 'Calendar Start Day',
        'help'     => 'Select which day calendars are to start with',
        'order'    => 30,
        'validate' => 'not_empty'
    );
    jrCore_register_setting('jrEvent', $_tmp);

    return true;
}
