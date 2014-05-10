<?php
/**
 * @file
 * Contains \Drupal\social_share\Form\SocialShareNetworks.
 */
namespace Drupal\social_share\Form;

use Drupal\Core\Form\ConfigFormBase;

/**
 * Form callback: Social network tabledrag form.
 */
class SocialShareNetworks extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'social_share_networks';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $networks = social_share_available_networks();
    $form['networks_table'] = array(
      '#type'   => 'table',
      '#header' => array(t('Network'), t('Share URL'), t('Operations'), t('Weight')),
      '#empty' => t('Here are no networks yet'),
      '#tabledrag' => array(
        array(
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'networks-table-order-weight'),
      ),
    );

    // Build Table Row.
    foreach ($networks as $network) {
      $form['networks_table'][$network['machine_name']]['#attributes']['class'][] = 'draggable';
      $form['networks_table'][$network['machine_name']]['network_name'] = array(
        '#markup' => $network['human_name'],
      );
      $form['networks_table'][$network['machine_name']]['url'] = array(
        '#markup' => $network['url'],
      );
      $form['networks_table'][$network['machine_name']]['operations'] = array(
        '#type'  => 'operations',
        '#links' => array(),
      );
      $form['networks_table'][$network['machine_name']]['operations']['#links']['edit'] = array(
        'title' => t('Edit'),
        'href'  => "admin/config/content/social-share/networks/{$network['machine_name']}",
      );
      if (!$network['locked']) {
        $form['networks_table'][$network['machine_name']]['operations']['#links']['delete'] = array(
          'title' => t('Delete'),
          'href'  => "admin/config/content/social-share/networks/{$network['machine_name']}/delete",
        );
      }
      $form['networks_table'][$network['machine_name']]['weight'] = array(
        '#type'  => 'weight',
        '#title' => t('Weight for @title', array('@title' => $network['human_name'])),
        '#title_display' => 'invisible',
        '#default_value' => $network['weight'],
        '#attributes' => array('class' => array('networks-table-order-weight')),
      );
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    foreach ($form_state['values']['networks_table'] as $machine_name => $weight) {
      db_update('social_share_networks')
        ->condition('machine_name', $machine_name)
        ->fields($weight)
        ->execute();
    }
    parent::submitForm($form, $form_state);
  }
}
