<?php

/**
 * @file
 * Contains membership_offer.page.inc.
 *
 * Page callback for Membership Offer entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Membership Offer templates.
 *
 * Default template: membership_offer.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_membership_offer(array &$variables) {
  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
