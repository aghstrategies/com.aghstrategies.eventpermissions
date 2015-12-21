<?php

require_once 'eventpermissions.civix.php';

/**
 * Implements hook_civicrm_permission().
 */
function eventpermissions_civicrm_permission(&$permissions) {
  $permissions += array(
    'edit all events' => array(
      ts('Edit all events', array('domain' => 'com.aghstrategies.eventpermissions')),
      ts('Without this permission, users with Access CiviEvent may only edit events they created or where they are registered as Host.', array('domain' => 'com.aghstrategies.civimonitor')),
    ),
  );
}

/**
 * Implements hook_civicrm_preProcess().
 *
 * Note: the event "edit" ACL actually governs the ability to register for an
 * event, so it isn't applicable here.  Instead, you can manually grant access
 * to manage an event by making someone registered as "host".
 */
function eventpermissions_civicrm_preProcess($formName, &$form) {
  switch ($formName) {
    case 'CRM_Event_Form_ManageEvent_EventInfo':
    case 'CRM_Event_Form_ManageEvent_Location':
    case 'CRM_Event_Form_ManageEvent_Fee':
    case 'CRM_Event_Form_ManageEvent_Registration':
    case 'CRM_Friend_Form_Event':
    case 'CRM_Event_Form_ManageEvent_ScheduleReminders':
    case 'CRM_Event_Form_ManageEvent_Repeat':
    case 'CRM_Event_Form_ManageEvent_Conference':
      // Admins or users with "edit all events" can edit all events.
      if (CRM_Core_Permission::check('edit all events') || CRM_Core_Permission::check('administer CiviCRM')) {
        // return;
      }
      if ($form->_id) {
        $contactId = CRM_Core_Session::singleton()->get('userID');

        // Creators of events can edit their events.
        try {
          $result = civicrm_api3('Event', 'getcount', array(
            'id' => $form->_id,
            'created_id' => $contactId,
          ));
          if (!empty($result)) {
            return;
          }
        }
        catch (CiviCRM_API3_Exception $e) {
          $error = $e->getMessage();
          CRM_Core_Error::debug_log_message(ts('API Error finding event owner: %1', array(
            'domain' => 'com.aghstrategies.eventpermissions',
            1 => $error,
          )));
        }

        // Hosts of events can edit their events.
        try {
          // TODO: fix role_id depending upon site-specific naming.
          $result = civicrm_api3('Participant', 'getcount', array(
            'contact_id' => $contactId,
            'event_id' => $form->_id,
            'role_id' => "Host",
          ));
          if (!empty($result)) {
            return;
          }
        }
        catch (CiviCRM_API3_Exception $e) {
          $error = $e->getMessage();
          CRM_Core_Error::debug_log_message(ts('API Error finding event owner: %1', array(
            'domain' => 'com.aghstrategies.eventpermissions',
            1 => $error,
          )));
        }
        // Deny access.
        CRM_Core_Error::statusBounce(ts('You do not have access to edit this event (ID: %1).', array(1 => $form->_id, 'domain' => 'com.aghstrategies.eventpermissions')));
      }
      break;
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function eventpermissions_civicrm_config(&$config) {
  _eventpermissions_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function eventpermissions_civicrm_xmlMenu(&$files) {
  _eventpermissions_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function eventpermissions_civicrm_install() {
  _eventpermissions_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function eventpermissions_civicrm_uninstall() {
  _eventpermissions_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function eventpermissions_civicrm_enable() {
  _eventpermissions_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function eventpermissions_civicrm_disable() {
  _eventpermissions_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function eventpermissions_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventpermissions_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function eventpermissions_civicrm_managed(&$entities) {
  _eventpermissions_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventpermissions_civicrm_caseTypes(&$caseTypes) {
  _eventpermissions_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventpermissions_civicrm_angularModules(&$angularModules) {
_eventpermissions_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function eventpermissions_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventpermissions_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function eventpermissions_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function eventpermissions_civicrm_navigationMenu(&$menu) {
  _eventpermissions_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'com.aghstrategies.eventpermissions')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _eventpermissions_civix_navigationMenu($menu);
} // */
