<?php

namespace Drupal\ecc_parents;

use Drupal\node\NodeInterface;

/**
 * Interface for ecc_parents.parent service.
 */
interface ParentsInterface {

  /**
   * Get parent node if it exists.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node.
   *
   * @return \Drupal\node\NodeInterface|null
   *   Parent node.
   */
  public function getParent(NodeInterface $node): ?NodeInterface;

  /**
   * Get child nodes of a parent node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Parent node.
   *
   * @return array|\Drupal\node\NodeInterface[]
   *   Child nodes.
   */
  public function getChildren(NodeInterface $node): array;

}
