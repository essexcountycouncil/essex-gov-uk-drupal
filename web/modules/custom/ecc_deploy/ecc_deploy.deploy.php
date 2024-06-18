<?php

/**
 * @file
 * Deploy functions for the Essex .GOV deploy module.
 */

use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * Deploy hook to update menu item paths.
 */
function ecc_deploy_deploy_menu_paths_001(&$sandbox) {
  // The pattern below will be used in hook_update_N FNs in subsequent
  // deployments, when the need to update menu items are required.
  // If the routes don't yet exist prior to the deployment, such as the case
  // when a view's path is updated, this is the place to do the menu updates.
  $menu_updates = [
    [
      'menu_name' => 'subsites',
      'old_path' => '/children-young-people-and-families/fostering/events',
      'new_path' => '/essex-fostering/events',
    ],
    [
      'menu_name' => 'subsites',
      'old_path' => '/children-young-people-and-families/fostering/events/search',
      'new_path' => '/essex-fostering/events/search',
    ],
  ];

  foreach ($menu_updates as $update) {
    // Load the menu link by its path.
    $query = \Drupal::entityTypeManager()->getStorage('menu_link_content')->getQuery();
    $query->condition('menu_name', $update['menu_name']);
    $query->condition('link__uri', 'internal:' . $update['old_path']);
    $query->accessCheck(FALSE);
    $result = $query->execute();

    if (!empty($result)) {
      // Load the menu link entity.
      $menu_link_id = reset($result);
      $menu_link = MenuLinkContent::load($menu_link_id);

      if ($menu_link) {
        // Update the link path.
        $menu_link->set('link', ['uri' => 'internal:' . $update['new_path']]);
        $menu_link->save();

        \Drupal::messenger()->addMessage(t('The menu item path has been updated from %old_path to %new_path.', [
          '%old_path' => $update['old_path'],
          '%new_path' => $update['new_path'],
        ]));
      }
      else {
        \Drupal::messenger()->addWarning(t('Menu link entity could not be loaded for %old_path.', [
          '%old_path' => $update['old_path'],
        ]));
      }
    }
    else {
      \Drupal::messenger()->addWarning(t('No menu link found with the path %old_path.', [
        '%old_path' => $update['old_path'],
      ]));
    }
  }

}
