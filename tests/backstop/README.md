# BackstopJS

## Instructions for Set Up for First Time (new projects)
- Install backstopjs globally
-- `npm install -g backstopjs`
- Initialise backstop
-- `backstop init`
- Set up the tests
-- Edit backstop.json
- Run the references
-- `backstop reference`
- Run the tests
-- `backstop test`
- Write more tests

## Something not working?
Can't get it started: You probably need a more recent version of node
- `nvm install 8`

Some tests missing: BackstopJS starts and stops Google Chrome like mad. You might be missing some reference screenshots because of this. In Terminal kill any chrome processes `kill <process-id>`

## Writing Tests
There are only two items in the array that _must_ be present,
- label
- url
When using PatternLab, it seems we cannot test a URL with a parameter, such as: http://dttas.docksal/themes/custom/patternlab/pattern-lab/public/?p=sample-pages-basic-page
Instead, we need to provide the full path to the file we want to test, like this: "../../../web/themes/custom/patternlab/pattern-lab/public/patterns/05-page-sections-above-header-above-header/05-page-sections-above-header-above-header.html"

I have left all the options in the first test, just so we have them there.

For naming convention, I am going with
- `01.*__Rendered_Page__<page-name>` for rendered pages
-- e.g. `01.01__Rendered_Page__Basic Page`
- `02.* Region__<region-name>` for regions
-- e.g. `02.01__Region__Header`
- `03.* Building_Block__<building-block-name>` for Building Blocks
-- e.g. `03.01__Building_Block__Text_with_Image`
- `04.* Content Type__<content-type-name>` for Content Types
-- e.g. `04.01__Content_Type__News`

This means we are using two underscores (__) for divisions, and one underscore (_) for spaces. We do this so we can update/test just one reference at a time (saves us a lot of time if we add a new component and don't want to run the full `backstop reference`) by running `backstop reference --filter=<scenario-label>`

To test the header, we are using the http://PROJECT.docksal path. This is so the logo is tested, the URL to it is relative to the site, not the PatternLab file.

## Running Tests
To run all tests, simply run `backstop test`.

If you just want to test one component, you can run `backstop test --filter=<scenario-name`,
e.g. `backstop test --filter=04_08__Content_Type__Notice`

However, this will mean that the report page will now only show one test result.
