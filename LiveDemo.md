# DevOps Managed Services (GitHub Actions)

### Walk through reference implementation
* https://github.com/aguilita1/SampleSyncApp
* [GitHub-hosted runners](https://docs.github.com/en/actions/using-github-hosted-runners/using-github-hosted-runners/about-github-hosted-runners)
* [Workflow syntax for GitHub Actions](https://docs.github.com/en/actions/writing-workflows/workflow-syntax-for-github-actions)

### Walk through Continuous Integration pipeline
* https://github.com/aguilita1/SampleSyncApp/blob/main/.github/workflows/php.yml
* Note ``DOCKERHUB_TOKEN`` and ``DOCKERHUB_USERNAME`` are [Repository secrets](https://github.com/aguilita1/SampleSyncApp/settings/secrets/actions)

### GitHub MarketPlace & Actions Used
* https://github.com/marketplace?type=actions
* https://github.com/aguilita1/SampleSyncApp

# Continuous Integration (php.yml) Pipeline Live Demo 

### Review version strategy 
* https://hub.docker.com/repository/docker/luigui/samplesyncapp/general

### How to run continuous integration pipeline
* Run on ``main`` branch manually 
* Run on commit-push to 'main' branch
  * Observe [caching](https://github.com/aguilita1/SampleSyncApp/actions/caches) of dev dependencies
* Run on [``dependabot/composer/monolog/monolog-3.8.0``](https://github.com/aguilita1/SampleSyncApp/tree/dependabot/composer/monolog/monolog-3.8.0) branch manually
* Run on a tag
  * Notice creates tag and latest
    
# Dependabot 

* Eliminate technical debt with [Dependabot version updates](https://docs.github.com/en/code-security/dependabot/dependabot-version-updates/about-dependabot-version-updates)
* Review Dependabot configuration [dependabot.yml](https://github.com/aguilita1/SampleSyncApp/blob/main/.github/dependabot.yml)
  * [Configuration options](https://docs.github.com/en/code-security/dependabot/dependabot-version-updates/configuration-options-for-the-dependabot.yml-file)
    * Configure to use [private registries](https://docs.github.com/en/code-security/dependabot/dependabot-version-updates/configuration-options-for-the-dependabot.yml-file#registries) 
  * Configured to look for updates in package manager for composer, github-actions, and docker.
    * Available [package managers](https://docs.github.com/en/code-security/dependabot/dependabot-version-updates/configuration-options-for-the-dependabot.yml-file#package-ecosystem) that Dependabot can monitor
  * Configured to run daily
* Review Dependabot [pull requests](https://github.com/aguilita1/SampleSyncApp/pulls) 
  * Release notes
  * Commits
  * Compatability Score
  * Dependabot commands and options
  * Note ``DOCKERHUB_TOKEN`` and ``DOCKERHUB_USERNAME`` have to be defined for Dependabot in [Repository secrets](https://github.com/aguilita1/SampleSyncApp/settings/secrets/actions)
* Demonstrate ``@dependabot squash and merge``
* Demonstrate ``@dependabot recreate``
