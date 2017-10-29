<?php

namespace Drupal\crashndash\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Crash n dash controller.
 */
class CrashnDashController extends ControllerBase {

  /**
   * Frontpage.
   */
  public function frontpage() {
    $room_data = json_decode(\Drupal::state()->get('crashndash.room_data', []));
    $rooms = [];
    $users = 0;
    foreach ($room_data->users as $user) {
      if ($user->room) {
        if (empty($rooms[$user->room])) {
          $rooms[$user->room] = 0;
        }
        $rooms[$user->room] = ($rooms[$user->room] || 0) + 1;
      }
      $users++;
    }
    arsort($rooms);
    if (count($rooms) > 6) {
      $rooms = array_slice($rooms, 0, 6, TRUE);
    }
    $updated = \Drupal::state()->get('crashndash.room_updated');
    if ($updated) {
      $date = format_date($updated, 'custom', 'D j F Y H:i:s');
    }
    return [
      '#theme' => 'crashndash_frontpage',
      '#rooms' => $rooms,
      '#users' => $users,
      '#room_updated' => $date,
      '#updated_raw' => $updated,
      '#cache' => [
        'keys' => [
          CRASHNDASH_DATA_KEY,
          'frontpage',
        ],
      ],
    ];
  }

  /**
   * High scores page.
   */
  public function highScores() {
    $records = json_decode(\Drupal::state()->get('crashndash.highscores'));
    if (!$records) {
      return [
        '#markup' => '<p>' .
        $this->t('We have a problem with our leaderboard provider. Please check back later.') .
        '</p>',
      ];
    }
    $total_records = 0;
    foreach ($records as $record) {
      $total_records++;
    }

    $total = 3;
    $total_records = count($records);
    // Populate global page variables.
    $pagesize = 100;
    pager_default_initialize($total_records, $pagesize);
    $headers = [
      t('Rank'),
      t('Name'),
      t('Score'),
    ];
    $rows = [];
    $added = 0;
    global $pager_page_array;
    $offset = $pager_page_array[0] * $pagesize;
    while ($added < $pagesize - 1) {
      if (empty($records[$offset + $added])) {
        break;
      }
      $row = $records[$offset + $added];
      $username = !empty($row->name) ? $row->name : t('Anonymous');
      $rows[] = [
        $added + $offset + 1,
        $username,
        $row->score,
      ];
      $added++;
    }

    // $output .= _theme('pager', $variables);.
    $lasttime = \Drupal::state()->get('crashndash.scores_updated');
    if (time() - $lasttime > 3600) {
      // Updated more than an hour ago. Problems!
      drupal_set_message(t('We are having some problems with getting fresh data for our leaderboard, but are working on the issue!'));
    }
    return [
      'table' => [
        '#theme' => 'table',
        '#rows' => $rows,
        '#header' => $headers,
        '#attributes' => [
          'class' => [
            // Hm, wonder why I call it this.
            'moai-table',
          ],
        ],
      ],
      'pager' => [
        '#type' => 'pager',
      ],
      'updated' => [
        '#markup' => '<h6 class="subheader lastupdated" data-time="' . $lasttime . '">' . t('Last updated at %date', ['%date' => date('D j F Y H:i:s', $lasttime)]) . '</div>',
      ],
      '#cache' => [
        'keys' => [
          CRASHNDASH_DATA_KEY,
          'highscores',
        ],
      ],
    ];
  }

  /**
   * Play page.
   */
  public function play() {
    $room = '';
    $referrer = '';
    // @todo: Use request object.

    if (!empty($_GET['room'])) {
      // Ho ho. get a room.
      $room = $_GET['room'];
    }
    if (!empty($_GET['referrer'])) {
      $referrer = $_GET['referrer'];
    }
    $vars = [
      'referrer' => $referrer,
      'room' => $room,
    ];
    return [
      '#theme' => 'crashndash_play',
      '#referrer' => $referrer,
      '#room' => $room,
      '#cache' => [
        // @todo: Fix this properly. Vary based on GET param.
        'max-age' => 0,
      ],
    ];
  }

}
