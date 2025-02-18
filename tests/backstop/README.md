# BackstopJS

**NB** `backstop-local.js` is required and is gitignored so that the tests can
be independent of hostnames.

```
// Sets variables outside of git which are used and reused in tests.
let localConfig = {};
localConfig.dev_domain = "https://domainoftest.com";
localConfig.prod_domain = "https://domainofreference.com";
module.exports = localConfig;
```

1. Add a local settings file `backstop-local.js` and enter reference and test domains.
1. run `ddev backstop reference --config=backstop.js`
1. run `ddev backstop test --config=backstop.js`
