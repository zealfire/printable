<?php

/**
 * @file
 * Contains \Drupal\printable\Form\PdfLinksConfigurationForm.
 */

namespace Drupal\printable\Form;

use Drupal\printable\PrintableEntityManagerInterface;
use Drupal\printable\PrintableFormatPluginManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides shared configuration form for all printable formats.
 */
class PdfLinksConfigurationForm extends FormBase {

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
   * @param \Drupal\printable\PrintableFormatPluginManager $printable_format_manager
   *   The printable format plugin manager.
   */
  public function __construct(PrintableEntityManagerInterface $printable_entity_manager, PrintableFormatPluginManager $printable_format_manager) {
    $this->printableEntityManager = $printable_entity_manager;
    $this->printableFormatManager = $printable_format_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('printable.entity_manager'),
      $container->get('printable.format_plugin_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pdf_links_configuration';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $printable_format = NULL) {

    $form['settings']['print_pdf_link_pos'] = array(
      '#type' => 'checkboxes',
      '#title' => 'Link location',
      '#default_value' => array(),
      '#options' => array(
        'node' => $this->t('Links area'),
        'comment' => $this->t('Comment area'),
        'user' => $this->t('User area'),
      ),
      '#description' => $this->t('Choose the location of the link(s) to the printer-friendly version pages. The Links area is usually below the node content, whereas the Comment area is placed near the comments. The user area is near the user name. Select the options for which you want to disable the link. If you select any option then it means that you have enabled printable support for that entity in the configuration tab.'),
    );
    foreach ($this->config('printable.settings')->get('printable_pdf_link_locations') as $link_location) {
      $form['settings']['print_pdf_link_pos']['#default_value'][] = $link_location;
    }
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
    \Drupal::service('config.factory')->getEditable('printable.settings')->set('printable_pdf_link_locations', $form_state->getValue('print_pdf_link_pos'))->save();
  }

}
