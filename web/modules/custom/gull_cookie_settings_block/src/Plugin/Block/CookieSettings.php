<?php

declare(strict_types = 1);

namespace Drupal\gull_cookie_settings_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Markup;

/**
 * Presents the EU cookie compliance form as a block.
 *
 * @Block(
 *   id = "cookie_settings_block",
 *   admin_label = @Translation("EU Cookie settings block")
 * )
 */
class CookieSettings extends BlockBase {

  /**
   * Wrapper for the EU Cookie compliance settings form.
   *
   * The EU Cookie compliance module presents the Cookie settings form in a
   * popup.  We provide the same form as a block so that it can be placed
   * anywhere.
   *
   * {@inheritdoc}
   */
  public function build(): array {

    $attachments = [];

    eu_cookie_compliance_page_attachments($attachments);

    // Adjust cookie domain.
    if (function_exists('gull_site_specific_page_attachments_alter')) {
      gull_site_specific_page_attachments_alter($attachments);
    }

    // Avoid using cached cookie data.
    $cookie_data = eu_cookie_compliance_build_data();

    $build = [
      '#markup' => Markup::create($cookie_data['variables']['popup_html_info']),
      '#cache' => $attachments['#cache'],
    ];

    // We provide two variations of the Cookie popup - one for the Cookie
    // settings page, another for every other page.
    $build['#cache']['contexts'][] = 'url.path';

    $build['#attached'] = $attachments['#attached'];
    $build['#attached']['library'][] = 'gull_cookie_settings_block/gull_cookie_settings_block';

    return $build;
  }

}
