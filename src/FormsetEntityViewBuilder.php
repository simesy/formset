<?php

namespace Drupal\formset;

use Drupal\Core\Entity\EntityInterface;
use Drupal\webform\WebformEntityViewBuilder;

/**
 * Render controller for formset.
 *
 * We probably don't need this, just here to make the formset entity type work.
 */
class FormsetEntityViewBuilder extends WebformEntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    /* @var $entity \Drupal\formset\FormsetInterface */
    return $entity->getSubmissionForm();
  }

}
