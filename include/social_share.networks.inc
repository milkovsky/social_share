<?php

/**
 * @file social_share.networks.inc
 */

function social_share_admin_networks($form, &$form_state) {
  $networks = social_share_available_networks();

  $form['networks']['#tree'] = TRUE;

  foreach ($networks as $network) {
    $form['networks'][$network['network_id']] = array(
      'human_name' => array(
        '#markup' => $network['human_name'],
      ),
      'url' => array(
        '#markup' => $network['url'],
      ),
      'edit' => array(
        '#markup' => l('Edit', 'admin/config/content/social-share/networks/' . $network['network_id'] .'/edit'),
      ),
    );

    if (!$network['locked']) {
      $form['networks'][$network['network_id']]['remove'] = array(
        '#markup' => l('Remove', 'admin/config/content/social-share/networks/' . $network['network_id'] .'/remove'),
      );
    }

    $form['networks'][$network['network_id']]['weight'] = array(
      '#type'          => 'weight',
      '#title'         => t('Weight'),
      '#default_value' => $network['weight'],
      '#delta'         => 20,
      '#title_display' => 'invisible',
    );
  }

  $form['actions'] = array('#type' => 'actions');

  $form['actions']['submit'] = array(
    '#type'   => 'submit',
    '#value'  => t('Save Changes'),
    '#submit' => array('social_share_admin_networks_submit'),
  );

  $form['actions']['add_network'] = array(
    '#markup' => l('Add Social Network', 'admin/config/content/social-share/networks/new',
      array(
        'attributes' => array(
          'class' => array(
            'button'
          )
        )
      )
    ),
  );


  return $form;
}

function social_share_admin_networks_submit($form, &$form_state) {
  $values = $form_state['values']['networks'];
  foreach($values as $network_id => $weight) {
    db_update('social_share_networks')
      ->condition('network_id', $network_id, '=')
      ->fields($weight)
      ->execute();
  }
  drupal_set_message('Your changes have been saved.');
}

function social_share_check_machine_name($name) {
  return FALSE;
}

function social_share_admin_networks_network($form, &$form_state, $network_id = NULL) {
  if ($network_id) {
    $network = social_share_get_network_by_id($network_id);
    $form['network_id'] = array(
      '#type'  => 'hidden',
      '#value' => $network_id,
    );
  }
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
      '#disabled'      => (isset($network['locked']) && $network['locked']) ? TRUE : FALSE,
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
      '#description'   => t('Provide the share url for this network.') .
                          '<br />' .
                          t('These are the only valid placeholders: !placeholders', array(
                            '!placeholders' =>
                            theme('item_list', array('items' => array(
                              '<em>%TITLE%</em>  --  ' . t('Title of page, node, etc'),
                              '<em>%DESC%</em>  --  ' . t('Node body, description, etc (Note: most social networks ignore this)'),
                              '<em>%URL%</em>  --  ' . t('URL being shared (Note: This is _REQUIRED_ for all social networks)'),
                              '<em>%IMAGE%</em>  --  ' . t('URL of image to share (Note: Very few networks accept this, most parse the page content to find images. Pinterest is a notable exception.)'),
                            )))
                          )),
    ),
  );

  $form['actions'] = array('#type' => 'actions');

  $form['actions']['submit'] = array(
    '#type'   => 'submit',
    '#value'  => t('Save Changes'),
    '#submit' => array('social_share_admin_networks_network_submit'),
  );

  return $form;
}

function social_share_admin_networks_network_submit($form, &$form_state) {
  $values = $form_state['values'];

  if (isset($values['network_id'])) {
    db_update('social_share_networks')
      ->condition('network_id', $values['network_id'], '=')
      ->fields(array(
        'human_name' => $values['human_name'],
        'machine_name' => $values['machine_name'],
        'url' => $values['url']
        ))
      ->execute();

    drupal_set_message('Your changes have been saved.');
  }
  else {
    db_insert('social_share_networks')
      ->fields(array(
        'human_name' => $values['human_name'],
        'machine_name' => $values['machine_name'],
        'url' => $values['url']
        ))
      ->execute();
    drupal_set_message('Your new network has been added.');
  }
  drupal_goto('admin/config/content/social-share/networks');
}
