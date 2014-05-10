<?php
/**
 * @file
 * Contains \Drupal\social_share\Form\SocialShareConfigNetwork.
 */
namespace Drupal\social_share\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Component\Utility\String;

/**
 * Form callback for social network add/edit form.
 */
class SocialShareConfigNetwork extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'social_share_config_network';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, $machine_name = NULL) {
    if ($machine_name) {
      $network = social_share_get_network($machine_name);
      $form['new'] = array(
        '#type'  => 'hidden',
        '#value' => FALSE,
      );
    }
    else {
      $form['new'] = array(
        '#type'  => 'hidden',
        '#value' => TRUE,
      );
    }

    $items = array('#theme' => 'item_list', '#items' => array(
      '<em>%TITLE%</em>  --  ' . t('Title of page, node, etc'),
      '<em>%DESC%</em>  --  ' . t('Node body, description, etc (Note: most social networks ignore this)'),
      '<em>%URL%</em>  --  ' . t('URL being shared (Note: This is _REQUIRED_ for all social networks)'),
      '<em>%IMAGE%</em>  --  ' . t('URL of image to share (Note: Very few networks accept this, most parse the page content to find images. Pinterest is a notable exception.)'),
    ));
    $form += array(
      'human_name' => array(
        '#type'          => 'textfield',
        '#title'         => t('Name'),
        '#default_value' => isset($network['human_name']) ? $network['human_name'] : '',
        '#maxlength'     => 255,
        '#required'      => TRUE,
      ),
      'machine_name' => array(
        '#type'          => 'machine_name',
        '#default_value' => isset($network['machine_name']) ? $network['machine_name'] : '',
        '#disabled'      => !empty($machine_name) ? TRUE : FALSE,
        '#maxlength'     => 255,
        '#required'      => TRUE,
        '#title_display' => 'invisible',
        '#machine_name'  => array(
          'exists' => 'social_share_check_machine_name',
          'source' => array('human_name'),
          'label'  => '',
        ),
      ),
      'url' => array(
        '#type'          => 'textfield',
        '#title'         => t('URL'),
        '#default_value' => isset($network['url']) ? $network['url'] : '',
        '#size'          => 100,
        '#required'      => TRUE,
        '#description'   => String::format('<p>!first</p><p>!second</p><p>!place</p>', array(
          '!first'  => t('Provide the share url for this network.'),
          '!second' => t('These are the only valid placeholders:'),
          '!place'  => drupal_render($items),

        )),
      ),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $record = array(
      'human_name' => $form_state['values']['human_name'],
      'machine_name' => $form_state['values']['machine_name'],
      'url' => $form_state['values']['url']
    );
    $machine_name = !$form_state['values']['new'] ? array('machine_name') : array();
    drupal_write_record('social_share_networks', $record, $machine_name);
    $form_state['redirect'] = 'admin/config/content/social-share/networks';
    parent::submitForm($form, $form_state);
  }
}
