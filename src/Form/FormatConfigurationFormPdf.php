<?php

/**
 * @file
 * Contains \Drupal\printable\Form\FormatConfigurationFormPdf.
 */

namespace Drupal\printable\Form;

use Drupal\printable\PrintableEntityManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Doctrine\Common\ClassLoader;
use Drupal\pdf_api\PdfGeneratorPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides shared configuration form for all printable formats.
 */
class FormatConfigurationFormPdf extends FormBase {

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
    return 'printable_configuration_pdf';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $printable_format = NULL) {
    $wkhtmltopdf_present = ClassLoader::classExists('mikehaertl\wkhtmlto\Pdf');
    $mpdf_present = ClassLoader::classExists('mPDF');
    $tcpdf_present = ClassLoader::classExists('TCPDF');
    $dompdf_present = ClassLoader::classExists('DOMPDF');
    $pdf_tool = $this->config('printable.settings')->get('pdf_tool');
    if ($wkhtmltopdf_present || $mpdf_present || $tcpdf_present || $dompdf_present) {
      $form['settings']['print_pdf_pdf_tool'] = array(
        '#type' => 'radios',
        '#title' => $this->t('PDF generation tool'),
        '#options' => array(),
        '#default_value' => $this->config('printable.settings')->get('pdf_tool'),
        '#description' => $this->t('This option selects the PDF generation tool being used by this module to create the PDF version.'),
      );
      if ($mpdf_present)
        $form['settings']['print_pdf_pdf_tool']['#options'] += array('mPDF' => 'mPDF');
      if ($tcpdf_present)
        $form['settings']['print_pdf_pdf_tool']['#options'] += array('TCPDF' => 'TCPDF');
      if ($wkhtmltopdf_present)
        $form['settings']['print_pdf_pdf_tool']['#options'] += array('wkhtmltopdf' => 'wkhtmltopdf');
      if ($dompdf_present)
        $form['settings']['print_pdf_pdf_tool']['#options'] += array('dompdf' => 'dompdf');
    }
    else { 
      drupal_set_message($this->t('You are seeing no PDF generating tool because you have not installed any third party library using composer.'));
    }
    $form['settings']['print_pdf_content_disposition'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Save the pdf'),
      '#description' => $this->t('Save the pdf instead of showing inline'),
      '#default_value' => $this->config('printable.settings')->get('save_pdf'),
    );
    $form['settings']['print_pdf_paper_size'] = array(
      '#type' => 'select',
      '#title' => $this->t('Paper size'),
      '#options' => array(),
      '#default_value' => (string)$this->config('printable.settings')->get('paper_size'),
      '#description' => $this->t('Choose the paper size of the generated PDF.'),
    );
    $paper_sizes = array(
      'A0',
      'A1',
      'A2',
      'A3',
      'A4',
      'A5',
      'A6',
      'A7',
      'A8',
      'A9',
      'B0',
      'B1',
      'B10',
      'B2',
      'B3',
      'B4',
      'B5',
      'B6',
      'B7',
      'B8',
      'B9',
      'C5E',
      'Comm10E',
      'DLE',
      'Executive',
      'Folio',
      'Ledger',
      'Legal',
      'Letter',
      'Tabloid',
    );
    foreach ($paper_sizes as $sizes ) {
      $form['settings']['print_pdf_paper_size']['#options'][$sizes] = $sizes;
    }
    $form['settings']['print_pdf_page_orientation'] = array(
      '#type' => 'select',
      '#title' => $this->t('Page orientation'),
      '#options' => array('portrait' => $this->t('Portrait'), 'landscape' => $this->t('Landscape')),
      '#default_value' => $this->config('printable.settings')->get('page_orientation'),
      '#description' => $this->t('Choose the page orientation of the generated PDF.'),
    );
    $form['settings']['print_pdf_filename'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('PDF filename'),
      '#default_value' => '',
      '#description' => $this->t("Filename with its location can be entered. If left empty and Save the pdf option has been selected the generated filename defaults to the node's path.The .pdf extension will be appended automatically."),
    );
    if ($wkhtmltopdf_present && $pdf_tool == 'wkhtmltopdf')
      $form['settings']['path_to_binary'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Path to binary file'),
        '#default_value' => $this->config('printable.settings')->get('path_to_binary'),
        '#description' => $this->t("Enter the path to binary file for wkhtmltopdf over here."),
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
    $pdf_tool = $this->config('printable.settings')->get('pdf_tool');
    \Drupal::service('config.factory')->getEditable('printable.settings')
      ->set('pdf_tool', $form_state->getValue('print_pdf_pdf_tool'))
      ->set('save_pdf', $form_state->getValue('print_pdf_content_disposition'))
      ->set('paper_size', (string)$form_state->getValue('print_pdf_paper_size'))
      ->set('page_orientation', $form_state->getValue('print_pdf_page_orientation'))
      ->set('pdf_location', $form_state->getValue('print_pdf_filename'))
      ->save();
    if (ClassLoader::classExists('mikehaertl\wkhtmlto\Pdf') && $pdf_tool == 'wkhtmltopdf') {
      \Drupal::service('config.factory')->getEditable('printable.settings')
      ->set('path_to_binary', $form_state->getValue('path_to_binary'))
      ->save();
    }
  }

}
