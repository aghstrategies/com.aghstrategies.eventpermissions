<?php

require_once 'eventpermissions.civix.php';

/**
 * Implements hook_civicrm_permission().
 */
function eventpermissions_civicrm_permission(&$permissions) {
  $permissions += array(
    'edit all events' => array(
      ts('Edit all events', array('domain' => 'com.aghstrategies.eventpermissions')),
      ts('Without this permission, users with Access CiviEvent may only edit events they created or where they are registered with a role defined on the <a href="%1">Administer Event Permissions</a> page.',
        array(
          'domain' => 'com.aghstrategies.civimonitor',
          1 => CRM_Utils_System::url('civicrm/eventpermissions', 'reset=1'),
        )
      ),
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
      $check = CRM_Eventpermissions_Utils::checkPerms($form->_id);
      if ($check === FALSE) {
        // Deny access.
        CRM_Core_Error::statusBounce(ts('You do not have access to edit this event (ID: %1).', array(1 => $form->_id, 'domain' => 'com.aghstrategies.eventpermissions')));
      }
      break;
  }
}

/**
 * Implements hook_civicrm_pageRun().
 */
function eventpermissions_civicrm_pageRun(&$page) {
  if ($page->getVar('_name') == 'CRM_Contact_Page_DashBoard') {
    $dashId = CRM_Eventpermissions_Utils::getDashletId();
    $resources = CRM_Core_Resources::singleton();
    $resources->addVars('eventPermissions', array('dashletId' => "#widget-$dashId"));
  }
}

/**
 * Implements hook_civicrm_navigationMenu().
 */
function eventpermissions_civicrm_navigationMenu(&$params) {
  $searchParams = array('url' => 'civicrm/eventpermissions?reset=1');
  $menuItems = array();
  CRM_Core_BAO_Navigation::retrieve($searchParams, $menuItems);
  if (!empty($menuItems)) {
    return;
  }
  $searchParams = array('url' => 'civicrm/admin/options/participant_role?reset=1');
  $menuItems = array();
  CRM_Core_BAO_Navigation::retrieve($searchParams, $menuItems);
  $parent = CRM_Utils_Array::value('parent_id', $menuItems);

  $navId = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
  if (is_int($navId)) {
    $navId++;
  }

  $basicAttrs = array(
    'label' => ts('Event Permissions', array('domain' => 'com.aghstrategies.eventpermissions')),
    'name' => 'Event Permissions',
    'url' => 'civicrm/eventpermissions?reset=1',
    'permission' => 'administer CiviCRM',
    'active' => 1,
    'navID' => $navId,
    'parentID' => $parent,
  );

  $searchParams = array('id' => $parent);
  $menuItems = array();
  CRM_Core_BAO_Navigation::retrieve($searchParams, $menuItems);
  if (empty($menuItems['parent_id'])) {
    $params[$parent]['child'][$navId] = $basicAttrs;
  }
  else {
    $params[$menuItems['parent_id']]['child'][$parent]['child'][$navId] = array('attributes' => $basicAttrs);
  }
}

/**
 * Implements hook_civicrm_copy().
 *
 * Sets the "created_id" to the current contact.
 */
function eventpermissions_civicrm_copy($objectName, &$object) {
  if ($objectName != 'Event') {
    return;
  }
  $contactId = CRM_Core_Session::singleton()->get('userID');
  if (empty($contactId)) {
    return;
  }
  $object->created_id = $contactId;
  $object->save();
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
