<?php

namespace Drupal\social_share\Plugin\Field\FieldFormatter;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\social_share\SocialShareLinkManagerTrait;

/**
 * Plugin implementation of the 'social_share_link' formatter.
 *
 * @FieldFormatter(
 *   id = "social_share_link",
 *   label = @Translation("Social share link"),
 *   field_types = {
 *     "social_share_link",
 *   }
 * )
 */
class SocialShareLinkFormatter extends FormatterBase {

  use SocialShareLinkManagerTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $link_manager = $this->getSocialShareLinkManager();

    foreach ($items as $delta => $item) {
      try {
        // @todo: Prepare configuration / context.
        $configuration = [];
        $share_link = $link_manager->createInstance($item->value, $configuration);

        $share_link->setContextValue('text', 'example');
        $share_link->setContextValue('url', 'http://orf.at');
        $share_link->setContextValue('media_url', 'http://placeimg.com/640/480/any');
        $share_link->setContextValue('title', 'Share it');
        $share_link->setContextValue('description', 'example2');

        $elements[$delta] = $share_link->build();
      }
      catch (PluginException $e) {
        // Silently ignore possibly outdated data values of not existing share
        // links.
      }
    }

    return $elements;
  }

}
