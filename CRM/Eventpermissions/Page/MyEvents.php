<?php

require_once 'CRM/Core/Page.php';

class CRM_Eventpermissions_Page_MyEvents extends CRM_Core_Page {
  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('My Events', array('domain' => 'com.aghstrategies.eventpermissions')));

    $enableCart = CRM_Core_BAO_Setting::getItem(CRM_Core_BAO_Setting::EVENT_PREFERENCES_NAME,
      'enable_cart'
    );

    // Get link options for managing events.
    $tabs = CRM_Event_Page_ManageEvent::tabs($enableCart);

    foreach ($tabs as $tab => $tabInfo) {
      $tabs[$tab]['name'] = $tabInfo['title'];
      $tabs[$tab]['qs'] = 'reset=1&id=%%id%%';
    }


    // Example: Assign a variable for use in a template
    $utils = new CRM_Eventpermissions_Utils;
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
