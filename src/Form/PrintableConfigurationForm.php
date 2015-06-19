<?php

/**
 * @file
 * Contains \Drupal\printable\Form\PrintableConfigurationForm.
 */

namespace Drupal\printable\Form;

use Drupal\printable\PrintableEntityManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides shared configuration form for all printable formats.
 */
class PrintableConfigurationForm extends ConfigFormBase {

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
    return 'printable_configuration';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['printable.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $printable_format = NULL) {

    // Allow users to choose what entities printable is enabled for.
    $form['settings']['printable_entities'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Printable Enabled Entities'),
      '#description' => $this->t('Select the entities that printable support should be enabled for.'),
      '#options' => array(),
      '#default_value' => array(),
    );

    // Build the options array.
    foreach ($this->printableEntityManager->getCompatibleEntities() as $entity_type => $entity_definition) {
      $form['settings']['printable_entities']['#options'][$entity_type] = $entity_definition->getLabel();
    }
    // Build the default values array.
    foreach ($this->printableEntityManager->getPrintableEntities() as $entity_type => $entity_definition) {
      $form['settings']['printable_entities']['#default_value'][] = $entity_type;
    }

    // Provide option to open printable page in a new tab/window.
    $form['settings']['open_target_blank'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Open in New Tab'),
      '#description' => $this->t('Open the printable version in a new tab/window.'),
      '#default_value' => $this->config('printable.settings')->get('open_target_blank'),
    );

    // Allow users to include CSS from the current theme.
    $form['settings']['css_include'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('CSS Include'),
      '#description' => $this->t('Specify an additional CSS file to include. Relative to the root of the Drupal install. The token <em>[theme:theme_machine_name]</em> is available.'),
      '#default_value' => $this->config('printable.settings')->get('css_include'),
    );

    // Provide option to turn off link extraction.
    $form['settings']['extract_links'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Extract Links'),
      '#description' => $this->t('Extract any links in the content, e.g. "Some Link (http://drupal.org)'),
      '#default_value' => $this->config('printable.settings')->get('extract_links'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::service('config.factory')->getEditable('printable.settings')
      ->set('printable_entities', $form_state->getValue('printable_entities'))
      ->set('open_target_blank', $form_state->getValue('open_target_blank'))
      ->set('css_include', $form_state->getValue('css_include'))
      ->set('extract_links', $form_state->getValue('extract_links'))
      ->save();
    // Invalidate the block cache to update custom block-based derivatives.
    // @todo try to make configsaveevent later.
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
    parent::submitForm($form, $form_state);
  }

}
