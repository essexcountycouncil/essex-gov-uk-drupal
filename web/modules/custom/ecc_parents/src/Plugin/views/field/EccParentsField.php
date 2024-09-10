<?php

namespace Drupal\ecc_parents\Plugin\views\field;

use Drupal\Core\Link;
use Drupal\ecc_parents\Parents;
use Drupal\node\NodeInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A views field handler to display parent page of an ECC (LGD) content type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("ecc_parents_views_field")
 */
class EccParentsField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected Parents $parents,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\ecc_parents\Parents $parents */
    $parents = $container->get('ecc_parents.parents');

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $parents
    );
  }

  /**
   * The current display.
   *
   * @var string
   *   The current display of the view.
   */
  protected $currentDisplay;

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->currentDisplay = $view->current_display;
  }

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $values->_entity;

    if ($node instanceof NodeInterface) {
      $parent = $this->parents->getParent($node);
      if ($parent) {
        return Link::createFromRoute(
          $parent->label(),
          'entity.node.canonical',
          ['node' => $parent->id()]
        )->toRenderable();
      }
    }

    return '';

  }

}
