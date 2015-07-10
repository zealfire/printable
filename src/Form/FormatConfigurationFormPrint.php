<?php

/**
 * @file
 * Contains \Drupal\printable\Form\FormatConfigurationFormPrint.
 */

namespace Drupal\printable\Form;

use Drupal\printable\PrintableEntityManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides shared configuration form for all printable formats.
 */
class FormatConfigurationFormPrint extends FormBase {

  /**
   * The printable entity manager.
   *
   * @var \Drupal\printable\PrintableEntityManagerInterface
   */
  protected $printableEntityManager;

  /**
   * Constructs a new form object.
   *
   * @param \Drupal\printable\PrintableEntityManagerInterface $printable_entity_manager
   *   The printable entity manager.
   */
  public function __construct(PrintableEntityManagerInterface $printable_entity_manager) {
    $this->printableEntityManager = $printable_entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('printable.entity_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'printable_configuration_print';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $printable_format = NULL) {
    $form['settings']['print_html_sendtoprinter'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Send to printer'),
      '#default_value' => $this->config('printable.settings')->get('send_to_printer'),
      '#description' => $this->t("Automatically calls the browser's print function when the printer-friendly version is displayed."),
    );

    $form['settings']['print_html_windowclose'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Close window after sending to printer'),
      '#default_value' => $this->config('printable.settings')->get('close_window'),
      '#description' => $this->t("When the above options are enabled, this option will close the window after its contents are printed."),
    );

    $form['settings']['print_html_display_sys_urllist'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Printer-friendly URLs list in system pages'),
      '#default_value' => $this->config('printable.settings')->get('list_attribute'),
      '#description' => $this->t('Enabling this option will display a list of printer-friendly destination URLs at the bottom of the page.'),
    );

    $form['settings']['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::service('config.factory')->getEditable('printable.settings')
      ->set('send_to_printer', $form_state->getValue('print_html_sendtoprinter'))
      ->set('close_window', $form_state->getValue('print_html_windowclose'))
      ->set('list_attribute', $form_state->getValue('print_html_display_sys_urllist'))
      ->save();
  }

}
