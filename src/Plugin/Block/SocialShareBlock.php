<?php

namespace Drupal\social_share\Plugin\Block\SocialShareBlock;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\social_share\SocialShareLinkConfigurationTrait;

/**
 * Provides a 'Social share links' block.
 *
 * @Block(
 *   id = "social_share_links",
 *   admin_label = @Translation("Social share links"),
 *   category = @Translation("Social"),
 *   context = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Content entity"),
 *       description = @Translation("An optional content entity which may be used as source for replacement tokens, accessible under the name 'entity'."),
 *       required = false,
 *     )
 *   }
 * )
 */
class SocialShareBlock extends BlockBase {

  use SocialShareLinkConfigurationTrait;

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

  }
}
