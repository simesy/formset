<?php

namespace Drupal\formset;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Defines the access control handler for the formset entity type.
 *
 * @see \Drupal\formset\Entity\Formset.
 */
class FormsetEntityAccessControlHandler extends EntityAccessControlHandler implements EntityHandlerInterface {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Webform source entity plugin manager.
   *
   * @var \Drupal\webform\Plugin\WebformSourceEntityManagerInterface
   */
  protected $formsetSourceEntityManager;

  /**
   * Webform access rules manager service.
   *
   * @var \Drupal\webform\WebformAccessRulesManagerInterface
   */
  protected $accessRulesManager;

  /**
   * FormsetEntityAccessControlHandler constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeInterface $entity_type, RequestStack $request_stack, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($entity_type);

    $this->requestStack = $request_stack;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('request_stack'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    if ($account->hasPermission('administer formset')) {
      return AccessResult::allowed();
    }
    elseif ($account->hasPermission('create formset')) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::neutral();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    return AccessResult::allowed();
  }

}
