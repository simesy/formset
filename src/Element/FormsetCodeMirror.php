<?php

namespace Drupal\formset\Element;

use Drupal\webform\Element\WebformCodeMirror;
use Drupal\Core\Form\FormStateInterface;

/**
 * Overriding class for webform_codemirror Element plugin.
 */
class FormsetCodeMirror extends WebformCodeMirror {

  /**
   * @InheritDoc
   */
  protected static function validateTwig($element, FormStateInterface $form_state, $complete_form) {

    if ('Drupal\formset\Entity\Formset' == get_class($form_state->getFormObject()->getWebform())) {
      // validateTwig tries to create a "test submission", which we don't support.
      return;
    }
    else {
      return parent::validateTwig($element, $form_state, $complete_form);
    }
  }

}
