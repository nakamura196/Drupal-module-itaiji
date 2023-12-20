<?php

namespace Drupal\itaiji\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itaiji_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['itaiji.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('itaiji.settings');
  
    $form['conversion_rules'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Conversion Rules'),
      '#description' => $this->t('Enter the conversion rules, one per line, in the format "original1, original2 => converted". For example, "學 => 学".'),
      '#default_value' => $this->getConversionRulesAsString($config->get('conversion_rules')),
      '#rows' => 10,
    ];
  
    return parent::buildForm($form, $form_state);
  }

  /**
   * Converts the conversion rules array to a string representation.
   */
  protected function getConversionRulesAsString($rules) {
    if ($rules === null) {
      return '';
    }
  
    $lines = [];
    foreach ($rules as $original => $converted) {
      // 原文と変換後の文字列を '=>' で結合します。
      $lines[] = $original . ' => ' . $converted;
    }
    return implode("\n", $lines);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $rulesString = $form_state->getValue('conversion_rules');
    $rules = $this->parseConversionRules($rulesString);

    $this->config('itaiji.settings')
      ->set('conversion_rules', $rules)
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Parses the string representation of conversion rules into an array.
   */
  protected function parseConversionRules($rulesString) {
    $rules = [];
    $lines = explode("\n", $rulesString);
    foreach ($lines as $line) {
      if (strpos($line, '=>') !== FALSE) {
        list($original, $converted) = array_map('trim', explode('=>', $line));
        $rules[$original] = $converted;
      }
    }
    return $rules;
  }
}