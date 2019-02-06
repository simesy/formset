<?php

namespace Drupal\formset\PathProcessor;

use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\HttpFoundation\Request;

/**
 * Path processor for formset.
 */
class FormsetPathProcessor implements OutboundPathProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function processOutbound($path, &$options = [], Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL) {

    // We reuse the webform_ui elements, and have to tweak some paths in the
    // admin ui, which keep trying to direct back to webform admin paths. It
    // would be possible to override the controller/form class, but this is much
    // less code.
    if ($request) {
      $uri = $request->getRequestUri();
      // On the formset manage page.
      if ($request && strpos($uri, '/admin/structure/formset/manage') === 0) {
        // And a link is being generate back to a webform element admin path.
        if (strpos($path, 'admin/structure/webform/manage') && strpos($path, '/element/add')) {
          // Point it back to formset.
          return str_replace( 'admin/structure/webform/manage', 'admin/structure/formset/manage', $path);
        }
      }
    }

    return $path;
  }
}
