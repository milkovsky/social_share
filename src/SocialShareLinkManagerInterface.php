<?php

namespace Drupal\social_share;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Interface for the social share link manager.
 */
interface SocialShareLinkManagerInterface extends PluginManagerInterface {

  /**
   * Creates a pre-configured instance of a social share link plugin.
   *
   * @param string $plugin_id
   *   The ID of the plugin being instantiated; i.e., the filter machine name.
   * @param array $configuration
   *   An array of configuration relevant to the plugin instance. As this plugin
   *   is not configurable, this is unused and should stay empty.
   *
   * @return \Drupal\social_share\SocialShareLinkInterface
   *   A fully configured plugin instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   If the instance cannot be created, such as if the ID is invalid.
   */
  public function createInstance($plugin_id, array $configuration = []);

}
