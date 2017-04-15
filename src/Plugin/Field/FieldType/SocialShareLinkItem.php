<?php

namespace Drupal\social_share\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\social_share\SocialShareLinkManagerTrait;

/**
 * Plugin implementation of the 'social_share_link' field type.
 *
 * @todo: Make allowed options and their order configurable.
 *
 * @FieldType(
 *   id = "social_share_link",
 *   label = @Translation("Social share link"),
 *   description = @Translation("Allows selecting social share links."),
 *   category = @Translation("Other"),
 *   default_widget = "options_buttons",
 *   default_formatter = "list_default",
 * )
 */
class SocialShareLinkItem extends FieldItemBase implements OptionsProviderInterface {

  use SocialShareLinkManagerTrait;

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Text value'))
      ->addConstraint('Length', ['max' => 255])
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    return array_keys($this->getPossibleOptions($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return $this->getSettableOptions($account);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    return array_keys($this->getSettableOptions($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    return array_map(function ($definition) {
      return $definition['label'];
    }, $this->getSocialShareLinkManager()->getDefinitions());
  }

}
