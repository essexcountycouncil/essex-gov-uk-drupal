/**
 * @file
 * ecc_live_handler.js
 *
 * Triggers GTM load and GA clear as needed on live preference changes
 */
(function ($, Drupal) {

    $(document).on('eu_cookie_compliance.changePreferences', function (event, categories) {
      if (categories.indexOf('analytics_cookies') >= 0) {
        window.gtm();
      };

      // Enforce cookie validity every time category approvals change
      // This way, any blocked cookies are removed as early as possible
      // when settings change, without waiting for the interval.
      Drupal.eu_cookie_compliance.BlockCookies();
    });

    $(document).ready(function() {
      // Also clear unapproved cookies early on page load, before the interval
      // is set up. This is useful in case a resident script has hooked into the
      // navigate away event, and sets a cookie during the transition.
      // While we can't stop that in a generic way, we can minimise the time it's
      // available for.
      Drupal.eu_cookie_compliance.BlockCookies();
    })

}) (jQuery, Drupal)

