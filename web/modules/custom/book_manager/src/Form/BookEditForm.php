<?php

namespace Drupal\book_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;

/**
 * Form class for the Book add/edit form.
 */
class BookEditForm extends FormBase {

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function getFormId() {
    return 'my_entity_edit_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    // Load the entity to edit
    $node_obj = $this->entityTypeManager->getStorage('node')->load($node);
    
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Book Title'),
      '#required' => TRUE,
      '#default_value' => $node_obj->getTitle(),
    ];
    $form['author'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Author'),
      '#required' => TRUE,
      '#default_value' => $node_obj->get('field_author')->value,
    ];
    $form['publication_year'] = [
      '#type' => 'number',
      '#title' => $this->t('Publication Year'),
      '#required' => TRUE,
      '#default_value' => $node_obj->get('field_publication_year')->value,
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    // Store the entity ID for submission handler
    $form_state->set('node_id', $node);

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Load the entity to edit
    $nid = $form_state->get('node_id');
    $node = Node::load($nid);
    $node->set('title', $values['title']);
    $node->set('field_author', $values['author']);
    $node->set('field_publication_year', $values['publication_year']);
    $node->setChangedTime(REQUEST_TIME);
    $node->save();

    // Redirect or display a message
    $form_state->setRedirect('book_manager.list');
  }
}
