<?php

/**
 * @file
 * Contains \Drupal\printable_pdf\Plugin\PrintableFormat\PdfFormat.
 */

namespace Drupal\printable_pdf\Plugin\PrintableFormat;

use Drupal\printable\Plugin\PrintableFormatBase;
use Drupal\printable\Annotation\PrintableFormat;
use Drupal\printable\PrintableCssIncludeInterface;
use Drupal\printable\LinkExtractor\LinkExtractorInterface;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\pdf_api\PdfGeneratorPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides a plugin to display a PDF version of a page.
 *
 * @PrintableFormat(
 *   id = "pdf",
 *   module = "printable_pdf",
 *   title = @Translation("PDF"),
 *   description = @Translation("PDF description.")
 * )
 */
class PdfFormat extends PrintableFormatBase {

  /**
   * The PDF generator plugin manager service.
   *
   * @var \Drupal\pdf_api\PdfGeneratorPluginManager
   */
  protected $pdfGeneratorManager;

  /**
   * The PDF generator plugin instance.
   *
   * @var \Drupal\pdf_api\Plugin\PdfGeneratorInterface
   */
  protected $pdfGenerator;

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory service.
   * @param \Drupal\pdf_api\PdfGeneratorPluginManager $pdf_generator_manager
   *   The PDF generator plugin manager service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ConfigFactory $config_factory, PdfGeneratorPluginManager $pdf_generator_manager, PrintableCssIncludeInterface $printable_css_include, LinkExtractorInterface $link_extractor) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $config_factory, $printable_css_include, $link_extractor);
    $this->pdfGeneratorManager = $pdf_generator_manager;
    $pdf_library = (string) $this->configFactory->get('printable.settings')->get('pdf_tool');
    $pdf_library = strtolower($pdf_library);
    $this->pdfGenerator = $this->pdfGeneratorManager->createInstance($pdf_library);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('config.factory'),
      $container->get('plugin.manager.pdf_generator'),
      $container->get('printable.css_include'),
      $container->get('printable.link_extractor')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'pdf_generator' => 'wkhtmltopdf',
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
    $options = array();
    foreach ($this->pdfGeneratorManager->getDefinitions() as $definition) {
      $options[$definition['id']] = $definition['title'];
    }
    $form['pdf_generator'] = array(
      '#type' => 'radios',
      '#title' => 'PDF Generator',
      '#default_value' => $config['pdf_generator'],
      '#options' => $options,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration(array(
      'pdf_generator' => $form_state['values']['pdf_generator'],
    ));
  }

  /**
   * Get  the header content.
   *
   * @return string
   *   Content of header.
   */
  public function getHeaderContent() {
    $pdf_header = array(
      '#theme' => 'printable_pdf_header',
    );
    return render($pdf_header);
  }

  /**
   * Get  the footer content.
   *
   * @return string
   *   Content of footer.
   */
  public function getFooterContent() {
    $pdf_footer = array(
      '#theme' => 'printable_pdf_footer',
    );
    return render($pdf_footer);
  }

  /**
   * Get the HTML content for PDF generation.
   *
   * @return string
   *   HTML content for PDF.
   */
  public function buildPdfContent() {
    $content = parent::buildContent();
    $rendered_page = parent::extractLinks(render($content));
    return $rendered_page;
  }

  /**
   * Set formatted header and footer.
   */
  public function formattedHeaderFooter() {
    // And this can be used by users who do not want default one, this example
    // is for wkhtmltopdf generator.
    $this->pdfGenerator->getObject()->SetFooter('This is a footer on left side||' . 'This is a footer on right side');
  }

  /**
   * {@inheritdoc}
   */
  public function getResponse() {
    $paper_size = (string) $this->configFactory->get('printable.settings')->get('paper_size');
    $paper_orientation = $this->configFactory->get('printable.settings')->get('page_orientation');
    $path_to_binary = $this->configFactory->get('printable.settings')->get('path_to_binary');
    $save_pdf = $this->configFactory->get('printable.settings')->get('save_pdf');
    $pdf_location = $this->configFactory->get('printable.settings')->get('pdf_location');
    $pdf_content = $this->buildPdfContent();
    $footer_content = $this->getFooterContent();
    $header_content = $this->getHeaderContent();
    // $this->formattedHeaderFooter();
    $this->pdfGenerator->setter($pdf_content, $pdf_location, $save_pdf, $paper_orientation, $paper_size, $footer_content, $header_content, $path_to_binary);
  }

}
