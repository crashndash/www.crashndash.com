<?php

/**
 * @file
 * Contains \Drupal\crashndash\Form\SettingsForm.
 */

namespace Drupal\crashndash\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form that configures crashndash settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'crashndash_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('crashndash.settings');

    $form['urls'] = array(
      '#type' => 'fieldset',
      '#title' => 'URL settings',
      '#description' => t('Point the module to the settings for the different API calls.'),
    );
    $form['urls']['highscores_url'] = array(
      '#type' => 'textfield',
      '#title' => 'API URL of highscores list',
      '#description' => t('URL plz.'),
      '#default_value' => $config->get('highscores_url'),
    );
    $form['urls']['highscores_user'] = array(
      '#type' => 'textfield',
      '#title' => 'API user of highscores list',
      '#description' => t('URL plz.'),
      '#default_value' => $config->get('highscores_user'),
    );
    $form['urls']['highscores_pw'] = array(
      '#type' => 'textfield',
      '#title' => 'API password of highscores list',
      '#description' => t('URL plz.'),
      '#default_value' => $config->get('highscores_pw'),
    );
    $form['urls']['status_url'] = array(
      '#type' => 'textfield',
      '#title' => 'API URL of current status',
      '#description' => t('URL plz.'),
      '#default_value' => $config->get('status_url'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $test = $form_state->getValues();
    dpm($test);
    \Drupal::config('crashndash.settings')
      ->set('highscores_url', $test['highscores_url'])
      ->set('highscores_user', $test['highscores_user'])
      ->set('highscores_pw', $test['highscores_pw'])
      ->set('status_url', $test['status_url'])
      ->save();
  }

}
