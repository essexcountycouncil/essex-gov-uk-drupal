<?php

namespace Drupal\ecc_parents;

use Drupal\node\NodeInterface;

/**
 * Helper functions for content types with parent/child relationship.
 */
class Parents implements ParentsInterface {

  /**
   * {@inheritdoc}
   */
  public function getParent(NodeInterface $node): ?NodeInterface {
    $parent_field = $this->getParentField($node);
    return $parent_field ? $node->get($parent_field)?->entity : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getChildren(NodeInterface $node): array {
    $children_field = $this->getChildrenField($node);
    return $children_field ? $node->get($children_field)->referencedEntities() : [];
  }

  /**
   * Get field that contains entity references to child pages.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node.
   *
   * @return string|null
   *   Field name.
   */
  protected function getChildrenField(NodeInterface $node): ?string {
    switch ($node->bundle()) {
      case 'localgov_guides_overview':
        $field = 'localgov_guides_pages';
        break;

      case 'localgov_services_landing':
        $field = 'localgov_destinations';
        break;

      case 'localgov_step_by_step_overview':
        $field = 'localgov_step_by_step_pages';
        break;
    }
    return isset($field) && $node->hasField($field) ? $field : NULL;
  }

  /**
   * Get field that contains entity reference to parent page.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node.
   *
   * @return string|null
   *   Field name.
   */
  protected function getParentField(NodeInterface $node): ?string {
    switch ($node->bundle()) {
      case 'localgov_directory':
      case 'localgov_services_landing':
      case 'localgov_services_page':
      case 'localgov_services_status':
      case 'localgov_services_sublanding':
      case 'localgov_step_by_step_overview':
      case 'localgov_subsites_overview':
      case 'localgov_subsites_page':
      case 'localgov_guides_overview':
        $field = 'localgov_services_parent';
        break;

      case 'localgov_guides_page':
        $field = 'localgov_guides_parent';
        break;

      case 'localgov_news_article':
        $field = 'localgov_newsroom';
        break;

      case 'localgov_step_by_step_page':
        $field = 'localgov_step_parent';
        break;
    }
    return isset($field) && $node->hasField($field) ? $field : NULL;
  }

}
