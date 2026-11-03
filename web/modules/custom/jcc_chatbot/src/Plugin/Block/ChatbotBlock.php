<?php

namespace Drupal\jcc_chatbot\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "jcc_chatbot",
 *   admin_label = @Translation("JCC Chatbot"),
 *   category = @Translation("JCC Chatbot")
 * )
 */
class ChatbotBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route match for the current route.
   *
   * @var Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Entity Type Manager
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Empty block because markup is provided by a template.
    // We just want to control block access.
    $build['content'] = [
      '#markup' => '',
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    // We currently only want the chatbot available on content tagged with the
    // following taxonomy terms. This may expand in the future.
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof NodeInterface) {
      $vid = 'case_type';
      // Add additional terms to the array as necessary.
      $valid_case_types = [
        $this->getTidByName('Name change', $vid),
      ];

      if ($case_types = $node->field_case_types) {
        foreach ($case_types->referencedEntities() as $type) {
          if (in_array($type->id(), $valid_case_types)) {
            return AccessResult::allowed();
          }
        }
      }
    }

    return AccessResult::forbidden();
  }

  /**
   * Utility: find term by name and vid.
   *
   * @param null $name
   *  Term name
   * @param null $vid
   *  Term vid
   *
   * @return int
   *  Term id or 0 if none.
   */
  protected function getTidByName($name = NULL, $vid = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);

    return !empty($term) ? $term->id() : 0;
  }
}
