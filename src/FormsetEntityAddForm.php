<?php

namespace Drupal\formset;

use Drupal\webform\WebformEntityAddForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a webform add form.
 */
class FormsetEntityAddForm extends WebformEntityAddForm {

//  use WebformDialogFormTrait;
//
  /**
   * {@inheritdoc}
   */
  protected function prepareEntity() {
    if ($this->operation == 'duplicate') {
      $this->setEntity($this->getEntity()->createDuplicate());
    }
    parent::prepareEntity();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form = parent::form($form, $form_state);

    /** @var \Drupal\webform\WebformInterface $webform */
    $formset = $this->getEntity();

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $formset->id() ?? 'formset' . date('Ymdhi'),
      '#machine_name' => [
        'exists' => '\Drupal\formset\Entity\Formset::load',
        'source' => ['title'],
        'label' => '<br/>' . $this->t('Machine name'),
      ],
      '#maxlength' => 32,
      '#field_suffix' => ' (' . $this->t('Maximum @max characters', ['@max' => 32]) . ')',
      '#disabled' => (bool) $formset->id() && $this->operation != 'duplicate',
      '#required' => TRUE,
    ];

    // Webform fields we don't really need, leave them hidden.
    $form['description']['#required'] = FALSE;
    $form['status']['#type'] = 'hidden';
    $form['status']['#default_value'] = FormsetInterface::STATUS_OPEN;
    unset($form['category']);

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\formset\FormsetInterface $formset */
    $formset = $this->getEntity();
    $formset->save();
    $context = [
      '@label' => $formset->label(),
      'link' => $formset->toLink($this->t('Edit'), 'settings')->toString(),
    ];
    $t_args = ['%label' => $formset->label()];
    $this->logger('formset')->notice('Formset @label created.', $context);
    $this->messenger()->addStatus($this->t('Formset %label created.', $t_args));

    $form_state->setRedirectUrl(Url::fromRoute('entity.formset.settings', ['formset' => $this->getEntity()->id()]));
  }

}
