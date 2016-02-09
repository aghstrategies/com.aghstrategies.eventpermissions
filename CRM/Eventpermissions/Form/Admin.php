<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Eventpermissions_Form_Admin extends CRM_Core_Form {
  /**
   * Build the form.
   */
  public function buildQuickForm() {
    $this->add(
      'select',
      'permission_role',
      ts('Permissioned Role(s)', array('domain', 'com.aghstrategies.eventpermissions')),
      $this->getRoles(),
      FALSE,
      array(
        'class' => 'crm-select2',
        'multiple' => TRUE,
      )
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    return array('permission_role' => CRM_Eventpermissions_Utils::getHostId());
  }

  public function postProcess() {
    $values = $this->exportValues();
    CRM_Eventpermissions_Utils::setHostId($values['permission_role']);
    CRM_Core_Session::setStatus(ts('Permissioned role(s) updated.', array(
      'domain' => 'com.aghstrategies.eventpermissions',
    )));
    parent::postProcess();
  }

  /**
   * Retrieve the participant roles.
   *
   * @return array
   *   The roles, by ID.
   */
  public function getRoles() {
    try {
      $result = civicrm_api3('Participant', 'getoptions', array(
        'field' => "participant_role_id",
        'context' => "search",
      ));
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(ts('API Error finding role options: %1', array(
        'domain' => 'com.aghstrategies.eventpermissions',
        1 => $error,
      )));
    }
    return (empty($result['values'])) ? array() : $result['values'];
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
