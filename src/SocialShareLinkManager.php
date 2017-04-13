<?php

namespace Drupal\social_share;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\social_share\Annotation\SocialShareLink;

/**
 * Manager for social share link plugins.
 *
 * @see \Drupal\social_share\SocialShareLinkInterface
 */
class SocialShareLinkManager extends DefaultPluginManager implements SocialShareLinkManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, ModuleHandlerInterface $module_handler, $plugin_definition_annotation_name = SocialShareLink::class) {
    $this->alterInfo('social_share_link');
    parent::__construct('Plugin/SocialShareLink', $namespaces, $module_handler, SocialShareLinkInterface::class, $plugin_definition_annotation_name);
  }

}
