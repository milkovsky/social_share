<?php

namespace Drupal\social_share\Plugin\Field\FieldFormatter;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\TypedData\TypedDataTrait;
use Drupal\social_share\SocialShareLinkManagerTrait;
use Drupal\typed_data\PlaceholderResolverTrait;

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

  use PlaceholderResolverTrait;
  use SocialShareLinkManagerTrait;
  use TypedDataTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $entity = $items->getEntity();
    $link_manager = $this->getSocialShareLinkManager();
    $bubbleable_metadata = new BubbleableMetadata();

    foreach ($items as $delta => $item) {
      try {
        $configuration = [];
        $share_link = $link_manager->createInstance($item->value, $configuration);

        // Set the context on the plugin.
        foreach ($share_link->getContextDefinitions() as $name => $definition) {
          // Process the context value.
          // @todo: Improve rules context API to make it better re-usable and
          // re-use it here.
          if (is_scalar($this->settings['context_values'][$name])) {
            $value =& $this->settings['context_values'][$name];
            $value = $this->getPlaceholderResolver()->replacePlaceholders($value, [
              $entity->getEntityTypeId() => $entity->getTypedData(),
            ], $bubbleable_metadata);
          }
          $share_link->setContextValue($name, $this->settings['context_values'][$name]);
        }

        $elements[$delta] = $share_link->build();
      }
      catch (PluginException $e) {
        // Silently ignore possibly outdated data values of not existing share
        // links.
      }
    }
    $bubbleable_metadata->applyTo($elements);
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    // We cannot apply defaults here without having knowledge about the field
    // definition. Thus apply the defaults later. Still all setting keys must
    // be listed here, such that they get stored.
    return ['context_values' => []] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    list($used_context, $used_by_plugins) = $this->getMergedContextDefinitions();

    // @todo: Use context configuration traits for configuring this.
    $form['context_values']['#tree'] = TRUE;
    foreach ($used_context as $name => $context_definition) {
      $help = $this->t('Used by: %plugins', ['%plugins' => implode(', ', $used_by_plugins[$name])]);
      $form['context_values'][$name] = [
        '#type' => 'textfield',
        '#title' => $context_definition->getLabel(),
        '#description' => $context_definition->getDescription() . ' ' . $help,
        '#default_value' => isset($this->settings['context_values'][$name]) ? $this->settings['context_values'][$name] : $context_definition->getDefaultValue(),
        '#required' => $context_definition->isRequired(),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    list($used_context, $used_by_plugins) = $this->getMergedContextDefinitions();

    foreach ($used_context as $name => $context_definition) {
      if (!empty($this->settings['context_values'][$name])) {
        $summary[] = $this->t('@label: @value', [
          '@label' => $context_definition->getLabel(),
          '@value' => $this->settings['context_values'][$name],
        ]);
      }
    }
    return $summary;
  }

  /**
   * Merges the context definitions of all given plugins.
   *
   * @see \Drupal\social_share\SocialShareLinkManagerInterface::getMergedContextDefinitions
   *
   * @return array[]
   */
  protected function getMergedContextDefinitions() {
    // Get allowed sharing links.
    // @todo: Improve when https://www.drupal.org/node/2329937 got committed.
    $field_item = $this->getTypedDataManager()
      ->create($this->fieldDefinition->getItemDefinition());
    $plugin_ids = $field_item->getPossibleValues();

    return $this->getSocialShareLinkManager()
      ->getMergedContextDefinitions($plugin_ids);
  }

}
