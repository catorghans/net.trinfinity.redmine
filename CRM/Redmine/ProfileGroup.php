<?php

/**
 * Created by PhpStorm.
 * User: hans
 * Date: 3-2-16
 * Time: 17:00
 */
class CRM_Redmine_ProfileGroup
{
    protected $_apiParams = array();

    /**
     * CRM_Redmine_ProfileGroup constructor.
     *
     * @param array $apiParams
     * @throws Exception when name not in apiParams
     */
    public function __construct($apiParams) {
        if (!isset($apiParams['name']) || empty($apiParams['name'])) {
            throw new Exception('Missing mandatory param name when constructing class CRM_Redmine_ProfileGroup');
        }
        $this->_apiParams = $apiParams;
    }

    /**
     * Method to create or update a tag
     * @return mixed
     * @throws Exception when error from API Tag Create
     */
    public function create() {
        $existingProfileGroup = $this->getProfileGroupWithName();
        if (isset($existingProfileGroup['id'])) {
            $this->_apiParams['id'] = $existingProfileGroup['id'];
        }
        if (!isset($this->_apiParams['is_active'])) {
            $this->_apiParams['is_active'] = 1;
            if (empty($this->_apiParams['description']) || !isset($this->_apiParams['description'])) {
                $this->_apiParams['description'] = CRM_Redmine_Utils::buildLabelFromName($this->_apiParams['name']);
            }
        }
        try {
            $group = civicrm_api3('UFGroup', 'Create', $this->_apiParams);
            $groupData = $group['values'];
        } catch (CiviCRM_API3_Exception $ex) {
            throw new Exception('Could not create or update profile group with name'
                .$this->_apiParams['name'].', error from API UFGroup Create: ' . $ex->getMessage());
        }
        return $groupData[$group["id"]];
    }

    /**
     * Function to get the tag with a name
     *
     * @return array|bool
     * @access public
     * @static
     */
    public function getProfileGroupWithName() {
        try {
            $groups = civicrm_api3('UFGroup', 'Get', array('name' => array('LIKE' => $this->_apiParams['name']."%")));
            $foundgroup = null;

            foreach ($groups["values"] as $groupid => $group){
                if ($group["id"]){
                    $groupid = $group["id"];
                    if ($group["name"] ==  $this->_apiParams['name']."_".$groupid){
                        $foundgroup = $group;
                        break;
                    }
                    elseif ($group["name"] == $this->_apiParams["name"]){
                        $foundgroup = $group;
                        break;
                    }
                }

            }
            return $foundgroup;
        } catch (CiviCRM_API3_Exception $ex) {
            return FALSE;
        }
    }
}
