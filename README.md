# Rollbar.com error reporting inside Flow/Neos

Report errors and unhandled exceptions to [Rollbar.com](https://rollbar.com/) 
service in your [Flow/Neos](https://www.neos.io/) project.

It is especially useful on Production environment, where you don't want
to have any exceptions or errors unnoticed.


# Features
* Error and exception logging for CLI request
* Error and exception logging for web request (server-side)
* Error and exception logging for front-end (live site)
* Error and exception logging for front-end (Neos back-end, content module
  and all other modules, like Media, Workspaces etc)
* Enabled by default for Production only, can be enabled for Development
  too.
* Sending currently authenticated account identifier, if present.
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
```


### Server-side error reporting

Configure it in `rollbarSettings`. You'll need at least provide
the API access token app (the `post_server_item` from your Rollbar app)
to `rollbarSettings.access_token`. You can add here any setting option
described in rollbar-php doc: https://github.com/rollbar/rollbar-php.


### Frontend (javascript) error reporting

Configure it in `rollbarJsSettings`. At least `accessToken` needs to be
provided (the `post_client_item` from your Rollbar app). You can add here
any setting option described in rollbar JS doc: https://rollbar.com/docs/notifier/rollbar.js/.

#### Neos

Frontend integration in Neos works out-of-the box, both for the public
site and all Neos CMS admin areas (content module and other sub-modules,
i.e. Media, History etc).

To enable Rollbar only on Neos CMS admin panel, you could add the
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

**Note**: the implementation of front-end side of things is a bit tricky
at this moment. Aspects are used to inject `rollbarConfig` into view 
of Neos Backend\ModuleController; Views are configured to override Neos'
`Default.html` layout (beware of that if you overridden it in your setup)
to render Rollbar config and snippet on all Neos back-end (sub)modules.
Apart of that a bit of .ts code (automatically included) to add it
to the page's HEAD section when in Neos content module or in live site.
Any ideas how to make it better more than welcomed.


## Authors

Author: Marcin Ryzycki (<marcin@m12.io>)


# License

MIT
