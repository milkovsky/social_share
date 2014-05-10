<?php
/**
 * @file
 * Contains \Drupal\social_share\Form\SocialShareDeleteNetwork.
 */
namespace Drupal\social_share\Form;

use Drupal\Core\Form\ConfirmFormBase;

/**
 * Form callback for confirmation of removal of a custom social network entry.
 */
class SocialShareDeleteNetwork extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'social_share_delete_network';
  }

  /**
    * The ID of the item to delete.
    * @var string
    */
  protected $human_name;

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to remove "@name"', array('@name' => $this->human_name));;
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'social_share.networks',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, $machine_name = NULL) {
    $network = social_share_get_network($machine_name);
    $this->human_name   = $network['human_name'];
    $this->machine_name = $machine_name;

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    db_delete('social_share_networks')
      ->condition('machine_name', $this->machine_name)
      ->execute();

    drupal_set_message(t('"@name" has been removed.', array('@name' => $this->human_name)));
    $form_state['redirect'] = 'admin/config/content/social-share/networks';
  }
}
