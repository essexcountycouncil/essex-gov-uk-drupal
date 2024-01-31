# Contributing

## Environments

### Production
https://essex.gov.uk

Only the _main_ branch can be deployed.

### Preprod
Currently used for multiple, conflicting purposes:
* Creating new content, prior to transferring it to Production
* Investigating new features, not yet in _develop_ branch
* UAT - testing by the client before deploying

To support this, any branch can be deployed.

New features may include new modules that are not in _develop_ branch.
These must be uninstalled manually before another branch is deployed to Preprod.

### Dev
Used for:
* QA by Nomensa's testers
* CI/CD - merges to _develop_ will trigger a deployment to Dev environment using
  the Gitlab pipeline.

## Branches
### _main_
This is the stable branch, containing the code that has been deployed to
Production. Its main purposes are to make it easier to:
* check what is on Production
* create hotfix branches

### _develop_
This is the branch for testing features and bugfixes before being merged to
_main_ and deployed to Production.

Pull requests should only be merged to _develop_ if they are ready to be tested
and deployed. If it is uncertain whether a new feature is required then it
should stay in a feature branch.

### Release branches
We will try to avoid release branches by frequent testing of _develop_, merging
to _main_ and deployment to Production.

### Feature and bugfix branches
New features and bugfixes should be branched off _develop_ and pull requests
created back to _develop_.

### Hotfix branches
Only hotfixes (e.g. for security updates) should be branched off _main_ so they
can be deployed to Production ahead of features that are still being
tested.

### Commits
Commit messages should be prefixed with a Jira ticket number so that the
purpose of the change can be traced.

Multiple commits in a pull request should be squashed unless the individual
commit messages are informative.
