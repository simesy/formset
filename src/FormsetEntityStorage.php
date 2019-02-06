<?php

namespace Drupal\formset;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Storage controller class for "formset" configuration entities.
 */
class FormsetEntityStorage extends ConfigEntityStorage {

  /**
   * Active database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Associative array container total results for all webforms.
   *
   * @var array
   */
  protected $totals;

  /**
   * Constructs a FormsetEntityStorage object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid_service
   *   The UUID service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection to be used.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeInterface $entity_type, ConfigFactoryInterface $config_factory, UuidInterface $uuid_service, LanguageManagerInterface $language_manager, Connection $database, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($entity_type, $config_factory, $uuid_service, $language_manager);
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('config.factory'),
      $container->get('uuid'),
      $container->get('language_manager'),
      $container->get('database'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function save(EntityInterface $entity) {
    $return = parent::save($entity);
    if ($return === SAVED_NEW) {
      // Insert formset database record used for transaction tracking.
      $this->database->insert('formset')
        ->fields([
          'formset_id' => $entity->id(),
        ])
        ->execute();
    }
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $entities) {
    parent::delete($entities);
    if (!$entities) {
      // If no entities were passed, do nothing.
      return;
    }

    // Delete all webform submission log entries.
    $formset_id = [];
    foreach ($entities as $entity) {
      $formset_id[] = $entity->id();
    }

    // Delete all webform records used to track next serial.
    $this->database->delete('formset')
      ->condition('formset_id', $formset_id, 'IN')
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getCategories($template = NULL) {
    $webforms = $this->loadMultiple();
    $categories = [];
    foreach ($webforms as $webform) {
      if ($template !== NULL && $webform->get('template') != $template) {
        continue;
      }
      if ($category = $webform->get('category')) {
        $categories[$category] = $category;
      }
    }
    ksort($categories);
    return $categories;
  }


  /**
   * Not relevent for form sets.
   */
  public function getTotalNumberOfResults($formset_id = NULL) {
    return 0;
  }

}
