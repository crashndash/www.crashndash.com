<?php

namespace Drupal\crashndash\Commands;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drush\Commands\DrushCommands;
use GuzzleHttp\Client;

/**
 * Command for crashndash.
 */
class CrashndashCommands extends DrushCommands {

  /**
   * Config.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $config;

  /**
   * Http client.
   *
   * @var \GuzzleHttp\Client
   */
  private $client;

  /**
   * State.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  private $state;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory, Client $client, StateInterface $state) {
    parent::__construct();
    $this->config = $config_factory;
    $this->client = $client;
    $this->state = $state;
  }

  /**
   * Get fresh data for game site.
   *
   * @command update:data
   * @aliases update-data
   */
  public function data() {
    $this->updateRoomData();
    $this->updateHighScores();
  }

  /**
   * Updates the high scpres.
   */
  private function updateHighScores() {
    // Find config for status page.
    $config = $this->config->get('crashndash.settings');

    $url = $config->get('highscores_url');
    /** @var \GuzzleHttp\Client $client */
    $opts = [
      'auth' => [
        $config->get('highscores_user'),
        $config->get('highscores_pw'),
      ],
    ];
    $response = $this->client->get($url, $opts);
    $code = $response->getStatusCode();
    if ($code != 200) {
      // We have problems with receiving stuff. Log it and bail out.
      \Drupal::logger('crashndash')
        ->error('Problems retrieving high scores data. The server says: ' . print_r($response->getBody(), TRUE));
      return;
    }
    $body = json_decode($response->getBody());
    $this->state->set('crashndash.highscores', json_encode($body));
    $this->state->set('crashndash.scores_updated', time());
    Cache::invalidateTags([
      CRASHNDASH_DATA_KEY,
    ]);
  }

  /**
   * Updates the room data.
   */
  private function updateRoomData() {
    // Find config for status page.
    $config = $this->config->get('crashndash.settings');
    $status_url = $config->get('status_url');
    /** @var \GuzzleHttp\Psr7\Response $response */
    $response = $this->client->get($status_url);
    $code = $response->getStatusCode();
    if ($code != 200) {
      // We have problems with receiving stuff. Log it and bail out.
      $this->logger->error('Problems retrieving room data. The server says: ' . print_r($response->getBody(), TRUE));
      return;
    }
    $body = json_decode($response->getBody());
    \Drupal::state()->set('crashndash.room_data', json_encode($body));
    \Drupal::state()->set('crashndash.room_updated', time());
    Cache::invalidateTags([
      CRASHNDASH_DATA_KEY,
    ]);
  }

}
