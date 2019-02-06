<?php

namespace Drupal\formset\EntitySettings;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityForm;

/**
 * Formset general settings.
 */
class FormsetEntitySettingsGeneralForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\webform\WebformInterface $webform */
    $formset = $this->entity;

    // General settings.
    $form['general_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('General settings'),
      '#open' => TRUE,
    ];
    $form['general_settings']['id'] = [
      '#type' => 'item',
      '#title' => $this->t('ID'),
      '#markup' => $formset->id(),
      '#value' => $formset->id(),
    ];
    $form['general_settings']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#value' => $formset->label(),
    ];
    $form['general_settings']['description'] = [
      '#type' => 'webform_html_editor',
      '#title' => $this->t('Administrative description'),
      '#default_value' => $formset->get('description'),
    ];

    return $form;
  }

}
