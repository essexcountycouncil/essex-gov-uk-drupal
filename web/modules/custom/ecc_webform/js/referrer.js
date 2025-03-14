Drupal.behaviors.webformReferrer = {
  attach: function (context, settings) {
    // Using once() to apply the effect when you want to run just one function.
    once('referrer-processed', '[data-drupal-selector=edit-referrer]', context).forEach(function (element) {
      element.classList.add('processed');
      element.value = document.referrer;
    });
  }
};
