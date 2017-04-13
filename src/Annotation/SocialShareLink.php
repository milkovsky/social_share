<?php

namespace Drupal\social_share\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Annotation class for social share links.
 *
 * @Annotation
 */
class SocialShareLink extends Plugin {

  /**
   * The machine-name of the plugin.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
