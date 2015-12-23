<?php

return array(
  array(
    'name' => 'eventpermissions_myevents',
    'entity' => 'Dashboard',
    'params' => array(
      'version' => '3',
      'name' => "eventpermissions_myevents",
      'label' => "My Events",
      'url' => "civicrm/dashlet/myEvents?reset=1&snippet=5",
      'permission' => "access CiviEvent",
      'is_reserved' => 1,
      'is_fullscreen' => 0,
      'is_active' => 1,
    ),
  ),
);
