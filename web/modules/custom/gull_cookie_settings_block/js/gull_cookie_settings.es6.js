/**
 * @file
 * Javascript associated with the Cookie settings block.
 *
 * The Cookie settings block presents the Cookie settings form as a content
 * block rather than a popup.  This requires two additional behaviors:
 * - The event handlers from the popup has to be attached to the form inside
 *   the block.
 * - In this block, we use radio buttons instead of the usual checkboxes.  These
 *   checkboxes are still present in the form, but user interaction happens with
 *   the radio buttons.  This script ensures that radio button clicks are
 *   relayed to the checkbox and vice versa.
 */

/* eslint no-use-before-define: "off" */
(function setupCookieSettingsBlock($, Drupal) {
  /**
   * Bring the cookie settings form to life.
   *
   * Attach event handlers to the Cookie settings block's markup.  These event
   * handlers are meant for the EU Cookie compliance popup.  We are reusing the
   * markup of the popup.  So the event handlers work just fine.
   *
   * @see Drupal.eu_cookie_compliance.initPopup();
   */
  Drupal.behaviors.activateCookieSettingsForm = {
    attach(context) {
      // Load accepted cookie categories and update their checkboxes.
      const selectedCookieCategories = Drupal.eu_cookie_compliance.getAcceptedCategories();

      Drupal.eu_cookie_compliance.setAcceptedCategories(
        selectedCookieCategories
      );
      Drupal.eu_cookie_compliance.loadCategoryScripts(selectedCookieCategories);

      Drupal.eu_cookie_compliance.setPreferenceCheckboxes(
        selectedCookieCategories
      );
      Drupal.eu_cookie_compliance.attachSavePreferencesEvents();

      $("#eu-cookie-compliance-categories", context)
        .once()
        .each(displayCheckboxAsRadios);
    }
  };

  /**
   * Replace checkbox interaction with Radio buttons.
   */
  function displayCheckboxAsRadios() {
    // Wait for Drupal.eu_cookie_compliance.initPopup() to finish first.
    setTimeout(() => {
      hideCheckboxes();
      clickRadiosBasedOnCheckboxStatus();
      setupUpClickHandlersForRadios();
      setupSaveSettingsFeedback();
    }, 300);
  }

  /**
   * We don't want to display the Cookie status checkboxes.
   *
   * This is because the user interaction is with Radio buttons.
   */
  function hideCheckboxes() {
    $("[name=cookie-categories]:checkbox").hide();
  }

  /**
   * Radio status should reflect checkbox status.
   *
   * The On/Off radio buttons are related to **a** cookie status checkbox.
   * After page load, we click one of these radio buttons depending on the
   * checkbox status.
   */
  function clickRadiosBasedOnCheckboxStatus() {
    $(
      "[name=cookie-categories]:checked + .eu-cookie-compliance-categories--on :radio:not(:checked)"
    ).prop("checked", true);
    $(
      "[name=cookie-categories]:not(:checked) + .eu-cookie-compliance-categories--on + .eu-cookie-compliance-categories--off :radio:not(:checked)"
    ).prop("checked", true);
  }

  /**
   * Event handlers for radio buttons.
   *
   * Whenever a radio button is clicked, click its corresponding checkbox.  This
   * way the On/Off status of a cookie is reflected in the related checkbox.
   */
  function setupUpClickHandlersForRadios() {
    $(
      ".eu-cookie-compliance-categories--on :radio, .eu-cookie-compliance-categories--off :radio"
    ).click(event => {
      const key = event.target.value;
      const relatedCheckboxId = `cookie-category-${key}`;

      $(`#${relatedCheckboxId}`).click();
    });
  }

  /**
   * Post-save form feedback.
   *
   * Provide feedback when the "Save cookie settings" button is clicked.
   */
  function setupSaveSettingsFeedback() {
    $(".eu-cookie-compliance-save-preferences-button").click(e => {
      $(`<p>${Drupal.t("Saved")}</p>`)
        .appendTo(e.target)
        .hide(2000);
    });
  }
})(jQuery, Drupal);
