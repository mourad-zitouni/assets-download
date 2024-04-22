<?php

namespace Drupal\download_form\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DownloadForm extends FormBase {

  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(
    MessengerInterface $messenger,
    LoggerChannelFactoryInterface $logger_factory,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->messenger = $messenger;
    $this->logger = $logger_factory->get('download_form');
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('logger.factory'),
      $container->get('entity_type.manager')
    );
  }

  public function getFormId() {
    return 'download_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $document_id = NULL) {
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $document_id,
    ];
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#required' => TRUE,
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Download'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $nid = $form_state->getValue('nid');
    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    
    if (empty($node)) {
      $this->messenger->addError($this->t('Invalid document ID.'));
    }

    if ($node) {
      $this->logger->notice('Asset downloaded by @email after form submission.', ['@email' =>$form_state->getValue('email')]);
      $form_state->setRedirectUrl(Url::fromUri('internal:/print/pdf/node/' . $nid));
    }
  }
}
