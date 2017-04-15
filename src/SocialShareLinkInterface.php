<?php

namespace Drupal\social_share;

use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Interface for social share links.
 *
 * @todo: Allow preparing of multi-rendering.
 */
interface SocialShareLinkInterface extends PluginInspectionInterface, ContextAwarePluginInterface {

  /**
   * Gets the render array for the link.
   *
   * Before calling this, all required context must be set on the plugin.
   *
   * @return mixed[]
   *   The render array.
   */
  public function build();

  /**
   * Gets the template info for the link's template(s).
   *
   * Note that there as no plugin configuration available when this method is
   * called.
   *
   * @return array[]
   *   An array as it would be returned by a hook_theme() implementation.
   */
  public function getTemplateInfo();

}
