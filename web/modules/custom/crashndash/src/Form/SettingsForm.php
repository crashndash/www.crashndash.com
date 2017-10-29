<?php

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
  public function getFormId() {
    return 'crashndash_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('crashndash.settings');

    $form['urls'] = [
      '#type' => 'fieldset',
      '#title' => 'URL settings',
      '#description' => t('Point the module to the settings for the different API calls.'),
    ];
    $form['urls']['highscores_url'] = [
      '#type' => 'textfield',
      '#title' => 'API URL of highscores list',
      '#description' => t('URL plz.'),
      '#default_value' => $config->get('highscores_url'),
    ];
    $form['urls']['highscores_user'] = [
      '#type' => 'textfield',
      '#title' => 'API user of highscores list',
      '#description' => t('URL plz.'),
      '#default_value' => $config->get('highscores_user'),
    ];
    $form['urls']['highscores_pw'] = [
      '#type' => 'textfield',
      '#title' => 'API password of highscores list',
      '#description' => t('URL plz.'),
      '#default_value' => $config->get('highscores_pw'),
    ];
    $form['urls']['status_url'] = [
      '#type' => 'textfield',
      '#title' => 'API URL of current status',
      '#description' => t('URL plz.'),
      '#default_value' => $config->get('status_url'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configFactory->getEditable('crashndash.settings')
      ->set('highscores_url', $values['highscores_url'])
      ->set('highscores_user', $values['highscores_user'])
      ->set('highscores_pw', $values['highscores_pw'])
      ->set('status_url', $values['status_url'])
      ->save();
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['crashndash.settings'];
  }

}
