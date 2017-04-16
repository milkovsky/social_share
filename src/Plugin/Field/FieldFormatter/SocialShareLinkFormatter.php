<?php

namespace Drupal\social_share\Plugin\Field\FieldFormatter;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\TypedDataTrait;
use Drupal\rules\Context\ContextConfig;
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
  use TypedDataTrait;

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
        foreach ($share_link->getContextDefinitions() as $name => $definition) {
          $share_link->setContextValue($name, $this->settings['context_values'][$name]);
        }
        $elements[$delta] = $share_link->build();
      }
      catch (PluginException $e) {
        // Silently ignore possibly outdated data values of not existing share
        // links.
      }
    }

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

    // Get allowed sharing links.
    // @todo: Improve when https://www.drupal.org/node/2329937 got committed.
    $field_item = $this->getTypedDataManager()
      ->create($this->fieldDefinition->getItemDefinition());
    $plugin_ids = $field_item->getPossibleValues();

    // Collect all needed context definitions and remember which link needs
    // which context.
    $used_context = [];
    $used_by_plugins = [];
    $definitions = $this->getSocialShareLinkManager()->getDefinitions();

    foreach ($plugin_ids as $plugin_id) {
      // Just silently ignore outdated, gone plugins.
      if (!isset($definitions[$plugin_id])) {
        continue;
      }
      foreach ($definitions[$plugin_id]['context'] as $name => $context_definition) {
        $used_context[$name] = $context_definition;
        $used_by_plugins += [$name => []];
        $used_by_plugins[$name][] = $plugin_id;
      }
    }
    // @todo: Use context configuration traits for configuring this.
    $form['context_values']['#tree'] = TRUE;
    foreach ($used_context as $name => $context_definition) {
      $help = $this->t('Used by: %plugins', ['%plugins' => implode(', ', $used_by_plugins[$name])]);
      $form['context_values'][$name] = [
        '#type' => 'textfield',
        '#title' => $context_definition->getLabel(),
        '#description' => $context_definition->getDescription() . ' ' . $help,
        '#default_value' => isset($this->settings['context_values'][$name]) ? $this->settings['context_values'][$name] : $context_definition->getDefaultValue(),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return parent::settingsSummary();
  }

}
