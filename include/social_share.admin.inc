<?php

/**
 * @file
 * Configuration options for Simple Share social network links.
 */

/**
 * Menu callback; Settings administration.
 */
function social_share_admin_settings() {
  $types = node_type_get_types();
  $form['node_types'] = array(
    '#prefix' => '<label>' . t('Node types') . '</label>',
    '#suffix' => '<br />',
  );
  foreach ($types as $type) {
    $node_types[$type->type] = $type->name;
    $form['node_types']['social_share_enabled_'. $type->type] = array(
      '#type' => 'checkbox',
      '#title' => $type->name . '<small> [' . l('edit', 'admin/structure/types/manage/' . $type->type) . ']</small>',
      '#default_value' => variable_get('social_share_enabled_' . $type->type, 0),
    );
  }

  $form['social_share_new_window'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Open Links in new window'),
    '#description'   => t('If enabled, the share links will open in a new window'),
    '#default_value' => variable_get('social_share_new_window', 0),
  );

  $form['social_share_block'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Make links available as a block'),
    '#description'   => t('If enabled, the share links will be available in a block called "Social Share"'),
    '#default_value' => variable_get('social_share_block', 0),
  );

  $site_languages = language_list();
  $labels = variable_get('social_share_label', array('en' => t('Share to')));
  if (count($site_languages) > 1) {
    $form['social_share_label'] = array(
      '#type'          => 'fieldset',
      '#title'         => t('Share label (by language)'),
      '#collapsible'   => TRUE,
      '#collapsed'     => (count($site_languages) > 3) ? TRUE : FALSE,
      '#tree'          => TRUE,
    );
  }
  else {
    $form['social_share_label'] = array(
      '#type'          => 'markup',
      '#tree'          => TRUE,
    );
  }
  foreach($site_languages as $lang_code => $lang) {
    if (count($site_languages) > 1) {
      if ($lang->name != $lang->native) {
        $lang_title = $lang->name .' ('. $lang->native .')';
      }
      else {
        $lang_title = $lang->name;
      }
    }
    else {
      $lang_title = t('Share label');
    }
    $form['social_share_label'][$lang_code] = array(
      '#type'          => 'textfield',
      '#title'         => $lang_title,
      '#default_value' => (isset($labels[$lang_code])) ? $labels[$lang_code] : '',
    );
  }

  $form['social_share_sites'] = array(
    '#type'          => 'checkboxes',
    '#title'         => t('Enabled share links'),
    '#description'   => t('Choose which websites you would like to enable a share link for.'),
    '#default_value' => variable_get('social_share_sites', array()),
    '#options'       => array(
      'delicious'    => t('Delicious'),
      'digg'         => t('Digg'),
      'facebook'     => t('Facebook'),
      'googleplus'   => t('Google Plus'),
      'hyves'        => t('Hyves'),
      'linkedin'     => t('LinkedIn'),
      'msnlive'      => t('MSN Live'),
      'myspace'      => t('Myspace'),
      'orkut'        => t('Orkut'),
      'reddit'       => t('Reddit'),
      'stumbleupon'  => t('StumbleUpon'),
      'twitter'      => t('Twitter'),
      'yahoo'        => t('Yahoo'),
    ),
  );

  $form['social_share_twitter_truncate'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Truncate titles when sharing to twitter'),
    '#description'   => t('If enabled, node titles will be truncated if the URL being shared and the title exceed the twitter character limit of 140
       characters. <br /><strong>NOTE:</strong> Enabling this may cause issues with unicode text (Arabic, Kanji, etc)'),
    '#default_value' => variable_get('social_share_twitter_truncate', 0),
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
