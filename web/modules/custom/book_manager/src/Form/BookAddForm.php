<?php

namespace Drupal\book_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form class for the Book add/edit form.
 */
class BookAddForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'book_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Book Title'),
      '#required' => TRUE,
    ];
    $form['author'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Author'),
      '#required' => TRUE,
    ];
    $form['publication_year'] = [
      '#type' => 'number',
      '#title' => $this->t('Publication Year'),
      '#required' => TRUE,
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Add custom form validation as needed.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save form submission data to a book node entity.
    $values = $form_state->getValues();
    
    $node_storage = \Drupal::service('entity_type.manager')->getStorage('node');
    $node = $node_storage->create([
      'type' => 'book',
      'title' => $values['title'],
      'field_author' => $values['author'],
      'field_publication_year' => $values['publication_year'],
    ]);
    $node->save();
    $form_state->setRedirect('book_manager.list');
  }
}
