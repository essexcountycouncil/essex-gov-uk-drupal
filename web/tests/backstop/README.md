# Backstop Tests

Backstop is a frontend regression testing tool. We use it to check that any changes we make to the frontend are deliberate, and we do not cause regressions.

## Get the things you need
If this is your first time using this, run `npm install` to get the packages that you need. `Node 18` is set as the required version in the `.nvmrc` file, but any recent version of node should work.

## Get the reference files
Next, if you do not have reference files yet (screenshots of the current version of the site), you need to get these.

Run `npm run backstop:reference` to get these files. When you run your tests, backstop will check your version of the site against these file to make sure there are no regressions and anything that has changed was supposed to have changed.

## Run the tests
After you get the reference files, you can now run your tests. You do this by running `npm run backstop:test`. This will then take a screenshot of each page listed in the `backstop.json` file and compare those with the reference screenshots we created above.

The output of these tests is then read to a HTML file so you can view the report on the web.

## Check the results
We have placed the `tests` directory inside the `web` directory so the results of our tests are accessible on the web. You can see them at `<your-local-domain>/tests/backstop/backstop_data/html_report/index.html`, for example: `essex-gov.local/tests/backstop/backstop_data/html_report/index.html`.

## Documentation
The full documentation for backstop is [available on GitHub](https://github.com/garris/BackstopJS).
