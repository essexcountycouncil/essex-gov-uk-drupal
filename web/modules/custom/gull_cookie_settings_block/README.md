# Cookie settings block

## What is it?
- Extends the functionality of the [EU Cookie compliance contrib module](https://www.drupal.org/project/eu_cookie_compliance).
- Provides a block containing the same content as the EU Cookie popup.  When this block is added to a page, we get a dedicated Cookie settings page.  At the time of writing in early 2020, many sites including the [BBC](https://www.bbc.co.uk/usingthebbc/cookies/how-can-i-change-my-bbc-cookie-settings/) use such a dedicated Cookie settings page.
- Only relevant when you want users to have greater control over site cookies by enabling them to accept or reject certain types of cookies e.g. accept Functional cookies, reject Analytics cookies and so on.

## How to configure
- Create a content page (e.g. /foo) explaining your site Cookies.
- Turn on this module.
- Add the "EU Cookie settings block" provided by this module to the **Content** region.
- Restrict the above block to the cookie explanation page (i.e. /foo) mentioned earlier.
- Configure the EU Cookie compliance module:
  - Select **Opt-in with categories** as the consent method.
  - Add all your cookie categories to the **Cookie categories with separate consent** textbox.
  - Enter the cookie settings page path (e.g. /foo) to the **Cookie settings page path** textfield.  Default path is **/cookies**.  Without this path, this module would not provide the category-wise Cookie settings form.
  - Consider updating the following textfields:
    - "Save preferences" button label
    - "Accept all categories" button label
    - Cookie policy button label

## What to expect
- When you hit a random page of the site, you should see a EU cookie popup asking you to either accept all the cookies or check the cookie policy.
- If you click the cookie policy link, you should land in the Cookie settings page.
- The Cookie settings page should provide a Cookie settings form for accepting or rejecting cookie categories.
