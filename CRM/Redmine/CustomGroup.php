<?php
/**
 * Class for CustomGroup configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Feb 2016
 * @license AGPL-3.0
 */
class CRM_Redmine_CustomGroup {

  protected $_apiParams = array();

  /**
   * CRM_Redmine_CustomGroup constructor.
   */
  public function __construct() {
    $this->_apiParams = array();
  }

  /**
   * Method to validate params for create
   *
   * @param $params
   * @throws Exception
   */
  private function validateCreateParams($params) {
    if (!isset($params['name']) || empty($params['name']) || !isset($params['extends']) ||
      empty($params['extends'])) {
      throw new Exception('When trying to create a Custom Group name and extends are mandatory parameters
      and can not be empty in class CRM_Redmine_CustomGroup');
    }
    $this->buildApiParams($params);
  }

  /**
   * Method to create custom group
   *
   * @param array $params
   * @return array
   * @throws Exception when error from API CustomGroup Create
   */
  public function create($params) {
    $this->validateCreateParams($params);
    $existing = $this->getWithName($this->_apiParams['name']);
    if (isset($existing['id'])) {
      $this->_apiParams['id'] = $existing['id'];
    }
    if (!isset($this->_apiParams['title']) || empty($this->_apiParams['title'])) {
      $this->_apiParams['title'] = CRM_Redmine_Utils::buildLabelFromName($this->_apiParams['name']);
    }
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Create', $this->_apiParams);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update custom group with name ' . $this->_apiParams['name']
        . ' to extend ' . $this->_apiParams['extends'] . ', error from API CustomGroup Create: ' .
        $ex->getMessage() . ", parameters : " . implode(";", $this->_apiParams));
    }
    return $customGroup['values'][$customGroup['id']];
  }

  /**
   * Method to get custom group with name
   *
   * @param string $name
   * @return array|bool
   */
  public function getWithName($name) {
    try {
      return civicrm_api3('CustomGroup', 'Getsingle', array('name' => $name));
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method to build api param list
   *
   * @param array $params
   */
  protected function buildApiParams($params) {
    $this->_apiParams = array();
    foreach ($params as $name => $value) {
      if ($name != 'fields') {
        $this->_apiParams[$name] = $value;
      }
    }
    switch ($this->_apiParams['extends']) {
      case "Activity":
        if (isset($this->_apiParams['extends_entity_column_value']) && !empty($this->_apiParams['extends_entity_column_value'])) {
          if (is_array($this->_apiParams['extends_entity_column_value'])) {
            foreach ($this->_apiParams['extends_entity_column_value'] as $extendsValue) {
              $activityType = new CRM_Redmine_ActivityType();
              $found = $activityType->getWithNameAndOptionGroupId($extendsValue, $activityType->getOptionGroupId());
              if (isset($found['value'])) {
                $this->_apiParams['extends_entity_column_value'][] = $found['value'];
              }
              unset ($activityType);
            }
          } else {
            $activityType = new CRM_Redmine_ActivityType();
            $found = $activityType->getWithNameAndOptionGroupId($this->_apiParams['extends_entity_column_value'], $activityType->getOptionGroupId());
            if (isset($found['value'])) {
              $this->_apiParams['extends_entity_column_value'] = $found['value'];
            }
          }
        }
        break;
      case "Membership":
        if (isset($this->_apiParams['extends_entity_column_value']) && !empty($this->_apiParams['extends_entity_column_value'])) {
          if (is_array($this->_apiParams['extends_entity_column_value'])) {
            foreach ($this->_apiParams['extends_entity_column_value'] as $extendsValue) {
              $membershipType = new CRM_Redmine_MembershipType();
              $found = $membershipType->getWithName($extendsValue);
              if (isset($found['id'])) {
                $this->_apiParams['extends_entity_column_value'][] = $found['id'];
              }
              unset ($membershipType);
            }
          } else {
            $membershipType = new CRM_Redmine_MembershipType();
            $found = $membershipType->getWithName($this->_apiParams['extends_entity_column_value']);
            if (isset($found['id'])) {
              $this->_apiParams['extends_entity_column_value'] = $found['id'];
            }
          }
        }
        break;
      case "Relationship":
        if (isset($this->_apiParams['extends_entity_column_value']) && !empty($this->_apiParams['extends_entity_column_value'])) {
          if (is_array($this->_apiParams['extends_entity_column_value'])) {
            foreach ($this->_apiParams['extends_entity_column_value'] as $extendsValue) {
              $relationshipType = new CRM_Redmine_RelationshipType();
              $found = $relationshipType->getWithNameAb($extendsValue);
              if (isset($found['id'])) {
                $this->_apiParams['extends_entity_column_value'][] = $found['id'];
              }
              unset ($relationshipType);
            }
          } else {
            $relationshipType = new CRM_Redmine_RelationshipType();
            $found = $relationshipType->getWithNameAb($this->_apiParams['extends_entity_column_value']);
            if (isset($found['id'])) {
              $this->_apiParams['extends_entity_column_value'] = $found['id'];
            }
          }
        }
        break;
      case "ParticipantEventType":
        if (isset($this->_apiParams['extends_entity_column_value']) && !empty($this->_apiParams['extends_entity_column_value'])) {
          if (is_array($this->_apiParams['extends_entity_column_value'])) {
            foreach ($this->_apiParams['extends_entity_column_value'] as $extendsValue) {
              $eventType = new CRM_Redmine_EventType();
              $found = $eventType->getWithNameAndOptionGroupId($extendsValue, $eventType->getOptionGroupId());
              if (isset($found['value'])) {
                $this->_apiParams['extends_entity_column_value'][] = $found['value'];
              }
              unset ($eventType);
            }
          } else {
            $eventType = new CRM_Redmine_EventType();
            $found = $eventType->getWithNameAndOptionGroupId($this->_apiParams['extends_entity_column_value'], $eventType->getOptionGroupId());
            if (isset($found['value'])) {
              $this->_apiParams['extends_entity_column_value'] = $found['value'];
            }
          }
        }
        break;
    }
  }
}
