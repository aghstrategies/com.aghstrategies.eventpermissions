<?php

/**
 * Miscellaneous utilities.
 */
class CRM_Eventpermissions_Utils {
  /**
   * The participant_role_id with the permission to edit an event.
   *
   * @type int
   */
  private $hostId = NULL;

  // TODO: Get role better than this.
  public function getHostId() {
    if (empty($this->hostId)) {
      try {
        $result = civicrm_api3('Participant', 'getoptions', array('field' => "participant_role_id"));
        foreach ($result['values'] as $k => $v) {
          if (strtolower($v) == 'host') {
            $this->hostId = $k;
            break;
          }
        }
      }
      catch (CiviCRM_API3_Exception $e) {
        $error = $e->getMessage();
        CRM_Core_Error::debug_log_message(ts('API Error finding ID of "host" participant role: %1', array(
          'domain' => 'com.aghstrategies.eventpermissions',
          1 => $error,
        )));
      }
    }
    return $this->hostId;
  }

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

  public function myUpcomingEvents() {
    $contactId = CRM_Core_Session::singleton()->get('userID');
    $curDate = date('YmdHis');
    $hostID = $this->getHostId();
    if (empty($hostID)) {
      $hostID = 0;
    }
    $query = "SELECT e.id, e.title, e.participant_listing_id
      FROM civicrm_event e
      LEFT JOIN civicrm_participant p
        ON p.event_id = e.id
        AND p.role_id = {$hostID}
        AND p.contact_id = {$contactId}
      WHERE (e.end_date >= {$curDate} OR e.end_date IS NULL)
        AND (e.created_id = {$contactId} OR p.id IS NOT NULL)";
    $events = CRM_Core_DAO::executeQuery($query);
    $return = array();
    while ($events->fetch()) {
      $return[$events->id] = array(
        'title' => $events->title,
        'participant_listing_id' => $events->participant_listing_id,
      );
    }
    return $return;
  }

}
