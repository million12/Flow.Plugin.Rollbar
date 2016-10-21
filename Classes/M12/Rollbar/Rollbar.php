<?php
namespace M12\Rollbar;

use TYPO3\Flow\Annotations as Flow;

/**
 * Class Rollbar
 * @Flow\Scope("singleton")
 */
class Rollbar
{
    /**
     * @Flow\Inject()
     * @var \TYPO3\Flow\Utility\Environment
     */
    protected $environment;

    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Security\Context
     */
    protected $securityContext;

    /**
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;


    /**
     * Initialise Rollbar
     */
    public function init()
    {
        // Only initialise it for configured environments.
        //
        // Note: When Rollbar is not initialised, consequent calls to
        // \Rollbar::report_*() methods are safe to call, but won't do anything.
        if ($this->isEnabledForEnv()) {
            // Don't set_exception_handler() - Flow does it
            // Don't set_error_handler() - Flow does it
            \Rollbar::init($this->getRollbarSettings(), false, false);
        }
    }

    /**
     * Check if Rollbar should be enabled for current environment,
     * according to the settings
     *
     * @return bool
     */
    public function isEnabledForEnv()
    {
        return $this->settings['enableForProduction']  && $this->environment->getContext()->isProduction()
            || $this->settings['enableForDevelopment'] && $this->environment->getContext()->isDevelopment();
    }

    /**
     * Prepare Rollbar settings
     *
     * @return array
     */
    public function getRollbarSettings()
    {
        $settings = $this->settings['rollbarSettings'];
        $settings['root'] = rtrim(FLOW_PATH_ROOT, '/');
        $settings['environment'] = strtolower($this->environment->getContext()); // Rollbar expects it lowercase
        $settings['person_fn'] = [$this, 'getPersonData'];

        return $settings;
    }

    /**
     * Get info about currently logged in user
     *
     * @return array with id,
     */
    public function getPersonData()
    {
        // Get currently authenticated account from security context.
        //
        // Note: wrap it in try/catch as in CLI mode security context
        // might be not initialised and will throw an exception.
        try {
            $account = $this->securityContext->getAccount();
        } catch (\Exception $e) {};

        return isset($account)
            ? [ 'id' => $account->getAccountIdentifier() ]
            : [];
    }
}
