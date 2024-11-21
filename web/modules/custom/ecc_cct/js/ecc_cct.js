

(function ($, Drupal, drupalSettings, cookies) {

  Drupal.behaviors.eccCCT = {
    attach: function (context) {
      var count = 0;
      var postPreferencesLoadHandler = function (response) {
        // The event usually fires twice: once on initial click, then
        // preferences are taken into account and it fires again.
        // We can't reasonably capture dithering users who change choices
        // several times per page load.
        count++;
        if (count === 2) {
          if (response.currentStatus === 'granted') {
            ecc_cct_log(1);
          }
          else if (response.currentStatus === 'denied') {
            ecc_cct_log(2);
          }
        }
      };
      Drupal.eu_cookie_compliance('postStatusSave', postPreferencesLoadHandler);



      async function ecc_cct_log(choice) {
        const response = await fetch('/ecc-cct/js/log/' + choice);
      }
    }
  }
})(jQuery, Drupal, drupalSettings, window.Cookies);
