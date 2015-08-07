<?php

/**
 * @file
 * Contains \Drupal\printable\Plugin\PrintableFormat\PrintFormat.
 */

namespace Drupal\printable\Plugin\PrintableFormat;

use Drupal\printable\Plugin\PrintableFormatBase;
use Drupal\printable\Annotation\PrintableFormat;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides a plugin to display a printable version of a page.
 *
 * @PrintableFormat(
 *   id = "print",
 *   module = "printable",
 *   title = @Translation("Print"),
 *   description = @Translation("Printable version of page.")
 * )
 */
class PrintFormat extends PrintableFormatBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'show_print_dialogue' => TRUE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies(){}

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    $form['show_print_dialogue'] = array(
      '#type' => 'checkbox',
      '#title' => 'Show print dialogue',
      '#default_value' => $config['show_print_dialogue'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state){}


  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration(array(
      'show_print_dialogue' => $form_state->getValue('show_print_dialogue'),
    ));
    $this->blockSubmit($form, $form_state);
  }

}
