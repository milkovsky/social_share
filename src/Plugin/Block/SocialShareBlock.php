<?php

namespace Drupal\social_share\Plugin\Block;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
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

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'allowed_plugins' => implode("\r\n", array_keys($this->getSocialShareLinkManager()->getDefinitions())),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['context_mapping']['entity']['#description'] = $this->t("An optional content entity which may be used as source for replacement tokens, accessible under the name 'entity'.");

    $form['allowed_plugins'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed plugins'),
      '#description' => $this->t('Allows restricting and ordering the allowed plugins. List one plugin ID per line.'),
      '#default_value' => $this->configuration['allowed_plugins'],
      '#required' => TRUE,
    ];

    $form['reload_button'] = [
      '#type' => 'button',
      '#value' => $this->t('Reload plugin configuration form'),
      '#limit_validation_errors' => [
        ['allowed_plugins'],
      ],
      '#ajax' => array(
        'callback' => [get_called_class(), 'reloadContextConfigurationForm'],
        'wrapper' => 'context-configuration-form',
        'progress' => array(
          'type' => 'throbber',
          'message' => "Reloading",
        ),
      ),
    ];

    $form['context_config']['#process'] = [[$this, 'updateContextConfigurationForm']];
    $form['context_config']['#type'] = 'container';
    $form['context_config']['#attributes'] = ['id' => 'context-configuration-form'];
    return $form;
  }

  /**
   * FAPI #process callback for updating the context configuration form.
   */
  public function updateContextConfigurationForm($form_element, FormStateInterface $form_state, &$form) {
    if ($values = $form_state->getValues()) {
      // This callback breaks out of the subform, so be sure to respect the
      // array parents.
      $parents = $form_element['#parents'];
      // Remove 'context_values' and append 'allowed_plugins' instead.
      array_pop($parents);
      $parents[] = 'allowed_plugins';
      $this->configuration['allowed_plugins'] = NestedArray::getValue($values, $parents);

      // Sometimes delimiters end up with \n instead of \r\n.
      $this->configuration['allowed_plugins'] = str_replace("\r\n", "\n", $this->configuration['allowed_plugins']);

      list($used_context, $used_by_plugins) = $this->getSocialShareLinkManager()
        ->getMergedContextDefinitions(explode("\n", $this->configuration['allowed_plugins']));
    }

    $form_element = $this->buildContextConfigurationForm($form_element, $form_state, $this->configuration, $used_context, $used_by_plugins);
    $form_element['context_values']['#type'] = 'fieldset';
    $form_element['context_values']['#title'] = $this->t('Social link plugin configuration');

    return $form_element;
  }

  /**
   * FAPI #ajax callback for updating the form.
   */
  public static function reloadContextConfigurationForm($form, FormStateInterface $form_state) {
    // Note that the ajax callback breaks out of the subform, so we have to
    // pre-prend the array-parents.
    $array_parents = $form_state->getTriggeringElement()['#array_parents'];
    // Remove the button from the parents and add 'context_values' instead.
    array_pop($array_parents);
    $array_parents[] = 'context_config';
    return NestedArray::getValue($form , $array_parents);
  }

  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $value = $form_state->getValue('context_config');
      $this->configuration['context_values'] = $value['context_values'];
      $this->configuration['allowed_plugins'] = $form_state->getValue('allowed_plugins');
    }
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

    return ;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

  }
}
