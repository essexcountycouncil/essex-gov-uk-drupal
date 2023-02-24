/**
 * @file
 * ecc_live_handler.js
 *
 * Triggers GTM load and GA clear as needed on live preference changes
 */
(function ($) {
  
    $(document).on('eu_cookie_compliance.changePreferences', function (event, categories) {
      if (categories.indexOf('analytics_cookies') >= 0) {
        window.gtm();
      } else {
        $.each(document.cookie.split(/; */), function()  {
          var splitCookie = this.split('=');
          console.log(splitCookie);
          if (splitCookie[0].indexOf('_ga') >= 0) {
            document.cookie = splitCookie[0] + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
          }
        });
      }
      console.log(categories);
  })

}) (jQuery)

