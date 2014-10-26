<?php

function crashndash_drush_command() {
  $items['update-data'] = array(
    'description' => dt('Get fresh data for game site'),
  );
  return $items;
}

function drush_crashndash_update_data() {
  _crashndash_update_room_data();
  _crashndash_update_high_scores();
}

function _crashndash_update_room_data() {
  // Find config for status page.
  $config = \Drupal::config('crashndash.settings');

  $status_url = $config->get('status_url');
  $client = \Drupal::service('http_client');
  $req = $client->createRequest('GET', $status_url);
  $response = $client->send($req);
  $code = $response->getStatusCode();
  if ($code != 200) {
    // We have problems with receiving stuff. Log it and bail out.
    watchdog('crashndash', 'Problems retrieving room data. The server says: ' . print_r($body, TRUE));
    return;
  }
  $body = $response->json();
  // @todo. Set in cache. Abuse the config system for now.
  $config->set('room_data', json_encode($body));
  $config->set('room_updated', time());
  $config->save();
}

function _crashndash_update_high_scores() {
  // Find config for status page.
  $config = \Drupal::config('crashndash.settings');

  $status_url = $config->get('highscores_url');
  $auth = base64_encode($config->get('highscores_user') . ':' . $config->get('highscores_pw'));
  $client = \Drupal::service('http_client');
  $opts = array(
    'headers' => array(
      'Authorization' => 'Basic bG9nZ2VyOkF0aWx1KVpPc3Z8PD9vVU80QDpmPU0vdXJiL0FMPXM5Tig1OyFr', 
    )
  );
  $req = $client->createRequest('GET', $status_url, $opts);
  $response = $client->send($req);
  $code = $response->getStatusCode();
  if ($code != 200) {
    // We have problems with receiving stuff. Log it and bail out.
    watchdog('crashndash', 'Problems retrieving room data. The server says: ' . print_r($body, TRUE));
    return;
  }
  $body = $response->json();//getBody(TRUE);
  // @todo. Set in cache. Abuse the config system for now.
  $config->set('highscores', json_encode($body));
  $config->set('scores_updated', time());
  $config->save();
}
