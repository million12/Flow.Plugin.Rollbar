# Rollbar.com error reporting inside Flow/Neos

This project connects adds ability to report unhandled errors and
exceptions to [Rollbar.com](https://rollbar.com/) service.

It's especially useful on Production environment, where you don't want
to have any exceptions or errors unnoticed.


# Features
* Error and exception logging to Rollbar for web request
* Error and exception logging to Rollbar for CLI request
* Enabled by default for Production only, can be enabled for Development
  too.
* Sending currently authenticated account identifier, if exist.


# Installation and configuration:

### Install it with composer:
```
composer require m12/flow-rollbar
```

### Configure Configuration/Settings.yaml:

You'll need at least provide the API access token from your Rollbar.com
app (the `post_server_item` from your Rollbar app settings).
You can also configure if Rollbar is enabled on Production
and Development environments.

Below is the default configuration:
```
M12:
  Rollbar:
    # Enables Rollbar reporting for Production context
    enableForProduction: true
    # Enables Rollbar reporting for Development context
    enableForDevelopment: false

    rollbarSettings:
      access_token: your POST_SERVER_ITEM access token here
```

Note: you can add here any setting option described in rollbar-php doc:
https://github.com/rollbar/rollbar-php

Note: the `root`, `environment` and `person_fn` options are automatically
configured.


## Authors

Author: Marcin Ryzycki (<marcin@m12.io>)


# License

MIT
