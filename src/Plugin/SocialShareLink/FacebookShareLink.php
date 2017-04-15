<?php

namespace Drupal\social_share\Plugin\SocialShareLink;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Plugin\ContextAwarePluginBase;
use Drupal\Core\Template\Attribute;
use Drupal\social_share\SocialShareLinkInterface;

/**
 * A social share link for facebook.
 *
 * @SocialShareLink(
 *   id = "social_share_facebook",
 *   label = @Translation("Facebook"),
 *   category = @Translation("Default"),
 *   context = {
 *     "text" = @ContextDefinition(
 *       data_type = "string",
 *       label = @Translation("Link text"),
 *       description = @Translation("The text of the sharing link.")
 *     ),
 *     "title" = @ContextDefinition("string",
 *       label = @Translation("Title"),
 *       description = @Translation("The title of the shared item.")
 *     ),
 *     "description" = @ContextDefinition("string",
 *       label = @Translation("Description"),
 *       description = @Translation("The description text to use for sharing."),
 *       required = false
 *     ),
 *     "description_short" = @ContextDefinition("string",
 *       label = @Translation("Description (short)"),
 *       description = @Translation("A short description text to use for sharing. Maximum length is 140 characters."),
 *       required = false
 *     ),
 *     "caption" = @ContextDefinition("string",
 *       label = @Translation("Caption"),
 *       description = @Translation("The caption used for sharing."),
 *       required = false
 *     ),
 *     "url" = @ContextDefinition("uri",
 *       label = @Translation("Shared URL"),
 *       description = @Translation("The URL to share. Defaults to the current page."),
 *       required = false
 *     ),
 *     "media_url" = @ContextDefinition("string",
 *       label = @Translation("Media-Url"),
 *       description = @Translation("The URL of some media to use for sharing."),
 *       required = false
 *     ),
 *     "media_image_url" = @ContextDefinition("string",
 *       label = @Translation("Media Image-Url"),
 *       description = @Translation("If some non-image media is shared, an optional preview image to use for sharing."),
 *       required = false
 *     ),
 *     "facebook_ref" = @ContextDefinition("string",
 *       label = @Translation("Facebook ref"),
 *       description = @Translation("Some comma-separated list of extra arguments to pass to facebook."),
 *       required = false
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
  public function build() {
    $render =  [
      '#theme' => $this->templateName,
      '#attributes' => new Attribute([])
    ];
    foreach ($this->getContexts() as $name => $context) {
      $render["#$name"] = $context->getContextValue();
    }
    return $render;
  }

  /**
   * {@inheritdoc}
   */
  public function getTemplateInfo() {
    $info = [];
    foreach ($this->getContextDefinitions() as $name => $definition) {
      $info['variables'][$name] = $definition->getDefaultValue();
    }
    return [
      $this->templateName => $info,
    ];
  }

}
