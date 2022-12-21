/**
 * @file
 * Insert Google analytics script into page head.
 *
 * This script is supposed to be invoked by the EU Cookie compliance module once
 * agreement for the Google analytics cookie has been obtained.
 *
 * @see gull_cookie_settings_block_page_attachments_alter().
 */

(function runDeferredScripts(jQuery, Drupal, drupalSettings) {

  var deferred_scripts = JSON.parse(drupalSettings.deferred_scripts || {});
  
  jQuery(deferred_scripts.google_analytics_tracking_file).appendTo('head');
  jQuery(deferred_scripts.google_analytics_tracking_script).appendTo('head');
})(jQuery, Drupal, drupalSettings)
