<?php
/**
 * @file
 * Contains \Drupal\social_share\Form\SocialShareSettings.
 */
namespace Drupal\social_share\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Component\Utility\String;
/**
 * Defines a form to configure maintenance settings for this site.
 */
class SocialShareSettings extends ConfigFormBase {

  /**
    * {@inheritdoc}
    */
  public function getFormID() {
    return 'social_share_settings';
  }

  /**
    * {@inheritdoc}
    */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('social_share.settings');
    $form['#attached'] = array(
      'library' => array('social_share/drupal.social_share.admin'),
    );

    // Node types block setting.
    $form['node_type'] = array(
      '#type'  => 'details',
      '#title' => t('Node types'),
      '#open'  => TRUE,
    );
    foreach (node_type_get_types() as $type) {
      $form['node_type'][$type->type] = array(
        '#type' => 'checkbox',
        '#title' => String::format('@type | !edit', array(
          '@type' => $type->name,
          '!edit' => l(t('edit'), "admin/structure/types/manage/{$type->type}", array(
            'query' => array('destination' => current_path()),
          )),
        )),
        '#default_value' => $config->get("type_{$type->type}"),
      );
    }

    // Visibility block setting.
    $form['visibility'] = array(
      '#type'  => 'details',
      '#title' => t('Visibility'),
      '#open'  => TRUE,
    );
    $form['visibility']['new_window'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Open Links in new window'),
      '#description'   => t('If enabled, the share links will open in a new window'),
      '#default_value' => $config->get('new_window'),
    );
    $form['visibility']['share_block'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Make links available as a block'),
      '#description'   => t('If enabled, the share links will be available in a block called "Social Share"'),
      '#default_value' => $config->get('share_block'),
    );

    // Other block setting.
    $form['other'] = array(
      '#type'  => 'details',
      '#title' => t('Other'),
      '#open'  => TRUE,
    );

    $lang_list = \Drupal::languageManager()->getLanguages();

    $form['other']['share_label'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Share label (by language)'),
      '#collapsible' => TRUE,
      '#collapsed'   => (count($lang_list) > 3) ? TRUE : FALSE,
      '#tree'        => TRUE,
    );

    foreach($lang_list as $lang_code => $lang) {
      $form['other']['share_label']["share_label_{$lang_code}"] = array(
        '#type'          => 'textfield',
        '#title'         => $lang->name,
        '#default_value' => $config->get("share_label_{$lang_code}"),
      );
    }

    $form['other']['twitter_truncate'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Truncate titles when sharing to twitter'),
      '#description'   => t('If enabled, node titles will be truncated if the URL being shared and the title exceed the twitter character limit of 140
         characters. <br /><strong>NOTE:</strong> Enabling this may cause issues with unicode text (Arabic, Kanji, etc)'),
      '#default_value' => $config->get('twitter_truncate'),
    );

    $form['other']['max_length'] = array(
      '#type'          => 'number',
      '#maxlength'     => 3,
      '#description'   => t('Define the maximum description length passed through the link. Anything over 100 is excessive.'),
      '#title'         => t('Maximum description length'),
      '#default_value' => $config->get('max_length'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
    * {@inheritdoc}
    */
  public function submitForm(array &$form, array &$form_state) {
    $this->configFactory->get('social_share.settings')
      ->set('new_window', $form_state['values']['new_window'])
      ->set('share_block', $form_state['values']['share_block'])
      ->set('twitter_truncate', $form_state['values']['twitter_truncate'])
      ->set('max_length', $form_state['values']['max_length'])
      ->save();

    $lang_list = \Drupal::languageManager()->getLanguages();
    foreach($lang_list as $lang_code => $lang) {
      $this->configFactory->get('social_share.settings')
        ->set("share_label_{$lang_code}", $form_state['values']["share_label_{$lang_code}"])
        ->save();
    }

    foreach (node_type_get_types() as $type) {
      $this->configFactory->get('social_share.settings')
        ->set("type_{$type->type}", $form_state['values'][$type->type])
        ->save();
    }
    parent::submitForm($form, $form_state);
  }
}
