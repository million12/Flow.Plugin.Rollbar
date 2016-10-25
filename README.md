# Rollbar.com error reporting inside Flow/Neos

Report errors and unhandled exceptions to [Rollbar.com](https://rollbar.com/) 
service in your [Flow/Neos](https://www.neos.io/) project.

It is especially useful on Production environment, where you don't want
to have any exceptions or errors unnoticed.


# Features
* Error and exception logging:
  * on server-side (PHP, both web requests and CLI requests)
  * on front-end (JS) for public site and Neos admin areas (content module
  and all other modules, like Media, Workspaces etc)
* Enabled by default for Production only, can be enabled for Development
  too.
* Sending environment and currently authenticated account identifier, if present.
* Tested with Flow 3.3, Neos 2.3 and PHP 7.0 (should work on PHP 5.6 up).


# Installation

Install it with composer:
```
composer require m12/flow-rollbar
```

# Configuration

Configure your settings in `Configuration/Settings.yaml`.
You can disable Rollbar error reporting for front-end JavaScript
or enable/disable it for prod/dev environement.

The following are the **defaults**:
```
M12:
  Rollbar:
    # Enables Rollbar reporting for Production context
    enableForProduction: true
    # Enables Rollbar reporting for Development context
    enableForDevelopment: false

    # Enables Rollbar on the front-end, in the browser
    # @see rollbarJsSettings below
    enableForFrontend: true

    # Server-side configuration
    #
    # You can add here any setting option described in rollbar-php
    # docs: https://github.com/rollbar/rollbar-php
    #
    # Note: the `root`, `environment` and `person_fn` options are automatically filled.
    rollbarSettings:
      access_token: your POST_SERVER_ITEM access token here
      batch_size: 10

    # Front-end side configuration
    #
    # You can add here any option available in
    # https://rollbar.com/docs/notifier/rollbar.js/ .
    #
    # Note: the `payload.environment` and `payload.person` options are automatically filled.
    rollbarJsSettings:
      accessToken: your POST_CLIENT_ITEM access token here
      captureUncaught: true
      captureUnhandledRejections: true
      payload: {}

    # Path to Rollbar html/js template
    templatePath: 'resource://M12.Rollbar/Private/Templates/Rollbar.html'

    # Assuming FLOW_CONTEXT contains sub-context, should it be sent in the `environment` key?
    # When set to false *and* sub-context *is* present, whole FLOW_CONTEXT will be sent as a separate metadata.
    includeSubContextInEnvironment: false
```


## Server-side error reporting

Configure it in `rollbarSettings`. You'll need at least provide
the API access token app (the `post_server_item` from your Rollbar app)
to `rollbarSettings.access_token`. You can add here any setting option
described in rollbar-php doc: https://github.com/rollbar/rollbar-php.


## Frontend (javascript) error reporting

Configure it in `rollbarJsSettings`. At least `accessToken` value needs to be
provided (the `post_client_item` from your Rollbar app). You can add here
any setting option described in rollbar JS doc: https://rollbar.com/docs/notifier/rollbar.js/.

### Neos

Frontend integration in Neos works out-of-the box, both for the public
site and all Neos CMS admin areas (content module and other sub-modules,
i.e. Media, History etc).

To enable Rollbar **only** on Neos CMS admin panel, you could add the
following line to your .ts code:
```
prototype(TYPO3.Neos:Page) {
	head {
		rollbar = TYPO3.TypoScript:Template {
			@if.onlyRenderWhenNotInLiveWorkspace = ${node.context.workspace.name != 'live'}
		}
	}
}
```

**Note**: the implementation of front-end side of things can be a bit tricky.
Views are configured to override Neos' `Default.html` layout to render
Rollbar config and JS snippet on all Neos back-end (sub)modules. Beware
of that if you have overridden the default Neos layout it in your setup.
Apart of that, we have a bit of .ts code (automatically included) to add
Rollbar snippet to the page HEAD section, both when in Neos content module
or in live site.


### Flow

Front-end integration requires adding a Rollbar snippet to your template
(or perhaps layout, so it's shared amongst all different views).

Add Rollbar snippet to your front-end layout code using
`<rollbar:renderRollbar />` tag. Remember to add `rollbar` namespace
at the very top of the file.

Rollbar recommends adding it at the very top of the HEAD section,
at least before any other .js code. Your code could look something like that:
```
{namespace rollbar=M12\Rollbar\ViewHelpers}
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<rollbar:renderRollbar />
	...
</head>
```


# Authors

Author: Marcin Ryzycki (<marcin@m12.io>)


# License

MIT
