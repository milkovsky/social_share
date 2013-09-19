<?php

/**
 * @file social_share.theme.inc
 */

/**
 * Theme function to render a container full of share links (for a node).
 */
function theme_social_share_links($variables) {
  extract($variables);
  if (!isset($nid) && !(isset($node))) {
    return array();
  }
  elseif (isset($nid) && !isset($node)) {
    $node = node_load($nid);
    if (!$node) {
      return array();
    }
  }

  $content = array();
  if (variable_get('social_share_enabled_' . $node->type, 0)) {
    $networks = variable_get('social_share_networks_' . $node->type, array());
    $theme_func = variable_get('social_share_theme_' . $node->type, 'social_share_link');

    foreach ($networks as $network) {
      $content[] = theme($theme_func, array('network' => $network, 'node' => $node));
    }
  }
  else {
    return array();
  }

  $content['#prefix'] = '<div class="social-share clearfix">';
  $content['#suffix'] = '</div>';

  if (variable_get('social_share_show_label_' . $node->type, 0)) {
    global $language;
    $label = variable_get('social_share_label', NULL);
    if (is_array($label)) {
      $label = (isset($label[$language->language])) ? $label[$language->language] : '';
    }
    if (strlen($label) > 0) {
      $content['#prefix'] .= '<span class="share-label">' . $label .': </span>';
    }
  }

  return $content;
}

/**
 * Theme function to render a single social share link (for a node)
 */
function theme_social_share_link($variables) {
  if (!isset($variables['network']) || !isset($variables['node'])) {
    return array();
  }

  extract($variables);
  $network = social_share_get_network($network);
  global $language;

  $data = array('node' => $node);
  $options = array('language' => $language, 'clear' => TRUE);

  $replacements = array(
    '%TITLE%' => token_replace(variable_get('social_share_title_' . $node->type, '[node:title]'), $data, $options),
    '%DESC%'  => token_replace(variable_get('social_share_description_' . $node->type, '[node:body]'), $data, $options),
    '%URL%'   => token_replace(variable_get('social_share_url_' . $node->type, '[node:url:absolute]'), $data, $options),
    '%IMAGE%' => token_replace(variable_get('social_share_image_' . $node->type, ''), $data, $options)
  );

  dpm($replacements);

  // if the shorten_urls module is installed & enabled, shorten the url being shared.
  if (module_exists('shorten')) {
    $replacements['%URL%'] = shorten_url($replacements['%URL%']);
  }

  // Trim description as necessary
  $max_desc_length = variable_get('social_share_max_desc_length', 50);
  if (strlen($replacements['%DESC%']) > $max_desc_length) {
    $replacements['%DESC%'] = substr($replacements['%DESC%'], 0, $max_desc_length) . '...';
  }

  // Trim title so it will fit in a tweet.
  if ($network['machine_name'] == 'twitter' && variable_get('social_share_twitter_truncate', 0)) {
    if ((strlen($replacements['%URL%']) + strlen($replacements['%TITLE%'])) > 140) {
      $length                  = 136 - strlen($replacements['%URL%']);
      $replacements['%TITLE%'] = substr($replacements['%TITLE%'], 0, $length) . '...';
    }
  }

  $link_options = array('attributes' => array('class' => 'social-share-'. str_replace('_', '-', $network['machine_name']), 'title' => $network['human_name']));
  if (variable_get('social_share_new_window', 0)) {
    $link_options['attributes']['target'] = '_blank';
  }

  $link = array(
    '#type'    => 'link',
    '#title'   => $network['human_name'],
    '#href'    => str_replace(array_keys($replacements), array_values($replacements), $network['url']),
    '#options' => $link_options,
    '#suffix'  => '&nbsp;',
  );

  // Return the link
  return $link;
}

/**
 * Convenience theme function, ensures small icon css is added and then
 * calls theme_social_share_link().
 */
function theme_social_share_small_icon($variables) {
  $css_path = drupal_get_path('module', 'social_share') .'/icons/social-share-icons-16.css';
  $css_queue = drupal_add_css();
  if (!isset($css_queue[$css_path])) {
    drupal_add_css($css_path);
  }
  return theme('social_share_link', $variables);
}

/**
 * Convenience theme function, ensures large icon css is added and then
 * calls theme_social_share_link().
 */
function theme_social_share_large_icon($variables) {
  $css_path = drupal_get_path('module', 'social_share') .'/icons/social-share-icons-32.css';
  $css_queue = drupal_add_css();
  if (!isset($css_queue[$css_path])) {
    drupal_add_css($css_path);
  }
  return theme('social_share_link', $variables);
}
