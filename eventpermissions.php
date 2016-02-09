<?php

require_once 'eventpermissions.civix.php';

/**
 * Implements hook_civicrm_permission().
 */
function eventpermissions_civicrm_permission(&$permissions) {
  $permissions['edit all events'][1] = implode(' ', array(
    $permissions['edit all events'][1],
    ts('Without this permission, users with Access CiviEvent may only edit events they created or where they are registered with a role defined on the <a href="%1">Administer Event Permissions</a> page.',
      array(
        'domain' => 'com.aghstrategies.civimonitor',
        1 => CRM_Utils_System::url('civicrm/eventpermissions', 'reset=1'),
      )
    ),
  ));
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
 * Implements hook_civicrm_aclGroup().
 */
function eventpermissions_civicrm_aclGroup($type, $contactID, $tableName, &$allGroups, &$currentGroups) {
  // Filter out other types of ACL checks
  if (($type != CRM_Core_Permission::EDIT && $type != CRM_Core_Permission::VIEW)
    || $tableName != 'civicrm_event') {
    return;
  }
  // get events where contact is host
  try {
    $result = civicrm_api3('Participant', 'get', array(
      'sequential' => 1,
      'return' => "event_id",
      'contact_id' => $contactID,
      'role_id' => array('IN' => CRM_Eventpermissions_Utils::getHostId()),
      'options' => array('limit' => 0),
    ));
    foreach ($result['values'] as $participant) {
      if (!in_array($participant['event_id'], $currentGroups)) {
        $currentGroups[] = $participant['event_id'];
      }
    }
  }
  catch (CiviCRM_API3_Exception $e) {
    $error = $e->getMessage();
    CRM_Core_Error::debug_log_message(ts('API Error finding event owner: %1', array(
      'domain' => 'com.aghstrategies.eventpermissions',
      1 => $error,
    )));
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
