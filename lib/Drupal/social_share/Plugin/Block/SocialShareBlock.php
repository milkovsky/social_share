<?php

/**
 * @file
 * Contains \Drupal\social_share\Plugin\Block\SocialShareBlock.
 */

namespace Drupal\social_share\Plugin\Block;

use Drupal\block\BlockBase;

/**
 * Provides a 'Social Share' block.
 *
 * @Block(
 *   id = "social_share_block",
 *   admin_label = @Translation("Social Share"),
 *   category = @Translation("Header")
 * )
 */
class SocialShareBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $available_networks = social_share_available_networks();
    foreach ($available_networks as $network) {
      $networks_enabled["{$network['machine_name']}_enabled"] = 1;
    }
    return array(
      'theme' => 'large_icon',
      'share_label' => 1,
    ) + $networks_enabled;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array();
    if (\Drupal::config('social_share.settings')->get('share_block')) {
      $networks = social_share_available_networks();
      foreach ($networks as $network) {
        if ($this->configuration["{$network['machine_name']}_enabled"]) {
          $items[] = $network;
        }
      }
      $build = array(
        '#theme' => 'social_share_links',
        '#items' => !empty($items) ? $items : array(),
        '#node'  => \Drupal::request()->attributes->get('node'),
        '#block' => TRUE,
        '#label' => $this->configuration['share_label'],
      );
      $size = $this->configuration['theme'] == 'large_icon' ? 32 : 16;
      $build['#attached'] = array(
        'library' => array("social_share/drupal.social_share_{$size}"),
      );
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, &$form_state) {
    $config = \Drupal::config('social_share.settings');
    $form['display'] = array(
      '#type'        => 'details',
      '#title'       => t('Display Settings'),
      '#description' => t('Configure site-wide social share settings to use in this block.'),
      '#open'        => TRUE,
    );
    $form['display']['theme'] = array(
      '#type'          => 'select',
      '#title'         => t('Display Format'),
      '#description'   => t('Select the display format for social share links.<br /><strong>NOTE:</strong> If you want to provide your own icons, or prefer text links, select none and add css as needed to your theme.'),
      '#default_value' => $this->configuration['theme'],
      '#options'       => array(
        'small_icon' => t('Small Icons (16px)'),
        'large_icon' => t('Large Icons (32px)'),
      ),
    );
    $form['display']['share_label'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display share label'),
      '#description' => t('If checked, the share label !config will be displayed before any enabled social network share links when displaying nodes of this type.', array(
        '!config' => l('as configured here', 'admin/config/content/social-share'),
      )),
      '#default_value' => $this->configuration['share_label'],
    );
    $form['replacements'] = array(
      '#type'        => 'details',
      '#title'       => t('Replacements'),
      '#description' => t('Specify the values to use for placeholders in the social share provider urls'),
      '#open'        => TRUE,
    );
    $form['replacements']['share_title'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Title'),
      '#description'   => t('Select the value to use for the title when sharing from this block.'),
      '#default_value' => $config->get('share_title_block'),
      '#required'      => TRUE,
    );
    $form['replacements']['share_url'] = array(
      '#type'          => 'textfield',
      '#title'         => t('URL'),
      '#description'   => t('Select the value to use for the URL when sharing from this block.'),
      '#default_value' => $config->get('share_url_block'),
      '#required'      => TRUE,
    );
    $form['replacements']['share_description'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Description'),
      '#description'   => t('Select the value to use for the description when sharing from this block. <br><strong>Note:</strong> Most social networks ignore this value.'),
      '#default_value' => $config->get('share_description_block'),
    );
    $form['replacements']['share_image'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Image'),
      '#description'   => t('Select the value to use for the image when sharing from this block. <br><strong>Note:</strong> Very few social networks accept this value, most automatically grab images from the page markup. Facebook, for example, uses og:image metatag values for image options.'),
      '#default_value' => $config->get('share_image_block'),
    );
    $form['networks'] = array(
      '#type'        => 'details',
      '#title'       => t('Social Networks'),
      '#description' => t('Specify the social network(s) to enable in this block.'),
      '#open'        => TRUE,
    );

    $available_networks = social_share_available_networks();
    foreach ($available_networks as $network) {
      $form['networks'][$network['machine_name']] = array(
        '#type'  => 'checkbox',
        '#title' => $network['human_name'],
        '#default_value' => $this->configuration["{$network['machine_name']}_enabled"],
      );
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, &$form_state) {
    foreach ($form_state['values']['networks'] as $machine_name => $value) {
      $this->configuration["{$machine_name}_enabled"] = $value;
    }

    \Drupal::config('social_share.settings')
      ->set('share_title_block', $form_state['values']['replacements']['share_title'])
      ->set('share_url_block', $form_state['values']['replacements']['share_url'])
      ->set('share_description_block', $form_state['values']['replacements']['share_description'])
      ->set('share_image_block', $form_state['values']['replacements']['share_image'])
      ->save();

    $this->configuration['theme'] = $form_state['values']['display']['theme'];
    $this->configuration['share_label'] = $form_state['values']['display']['share_label'];
  }
}
