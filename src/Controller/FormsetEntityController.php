<?php

namespace Drupal\formset\Controller;

use Drupal\webform\Controller\WebformEntityController;
use Drupal\Core\Render\RendererInterface;
use Drupal\webform\WebformInterface;
use Drupal\webform\WebformRequestInterface;
use Drupal\webform\WebformTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides route responses for form set.
 */
class FormsetEntityController extends WebformEntityController {

  /**
   * Form set request handler.
   *
   * @var \Drupal\webform\FormsetRequestInterface
   */
  //protected $requestHandler;

  /**
   * Constructs a WebformEntityController object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\webform\WebformRequestInterface $request_handler
   *   The webform request handler.
   * @param \Drupal\webform\WebformTokenManagerInterface $token_manager
   *   The webform token manager.
   */
  public function __construct(RendererInterface $renderer, WebformRequestInterface $request_handler, WebformTokenManagerInterface $token_manager) {
    $this->renderer = $renderer;
    $this->requestHandler = $request_handler;
    $this->tokenManager = $token_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('webform.request'),
      $container->get('webform.token_manager')
    );
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\formset\FormsetInterface|null $formset
   *   A formset.
   *
   * @return string
   *   The formset label as a render array.
   */
  public function title(WebformInterface $formset = NULL) {
    if ($formset) {
      return $this->t('Form set: ' . $formset->id(), []);
    }
    else {
      return $this->t('Form set', []);
    }
  }

}
