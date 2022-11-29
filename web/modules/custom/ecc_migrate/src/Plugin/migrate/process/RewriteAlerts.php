<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Provides a rewrite_alerts plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "rewrite_alerts",
 *   handle_multiples=true,
 * )
 */
class RewriteAlerts extends ProcessPluginBase {

  /**
   * Markdown converter.
   *
   * Alert bodies are markdown. We need to convert them to HTML before
   * inserting into body text.
   */
  protected MarkdownConverter $markdownConverter;

  /**
   * Constructs a RewriteAlerts plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $environment = new Environment([]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $this->markdownConverter = new MarkdownConverter($environment);
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Look through $value for instances of the form
    // "{{Alerts-Inline:<Alert Title>}}".
    if (!str_contains($value[0], '{{Alerts-Inline:')) {
      // No alerts are in the source, so return unchanged.
      return $value[0];
    }
    $html = $value[0];
    $inline_alerts = explode('{{Alerts-Inline:', $value[0]);
    foreach ($inline_alerts as $inline_alert) {
      $alert_markdown = '';
      $alert_html = '';
      $alert_title = substr($inline_alert, 0, strpos($inline_alert, '}}'));
      if (!$alert_title) {
        // This case is probably the first element exploded from our body text.
        // This is everything prior to the first inline alert so should be
        // ignored.
        continue;
      }
      if (!array_key_exists($alert_title, $value[1])) {
        continue;
      }
      $alert_source = $value[1][$alert_title];
      // We only want to act on alerts with specific tags.
      if (!isset($alert_source['metadata']['tags'][0])) {
        continue;
      }
      if (!in_array($alert_source['metadata']['tags'][0]['sys']['id'], [
        'inlineAlert',
        'quote',
      ])) {
        continue;
      }
      $alert_markdown = $alert_source['fields']['body']['en-GB'];
      $alert_html = $this->markdownConverter->convert($alert_markdown);
      if ($alert_source['metadata']['tags'][0]['sys']['id'] == 'inlineAlert') {
        $alert_html = "<div class='inset'><h3>{$alert_source['fields']['title']['en-GB']}</h3>$alert_html</div>";
      }
      if ($alert_source['metadata']['tags'][0]['sys']['id'] == 'quote') {
        $alert_html = "<blockquote>$alert_html</blockquote>";
      }
      $html = str_replace("{{Alerts-Inline:{$alert_source['fields']['title']['en-GB']}}}", $alert_html, $html);
    }
    return $html;
  }

}
