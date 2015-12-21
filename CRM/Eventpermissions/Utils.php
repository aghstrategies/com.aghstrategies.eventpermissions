<?php

/**
 * Miscellaneous utilities.
 */
class CRM_Eventpermissions_Utils {
  /**
   * See if the current user can edit an event.
   *
   * @param int $eventId
   *   The event ID.
   *
   * @return bool
   *   Whether permission is granted.
   */
  public static function checkPerms($eventId) {
    // Admins or users with "edit all events" can edit all events.
    if (CRM_Core_Permission::check('edit all events') || CRM_Core_Permission::check('administer CiviCRM')) {
      return TRUE;
    }

    if (!$eventId) {
      return NULL;
    }

    $contactId = CRM_Core_Session::singleton()->get('userID');

    // Creators of events can edit their events.
    try {
      $result = civicrm_api3('Event', 'getcount', array(
        'id' => $eventId,
        'created_id' => $contactId,
      ));
      if (!empty($result)) {
        return TRUE;
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
        'event_id' => $eventId,
        'role_id' => "Host",
      ));
      if (!empty($result)) {
        return TRUE;
      }
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(ts('API Error finding event owner: %1', array(
        'domain' => 'com.aghstrategies.eventpermissions',
        1 => $error,
      )));
    }
    return FALSE;
  }

}
