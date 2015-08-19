<?php

/**
 * @file
 * Contains \Drupal\printable_mail\Form\FormatConfigurationFormMail
 */

namespace Drupal\printable_mail\Form;

use Drupal\printable\PrintableEntityManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides shared configuration form for all printable formats.
 */
class FormatConfigurationFormMail extends FormBase {

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
    return 'printable_configuration_mail';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $printable_format = NULL) {
    $form['settings'] = array(
    '#type' => 'fieldset',
    '#title' => $this->t('Send by email options'),
    );

    $form['settings']['print_mail_hourly_threshold'] = array(
      '#type' => 'select',
      '#title' => $this->t('Hourly threshold'),
      '#default_value' => 1,
      '#options' => (array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50)),
      '#description' => $this->t('The maximum number of emails a user can send per hour.'),
    );

    $form['settings']['print_mail_use_reply_to'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Use Reply-To header'),
      '#default_value' => 0,
      '#description' => $this->t("When enabled, any email sent will use the provided user and username in the 'Reply-To' header, with the site's email address used in the 'From' header (configured in site-information). Enabling this helps in preventing email being flagged as spam."),
    );

    $form['settings']['print_mail_teaser_default'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Send only the teaser'),
      '#default_value' => 0,
      '#description' => $this->t("If selected, the default choice will be to send only the node's teaser instead of the full content."),
    );

    $form['settings']['print_mail_user_recipients'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable user list recipients'),
      '#default_value' => 0,
      '#description' => $this->t("If selected, a user list will be included as possible email recipients."),
    );

    $form['settings']['print_mail_teaser_choice'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable teaser/full mode choice'),
      '#default_value' => 0,
      '#description' => $this->t('If checked, the user will be able to choose between sending the full content or only the teaser at send time.'),
    );

    $form['settings']['print_mail_send_option_default'] = array(
      '#type' => 'select',
      '#title' => $this->t('Default email sending format'),
      '#default_value' => 'sendpage',
      '#options' => array(
      'sendlink' => $this->t('Link'),
      'sendpage' => $this->t('Inline HTML'),
      ),
    );
  
    if (class_exists('Mail_mime')) {
      $form['settings']['print_mail_send_option_default']['#options']['inline-attachment'] = t('Inline HTML with Attachment');
      $form['settings']['print_mail_send_option_default']['#options']['plain-attachment'] = t('Plain Text with Attachment');
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
    \Drupal::service('config.factory')->getEditable('printable.settings')
      ->set('mail_tool', $form_state->getValue('print_mail_mail_tool'))
      ->set('save_mail', $form_state->getValue('print_mail_content_disposition'))
      ->set('paper_size', (string)$form_state->getValue('print_mail_paper_size'))
      ->set('page_orientation', $form_state->getValue('print_mail_page_orientation'))
      ->set('mail_location', $form_state->getValue('print_mail_filename'))
      ->save();
  }

}
