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
   *
   * @param \Drupal\pdf_api\PdfGeneratorPluginManager $pdf_generator_manager
   *   The PDF generator plugin manager service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ConfigFactory $config_factory, PdfGeneratorPluginManager $pdf_generator_manager, PrintableCssIncludeInterface $printable_css_include, LinkExtractorInterface $link_extractor) {
    parent::__construct($configuration,$plugin_id, $plugin_definition, $config_factory, $printable_css_include,$link_extractor);
    $config = $this->getConfiguration();
    $this->pdfGeneratorManager = $pdf_generator_manager;
    $pdf_library = (string)$this->configFactory->get('printable.settings')->get('pdf_tool');
    switch ($pdf_library) {
      case "wkhtmltopdf":
        $this->pdfGenerator = $this->pdfGeneratorManager->createInstance($config['pdf_generator']);
        break;
      case "mPDF":
        $this->pdfGenerator = $this->pdfGeneratorManager->createInstance('mpdf');
        break;
      case "TCPDF":
        $this->pdfGenerator = $this->pdfGeneratorManager->createInstance('tcpdf');
        break;
    }
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
      'pdf_generator' => 'wkhtmltopdf'
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
   * Get  the header content
   *
   * @return string
   *  Content of header.
   */
  public function getHeaderContent() {
    $pdf_header = array(
      '#theme' => 'printable_pdf_header',
    );
    return render($pdf_header);
  }

  /**
   * Get  the footer content
   *
   * @return string
   *  Content of footer.
   */
  public function getFooterContent() {
    $pdf_footer = array(
      '#theme' => 'printable_pdf_footer',
    );
    return render($pdf_footer);
  }

  /**
   * {@inheritdoc}
   */
  public function buildContent() {
    $content = parent::buildContent();
    $rendered_page = parent::extractLinks(render($content));
    $this->pdfGenerator->addPage($rendered_page);
    $this->pdfGenerator->setHeader($this->getHeaderContent());
    // This will be used as default.
    $this->pdfGenerator->setFooter($this->getFooterContent());
    // And this can be used by user which do not default one.
    //$this->pdfGenerator->getObject()->setOptions(array('footer-left' => $this->getFooterContent()));
  }

  /**
   * {@inheritdoc}
   */
  public function mbuildContent($save_pdf, $filename) {
    $this->pdfGenerator->setHeader($this->getHeaderContent());
    $this->pdfGenerator->setFooter($this->getFooterContent());
    //$this->pdfGenerator->getObject()->SetFooter(render($pdf_footer));
    $content = parent::buildContent();
    $rendered_page = parent::extractLinks(render($content));
    if($save_pdf) {
      if(empty($filename)) {
        $filename = str_replace("/", "_", \Drupal::service('path.current')->getPath());
        $filename = substr($filename, 1);
      }
      $this->pdfGenerator->stream(utf8_encode(new Response($rendered_page)), $filename.'.pdf');
    }
    else
      $this->pdfGenerator->send(utf8_encode(new Response($rendered_page)));
  }

  /**
   * {@inheritdoc}
   */
  public function getResponse() {
    $pdf_library = (string)$this->configFactory->get('printable.settings')->get('pdf_tool');
    $paper_size = (string)$this->configFactory->get('printable.settings')->get('paper_size');
    $paper_orientation = $this->configFactory->get('printable.settings')->get('page_orientation');
    $save_pdf = $this->configFactory->get('printable.settings')->get('save_pdf');
    $pdf_location = $this->configFactory->get('printable.settings')->get('pdf_location');
    switch ($pdf_library) {
      case "wkhtmltopdf":
        $this->buildContent();
        $this->pdfGenerator->setPageSize($paper_size);
        $this->pdfGenerator->setPageOrientation($paper_orientation);
        if($save_pdf){
          $filename = $pdf_location;
          if(empty($filename)){
            $filename = str_replace("/", "_", \Drupal::service('path.current')->getPath());
            $filename = substr($filename, 1);
          }
          $this->pdfGenerator->stream("", $filename . '.pdf');
        }
        else
          $this->pdfGenerator->send();
        break;
      case "mPDF":
        $this->pdfGenerator->setPageSize($paper_size);
        $this->pdfGenerator->setPageOrientation($paper_orientation);
        $filename = $pdf_location;
        $this->mbuildContent($save_pdf, $filename);
        $this->buildContent();
        break;
      case "TCPDF":
        $this->pdfGenerator->setPageOrientation($paper_orientation);
        $this->buildContent();
        $this->pdfGenerator->setFooter("");
        if($save_pdf) {
          $filename = $pdf_location;
          if(empty($filename)) {
            $filename = str_replace("/", "_", \Drupal::service('path.current')->getPath());
            $filename = substr($filename, 1);
          }
          $this->pdfGenerator->stream("", $filename . '.pdf');
        }
        else
          $this->pdfGenerator->send("");
        break;
    }  
  }

}
