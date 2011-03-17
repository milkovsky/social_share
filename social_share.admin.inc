<?php

/**
 * @file
 * Configuration options for Simple Share social network links.
 */

/**
 * Menu callback; Settings administration.
 */
function social_share_admin_settings() {
  $form['social_share_node_types'] = array(
    '#type'          => 'checkboxes',
    '#title'         => t('Node types'),
    '#description'   => t('Choose which node types to display the share links on.'),
    '#default_value' => variable_get('social_share_node_types', array()),
    '#options'       => node_get_types('names'),
  );

  $form['social_share_teaser'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Display in teaser view'),
    '#description'   => t('If enabled, the share links will appear in node teasers. If disabled, they will only appear on the full node page.'),
    '#default_value' => variable_get('social_share_teaser', 1),
  );

  $form['social_share_weight'] = array(
    '#type'          => 'weight',
    '#delta'         => 10,
    '#description'   => t('Where you want the share links to appear in the node. Negative numbers are rendered earlier, positive numbers are rendered later.'),
    '#title'         => t('Weight'),
    '#default_value' => variable_get('social_share_weight', 0),
  );

  $form['social_share_sites'] = array(
    '#type'          => 'checkboxes',
    '#title'         => t('Enabled share links'),
    '#description'   => t('Choose which websites you would like to enable a share link for.'),
    '#default_value' => variable_get('social_share_sites', array()),
    '#options'       => array(
      'facebook'     => t('Facebook'),
      'twitter'      => t('Twitter'),
      'googlebuzz'   => t('Google Buzz'),
      'myspace'      => t('Myspace'),
      'msnlive'      => t('MSN Live'),
      'yahoo'        => t('Yahoo'),
      'linkedin'     => t('LinkedIn'),
      'orkut'        => t('Orkut')
    ),
  );

  $form['social_share_max_desc_length'] = array(
    '#type'          => 'textfield',
    '#maxlength'     => 3,
    '#size'          => 3,
    '#description'   => t('Define the maximum description length passed through the link. Anything over 100 is excessive.'),
    '#title'         => t('Maximum description length'),
    '#default_value' => variable_get('social_share_max_desc_length', 50),
  );

  return system_settings_form($form);
}
