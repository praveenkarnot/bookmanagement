<?php

namespace Drupal\book_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller class for Book management.
 */
class BookController extends ControllerBase
{

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager)
  {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Displays a table listing of books (nodes).
   */
  public function listBooks()
  {
    // Build the header for the table.
    $header = [
      ['data' => $this->t('Book Title')],
      ['data' => $this->t('Author')],
      ['data' => $this->t('Publication Year')],
      ['data' => $this->t('Operations')],
    ];

    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->accessCheck(TRUE);
    $query->condition('type', 'book');
    $nids = $query->execute();

    // Load entity type.
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

    // Build rows for the table.
    $rows = [];
    foreach ($nodes as $node) {
      $rows[] = [
        'data' => [
          'title' => $node->getTitle(),
          'author' => $node->get('field_author')->value,
          'published' => $node->get('field_publication_year')->value,
          [
            'data' => [
              '#type' => 'operations',
              '#links' => [
                'edit' => [
                  'title' => $this->t('Edit'),
                  'url' => Url::fromRoute(
                    'book_manager.edit',
                    array('node' => $node->id())
                  ),
                ],
                'delete' => [
                  'title' => $this->t('Delete'),
                  'url' => Url::fromRoute(
                    'book_manager.delete',
                    array('node' => $node->id())
                  ),
                ],
              ],
            ],
          ],
        ],
      ];
    }

    // Build the table render array.
    $build = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No books found.'),
    ];

    return $build;
  }

  /**
   * Deletes a book node.
   */
  public function deleteBook(Request $request, string $node)
  {
    // Delete the specified book node.
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($node);
    $node->delete();
    // Redirect to the book listing page.
    return $this->redirect('book_manager.list');
  }
}
