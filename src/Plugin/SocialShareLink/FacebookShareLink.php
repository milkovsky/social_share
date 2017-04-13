<?php

namespace Drupal\social_share\Plugin\SocialShareLink;

use Drupal\Core\Plugin\ContextAwarePluginBase;
use Drupal\social_share\SocialShareLinkInterface;

/**
 * A social share link for facebook.
 *
 *
 * @SocialShareLink(
 *   id = "social_share_facebook",
 *   label = @Translation("Facebook"),
 *   context = {
 *     "url" = @ContextDefinition("uri",
 *       label = @Translation("Url"),
 *       description = @Translation("The URL to share."),
 *     ),
 *     "media_url" = @ContextDefinition("uri",
 *       label = @Translation("Media-Url"),
 *       description = @Translation("The URL of the image to use for sharing."),
 *     ),
 *     "description" = @ContextDefinition("string",
 *       label = @Translation("Description"),
 *       description = @Translation("The description text to use for sharing."),
 *     ),
 *   }
 * )
 */
class FacebookShareLink extends ContextAwarePluginBase implements SocialShareLinkInterface {

  /**
   * The machine name of the template used.
   *
   * @var string
   */
  protected $templateName = 'social_share_link_facebook';

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      // @todo: Autogenearte based upon context definitions.
    ];
  }

  public function getTemplateInfo() {
    return [
      'social_share_link_facebook' => [
        // @todo: Autogenearte based upon context definitions.
      ]
    ];
  }

}
