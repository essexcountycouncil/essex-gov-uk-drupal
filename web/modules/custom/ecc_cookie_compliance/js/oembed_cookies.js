/**
 * @file
 * ecc_oembed_cookies.js
 *
 * Defines the output of the oembed html field based on the consent of marketing cookies.
 */
(function ($, Drupal) {

  Drupal.behaviors.cookiesVideoDisplay = {
    attach: function(context, settings) {
        if (Drupal.eu_cookie_compliance.getCookieStatus() === 'granted' && $.inArray("marketing_cookies", Drupal.eu_cookie_compliance.getAcceptedCategories()) !== -1) {
          $.each(settings.ecc_cookie_oembed, function(key, val) {
            $('.media-oembed-'+key).html(settings.ecc_cookie_oembed[key].video)
          })
        }

    }
  }

}) (jQuery, Drupal)
