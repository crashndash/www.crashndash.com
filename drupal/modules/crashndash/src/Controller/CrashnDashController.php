<?php

/**
 * @file
 * Contains \Drupal\crashndash\Controller\DevelController.
 */

namespace Drupal\crashndash\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for devel module routes.
 */
class CrashnDashController extends ControllerBase {

  public function frontpage() {
    $config = $this->config('crashndash.settings');
    $room_data = json_decode($config->get('room_data', array()));
    $rooms = array();
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
    $vars = array(
      'rooms' => $rooms,
      'users' => $users,
      'room_updated' => format_date($config->get('room_updated'), 'custom', 'D j F Y H:i:s'),
      'updated_raw' => $config->get('room_updated')
    );
    return _theme('crashndash_frontpage', $vars);
  }

  public function high_scores() {
    $config = $this->config('crashndash.settings');
    $records = json_decode($config->get('highscores'));
    if (!$records) {
      return '<p>' .
        t('We have a problem with our leaderboard provider. Please check back later.') .
      '</p>';
    }
    $total_records = 0;
    foreach ($records as $record) {
      $total_records++;
    }

    global $pager_page_array;
    // Moai page to get is global page + 1.
    $total = 3;
    $total_records = count($records);
    // Populate global page variables.
    $pagesize = 100;
    pager_default_initialize($total_records, $pagesize);
    $headers = array(
      t('Rank'),
      t('Name'),
      t('Score'),
    );
    $rows = array();
    $added = 0;
    global $pager_page_array;
    $offset = $pager_page_array[0] * $pagesize;
    while ($added < $pagesize - 1) {
      if (empty($records[$offset + $added])) {
        break;
      }
      $row = $records[$offset + $added];
      $username = !empty($row->name) ? $row->name : t('Anonymous');
      $rows[] = array(
        $added + $offset + 1,
        $username,
        $row->score
      );
      $added++;
    }
    $variables = array(
      'parameters' => array(
        'rows' => $pagesize,
      ),
    );
    $output = '';
    $output .= _theme('table', array('header' => $headers, 'rows' => $rows, 'attributes' => array('class' => array('moai-table'))));

    $output .= _theme('pager', $variables);
    $lasttime = $config->get('scores_updated');
    if (time() - $lasttime > 3600) {
      // Updated more than an hour ago. Problems!
      drupal_set_message(t('We are having some problems with getting fresh data for our leaderboard, but are working on the issue!'));
    }
    $output .= '<h6 class="subheader lastupdated" data-time="' . $lasttime . '">' . t('Last updated at %date', array('%date' => date('D j F Y H:i:s', $lasttime))) . '</div>';
    return $output;
    return 'high of the scores';
  }

  public function play() {
    $room = '';
    $referrer = '';
    if (!empty($_GET['room'])) {
      // ho ho. get a room.
      $room = $_GET['room'];
    }
    if (!empty($_GET['referrer'])) {
      $referrer = $_GET['referrer'];
    }
    $vars = array(
      'referrer' => $referrer,
      'room' => $room
    );
    return _theme('crashndash_play', $vars);
    return 'play of the seas';
  }

}
