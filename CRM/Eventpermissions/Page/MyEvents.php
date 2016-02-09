<?php

require_once 'CRM/Core/Page.php';

/**
 * My Events widget.
 *
 * This sets up the widget for listing events where you are the owner or the
 * "host".  That is, these are the events that you can edit even if you don't
 * have the "edit all events" permission.
 */
class CRM_Eventpermissions_Page_MyEvents extends CRM_Core_Page {
  /**
   * Basic page run function.
   */
  public function run() {
    // Get link options for managing events.
    $enableCart = CRM_Core_BAO_Setting::getItem(CRM_Core_BAO_Setting::EVENT_PREFERENCES_NAME,
      'enable_cart'
    );
    $tabs = CRM_Event_Page_ManageEvent::tabs($enableCart);
    foreach ($tabs as $tab => $tabInfo) {
      $tabs[$tab]['name'] = $tabInfo['title'];
      $tabs[$tab]['qs'] = ($tab == 'reminder') ? 'reset=1&action=browse&setTab=1&id=%%id%%' : 'reset=1&action=update&id=%%id%%';
    }

    // Get link options for participant listings.
    $statusTypes = CRM_Event_PseudoConstant::participantStatus(NULL, 'is_counted = 1');
    $statusTypesPending = CRM_Event_PseudoConstant::participantStatus(NULL, 'is_counted = 0');
    $participantLinks = array();
    if (1 ||!empty($statusTypes)) {
      $participantLinks[2] = array(
        'name' => implode(', ', array_values($statusTypes)),
        'url' => 'civicrm/event/search',
        'qs' => 'reset=1&force=1&status=true&event=%%id%%',
        'title' => ts('Counted', array('domain' => 'com.aghstrategies.eventpermissions')),
      );
    }
    if (!empty($statusTypesPending)) {
      $participantLinks[3] = array(
        'name' => implode(', ', array_values($statusTypesPending)),
        'url' => 'civicrm/event/search',
        'qs' => 'reset=1&force=1&status=false&event=%%id%%',
        'title' => ts('Not Counted', array('domain' => 'com.aghstrategies.eventpermissions')),
      );
    }
    $participantLinks[4] = array(
      'name' => ts('Public Participant Listing', array('domain' => 'com.aghstrategies.eventpermissions')),
      'url' => 'civicrm/event/participant',
      'qs' => 'reset=1&id=%%id%%',
      'title' => ts('Public Participant Listing', array('domain' => 'com.aghstrategies.eventpermissions')),
    );

    // Get link options for event links.
    $eventLinks = array(
      array(
        'name' => ts('Register Participant', array('domain' => 'com.aghstrategies.eventpermissions')),
        'url' => 'civicrm/participant/add',
        'qs' => 'reset=1&action=add&context=standalone&eid=%%id%%',
        'title' => ts('Register Participant', array('domain' => 'com.aghstrategies.eventpermissions')),
      ),
      array(
        'name' => ts('Event Info', array('domain' => 'com.aghstrategies.eventpermissions')),
        'url' => 'civicrm/event/info',
        'qs' => 'reset=1&id=%%id%%',
        'title' => ts('Event Info', array('domain' => 'com.aghstrategies.eventpermissions')),
      ),
      array(
        'name' => ts('Online Registration (Test-drive)', array('domain' => 'com.aghstrategies.eventpermissions')),
        'url' => 'civicrm/event/register',
        'qs' => 'reset=1&action=preview&id=%%id%%',
        'title' => ts('Online Registration (Test-drive)', array('domain' => 'com.aghstrategies.eventpermissions')),
      ),
      array(
        'name' => ts('Online Registration (Live)', array('domain' => 'com.aghstrategies.eventpermissions')),
        'url' => 'civicrm/event/register',
        'qs' => 'reset=1&id=%%id%%',
        'title' => ts('Online Registration (Live)', array('domain' => 'com.aghstrategies.eventpermissions')),
      ),
    );

    $events = array();
    foreach (CRM_Eventpermissions_Utils::myUpcomingEvents() as $id => $event) {
      $events[] = array(
        'title' => CRM_Utils_System::href($event['title'], 'civicrm/event/info', "id=$id&reset=1"),
        'links' => CRM_Core_Action::formLink($tabs,
          NULL,
          array('id' => $id),
          ts('Configure', array('domain' => 'com.aghstrategies.eventpermissions')),
          TRUE,
          'eventpermissions.myevents.configure',
          'Event',
          $id
        ),
        'participantLinks' => CRM_Core_Action::formLink($participantLinks,
          ($event['participant_listing_id']) ? 6 : 3,
          array('id' => $id),
          ts('Participants', array('domain' => 'com.aghstrategies.eventpermissions')),
          TRUE,
          'eventpermissions.myevents.participants',
          'Event',
          $id
        ),
        'eventLinks' => CRM_Core_Action::formLink($eventLinks,
          NULL,
          array('id' => $id),
          ts('Event Links', array('domain' => 'com.aghstrategies.eventpermissions')),
          TRUE,
          'eventpermissions.myevents.links',
          'Event',
          $id
        ),
      );
    }
    $this->assign('events', $events);

    parent::run();
  }

}
