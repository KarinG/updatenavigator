<?php

require_once 'primarycontact.civix.php';

use CRM_Primarycontact_ExtensionUtil as E;

/**
 * When a new Primary Contact relationship is being made for an Organization,
 * this code makes previous Primary Contacts for that Organization inactive,
 * with an ending date of the creation date of the new relationship
 * so that there is only one Primary Contact per Organization.
 */
function primarycontact_civicrm_postCommit($op, $objectName, $objectId, &$objectRef) {
  if ($op === 'create' && $objectName === 'Relationship') {
    $primaryRelationshipId = current(\Civi\Api4\RelationshipType::get(FALSE)
      ->addSelect('id')
      ->addWhere('name_b_a', '=', 'Primary Contact is')
      ->execute()
      ->first());
    if ($objectRef->relationship_type_id == $primaryRelationshipId) {
      $today = (new \DateTime())->format('Y-m-d');
      \Civi\Api4\Relationship::update(FALSE)
        ->addValue('is_active', FALSE)
        ->addValue('end_date', $today)
        ->addWhere('contact_id_b', '=', $objectRef->contact_id_b)
        ->addWhere('relationship_type_id', '=', $primaryRelationshipId)
        ->addWhere('id', '!=', $objectId)
        ->execute();
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function primarycontact_civicrm_config(&$config): void {
  _primarycontact_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function primarycontact_civicrm_install(): void {
  _primarycontact_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function primarycontact_civicrm_enable(): void {
  _primarycontact_civix_civicrm_enable();
}
