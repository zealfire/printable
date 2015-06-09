<?php

/**
 * @file
 * Contains \Drupal\printable\Plugin\PrintableFormat\PrintFormat
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
 *   description = @Translation("Print description.")
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
  }

  /**
   * {@inheritdoc}
   */
  protected function buildContent() {
    $build = parent::buildContent();
    $config = $this->getConfiguration();
    if ($this->configFactory->get('printable.settings')->get('send_to_printer')) {
      //@todo afterwards this is just for testing
      $build['#attached']['js'][] = array(
        'type' => 'inline',
        'data' => '(function ($) {
  Drupal.behaviors.yourBehaviorName = {
    attach: function (context) {
      alert("ff");console.log("ad");
    }
  };
})(jQuery);',
      );
    }
    return $build;
  }
}
