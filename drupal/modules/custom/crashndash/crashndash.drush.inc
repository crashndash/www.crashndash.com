<?php

/**
 * Implements hook_drush_command().
 */
function crashndash_drush_command() {
  $items['update-data'] = array(
    'description' => dt('Get fresh data for game site'),
  );
  return $items;
}

/**
 * Command callback for updating all data.
 */
function drush_crashndash_update_data() {
  _crashndash_update_room_data();
  _crashndash_update_high_scores();
}

/**
 * Drush callback for updating room data.
 */
function _crashndash_update_room_data() {
  // Find config for status page.
  $config = \Drupal::configFactory()->getEditable('crashndash.settings');
  $status_url = $config->get('status_url');
  /** @var \GuzzleHttp\ClientInterface $client */
  $client = \Drupal::service('http_client');
  /** @var \GuzzleHttp\Psr7\Response $response */
  $response = $client->get($status_url);
  $code = $response->getStatusCode();
  if ($code != 200) {
    // We have problems with receiving stuff. Log it and bail out.
    watchdog('crashndash', 'Problems retrieving room data. The server says: ' . print_r($body, TRUE));
    return;
  }
  $body = json_decode($response->getBody());
  // @todo. Set in cache. Abuse the config system for now.
  $config->set('room_data', json_encode($body));
  $config->set('room_updated', time());
  $config->save();
}

/**
 * Drush callback for updating high scores.
 */
function _crashndash_update_high_scores() {
  // Find config for status page.
  $config = \Drupal::configFactory()->getEditable('crashndash.settings');

  $status_url = $config->get('highscores_url');
  /** @var GuzzleHttp\Client $client */
  $client = \Drupal::service('http_client');
  $opts = array(
    'auth' => [
      $config->get('highscores_user'),
      $config->get('highscores_pw'),
    ],
  );
  $response = $client->get($status_url, $opts);
  $code = $response->getStatusCode();
  if ($code != 200) {
    // We have problems with receiving stuff. Log it and bail out.
    watchdog('crashndash', 'Problems retrieving high scores data. The server says: ' . print_r($response->getBody(), TRUE));
    return;
  }
  $body = json_decode($response->getBody());
  // @todo. Set in cache. Abuse the config system for now.
  $config->set('highscores', json_encode($body));
  $config->set('scores_updated', time());
  $config->save();
}
