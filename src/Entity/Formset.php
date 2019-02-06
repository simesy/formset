<?php

namespace Drupal\formset\Entity;

use Drupal\formset\FormsetInterface;
use Drupal\webform\Entity\Webform;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the form set entity.
 *
 * @ConfigEntityType(
 *   id = "formset",
 *   label = @Translation("Form set"),
 *   handlers = {
 *     "storage" = "\Drupal\formset\FormsetEntityStorage",
 *     "view_builder" = "Drupal\formset\FormsetEntityViewBuilder",
 *     "list_builder" = "Drupal\formset\FormsetEntityListBuilder",
 *     "access" = "Drupal\formset\FormsetEntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\formset\FormsetEntityAddForm",
 *       "duplicate" = "Drupal\formset\FormsetEntityAddForm",
 *       "delete" = "Drupal\formset\FormsetEntityDeleteForm",
 *       "edit" = "Drupal\formset\FormsetEntityElementsForm",
 *       "settings" = "Drupal\formset\EntitySettings\FormsetEntitySettingsGeneralForm",
 *       "export" = "Drupal\formset\FormsetEntityExportForm",
 *     }
 *   },
 *   admin_permission = "administer formset",
 *   static_cache = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *   },
 *   links = {
 *     "canonical" = "/formset/{formset}",
 *     "access-denied" = "/formset/{formset}/access-denied",
 *     "add-form" = "/formset/{formset}",
 *     "edit-form" = "/admin/structure/formset/manage/{formset}/elements",
 *     "test-form" = "/formset/{formset}/test",
 *     "duplicate-form" = "/admin/structure/formset/manage/{formset}/duplicate",
 *     "delete-form" = "/admin/structure/formset/manage/{formset}/delete",
 *     "export-form" = "/admin/structure/formset/manage/{formset}/export",
 *     "settings" = "/admin/structure/formset/manage/{formset}/settings",
 *     "settings-form" = "/admin/structure/formset/manage/{formset}/settings/form",
 *     "collection" = "/admin/structure/formset",
 *     "results-submissions" = "/not-used",
 *   },
 *   config_export = {
 *     "id",
 *     "uuid",
 *     "uid",
 *     "title",
 *     "description",
 *     "categories",
 *     "settings",
 *     "elements",
 *   },
 * )
 */
class Formset extends Webform implements FormsetInterface {

  public function preSave(EntityStorageInterface $storage) {
  }

  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
  }

  public function getSetting($key, $default = FALSE) {
    switch ($key) {
      case 'form_prepopulate':
      case 'prepopulate':
      case 'token_update':
      case 'form_confidential':
      case 'form_remote_addr':
      case 'form_prepopulate_source_entity':
      case 'draft':
      case 'draft_multiple':
      case 'limit_total_unique':
      case 'limit_user_unique':
      case 'autofill':
      case 'wizard_track':
        // These settings are mostly relevent to webform, maybe some will be used?
        return FALSE;
      default:
        $func = "poor_mans_exception__{$key}";
        $func();
    }
  }

}
