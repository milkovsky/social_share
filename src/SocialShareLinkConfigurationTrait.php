<?php

namespace Drupal\social_share;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\TypedData\TypedDataTrait;
use Drupal\typed_data\PlaceholderResolverTrait;

/**
 * Trait for helping with social share link configuration.
 *
 * @internal
 */
trait SocialShareLinkConfigurationTrait {

  use PlaceholderResolverTrait;
  use SocialShareLinkManagerTrait;
  use TypedDataTrait;

  /**
   * Prepares building the social link for the given plugin.
   *
   * @param $pluginId
   *   The ID of link to render.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   The bubbleable metadata used for collection render metadata.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   If given, an entity for which to add in placeholder tokens.
   *
   * @return \Drupal\social_share\SocialShareLinkInterface
   *   The social share link, ready for rendering.
   */
  protected function prepareLinkBuild($pluginId,BubbleableMetadata $bubbleable_metadata, EntityInterface $entity = NULL) {
    $link_manager = $this->getSocialShareLinkManager();

    $configuration = [];
    $share_link = $link_manager->createInstance($pluginId, $configuration);

    // Set the context on the plugin.
    foreach ($share_link->getContextDefinitions() as $name => $definition) {
      // Process the context value.
      // @todo: Improve rules context API to make it better re-usable and
      // re-use it here.
      if (is_scalar($this->settings['context_values'][$name])) {
        $value =& $this->settings['context_values'][$name];
        $value = $this->getPlaceholderResolver()->replacePlaceholders($value, [
          $entity->getEntityTypeId() => $entity->getTypedData(),
        ], $bubbleable_metadata, ['clear' => TRUE]);
      }
      $share_link->setContextValue($name, $this->settings['context_values'][$name]);
    }
    return $share_link;
  }

  /**
   * Builds the configuration form for the used context.
   *
   * @param array $form
   *   The form to attach to.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param array $configuration
   *   The array of configuration values, containing the context configuration.
   * @param array $used_context
   *   The merged context definitions, as returned from
   *   \Drupal\social_share\SocialShareLinkManagerInterface::getMergedContextDefinitions().
   * @param $used_by_plugins
   *   The array mapping the context definitions names to plugin IDs as provided
   *   by \Drupal\social_share\SocialShareLinkManagerInterface::getMergedContextDefinitions().
   *
   * @return array
   *   The modified form array.
   */
  protected function buildContextConfigurationForm(array $form, FormStateInterface $form_state, array $configuration, array $used_context, array $used_by_plugins) {
    // @todo: Use context configuration traits for configuring this.
    $form['context_values']['#tree'] = TRUE;
    foreach ($used_context as $name => $context_definition) {
      $help = $this->t('Used by: %plugins', ['%plugins' => implode(', ', $used_by_plugins[$name])]);
      $form['context_values'][$name] = [
        '#type' => 'textfield',
        '#title' => $context_definition->getLabel(),
        '#description' => $context_definition->getDescription() . ' ' . $help,
        '#default_value' => isset($configuration['context_values'][$name]) ? $configuration['context_values'][$name] : $context_definition->getDefaultValue(),
        '#required' => $context_definition->isRequired(),
      ];
    }
    return $form;
  }

}
