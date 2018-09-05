<?php

namespace Crashndash\Context;

use Drupal\DrupalExtension\Context\DrupalContext;

class FeatureContext extends DrupalContext {

  /**
   * @When I mock the highscores
   */
  public function iMockTheHighscores()
  {
    $contents = file_get_contents(__DIR__ . '/../../files/highscores.json');
    \Drupal::state()->set('crashndash.highscores', $contents);
  }

}
