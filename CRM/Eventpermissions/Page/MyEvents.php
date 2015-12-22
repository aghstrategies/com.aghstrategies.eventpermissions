<?php

require_once 'CRM/Core/Page.php';

class CRM_Eventpermissions_Page_MyEvents extends CRM_Core_Page {
  public function run() {
    // Get link options for managing events.
    $enableCart = CRM_Core_BAO_Setting::getItem(CRM_Core_BAO_Setting::EVENT_PREFERENCES_NAME,
      'enable_cart'
    );
    $tabs = CRM_Event_Page_ManageEvent::tabs($enableCart);

    foreach ($tabs as $tab => $tabInfo) {
      $tabs[$tab]['name'] = $tabInfo['title'];
      $tabs[$tab]['qs'] = ($tab == 'reminder') ? 'reset=1&action=browse&id=%%id%%' : 'reset=1&action=update&id=%%id%%';
    }

    $utils = new CRM_Eventpermissions_Utils();
    $events = array();
    foreach ($utils->myUpcomingEvents() as $id => $title) {
      $events[] = array(
        'title' => CRM_Utils_System::href($title, 'civicrm/event/info', "id=$id&reset=1"),
        'links' => CRM_Core_Action::formLink($tabs,
          NULL,
          array('id' => $id),
          ts('Configure', array('domain' => 'com.aghstrategies.eventpermissions')),
          TRUE,
          'eventpermissions.myevents.list',
          'Event',
          $id
        ),
      );
    }
    $this->assign('events', $events);

    parent::run();
  }
}
