<?php

namespace Drupal\social_share;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\CategorizingPluginManagerTrait;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\social_share\Annotation\SocialShareLink;

/**
 * Manager for social share link plugins.
 *
 * @see \Drupal\social_share\SocialShareLinkInterface
 */
class SocialShareLinkManager extends DefaultPluginManager implements SocialShareLinkManagerInterface {

  use CategorizingPluginManagerTrait;

  /**
   * Constructs the object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, ModuleHandlerInterface $module_handler) {
    $this->alterInfo('social_share_link');
    parent::__construct('Plugin/SocialShareLink', $namespaces, $module_handler, SocialShareLinkInterface::class, SocialShareLink::class);
  }

}
