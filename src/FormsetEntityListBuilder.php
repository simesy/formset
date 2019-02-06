<?php

namespace Drupal\formset;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\webform\WebformEntityListBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Defines a class to build a listing of formset entities.
 *
 * @see \Drupal\formset\Entity\Formset
 */
class FormsetEntityListBuilder extends WebformEntityListBuilder {

  use ToknApiConnectionTrait;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Search keys.
   *
   * @var string
   */
  protected $keys;

  /**
   * Search category.
   *
   * @var string
   */
  protected $category;

  /**
   * Search state.
   *
   * @var string
   */
  protected $state;

  /**
   * Webform submission storage.
   *
   * @var \Drupal\webform\WebformSubmissionStorageInterface
   */
  protected $submissionStorage;

  /**
   * User storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Role storage.
   *
   * @var \Drupal\user\RoleStorageInterface
   */
  protected $roleStorage;

  /**
   * @var \Drupal\uscan_object\ToknApi
   *   The Tokn api service.
   */
  protected $api;

  protected $categories;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    EntityStorageInterface $storage,
    RequestStack $request_stack,
    AccountInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager) {

    parent::__construct($entity_type, $storage, $request_stack, $current_user, $entity_type_manager);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $header = $this->buildHeader();
    $query = $this->getQuery($this->keys, $this->category, $this->state);
    $query->tableSort($header);
    $query->pager($this->getLimit());
    return $query->execute();
  }

  /**
   * Build information summary.
   *
   * @return array
   *   A render array representing the information summary.
   */
  protected function buildInfo() {
    // Display info.
    if ($this->currentUser->hasPermission('administer formset') && ($total = $this->getTotal($this->keys, $this->category, $this->state))) {
      return [
        '#markup' => $this->formatPlural($total, '@total form set', '@total form sets', ['@total' => $total]),
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];
    }
    else {
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = parent::buildHeader();
    // unset($header['title']);
    unset($header['results']);
    unset($header['description']);
    unset($header['status']);
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    // Operations.
    $parents = parent::buildRow($entity);

    $row = [
      'title' => $parents['title'],
      'category' => '', // Not used in proof of concept.
      'owner' => ($owner = $entity->getOwner()) ? $owner->toLink() : '',
      'operations' => $parents['operations'],
    ];

    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity, $type = 'edit') {
    $operations = parent::getDefaultOperations($entity, $type = 'edit');
    unset($operations['submission_page']);
    unset($operations['submission_view_any']);
    unset($operations['results']);
    return $operations;
  }

  /**
   * Get the base entity query filtered by webform and search.
   *
   * @param string $keys
   *   (optional) Search key.
   * @param string $category
   *   (optional) Category.
   * @param string $state
   *   (optional) Webform state. Can be 'open' or 'closed'.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   An entity query.
   */
  protected function getQuery($keys = '', $category = '', $state = '') {
    $query = $this->getStorage()->getQuery();
    return $query;
  }

  /**
   * Is the current user a webform administrator.
   *
   * @return bool
   *   TRUE if the current user has 'administer webform' or 'edit any webform'
   *   permission.
   */
  protected function isAdmin() {
    $account = $this->currentUser;
    return ($account->hasPermission('administer formset') || $account->hasPermission('edit any formset'));
  }

}
