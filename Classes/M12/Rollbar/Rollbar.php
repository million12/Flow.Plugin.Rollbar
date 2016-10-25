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
        // Note: When Rollbar is NOT initialised, consequent calls to
        // \Rollbar::report_*() methods are safe to call, but won't do anything.
        // Note: use this->isEnabledForEnv(false) to exclude initialising in Testing env.
        if ($this->isEnabledForEnv(false)) {
            // Don't set_exception_handler() - Flow does it
            // Don't set_error_handler() - Flow does it
            \Rollbar::init($this->getRollbarSettings(), false, false);
        }
    }

    /**
     * Check if Rollbar should be enabled for current environment,
     * according to the settings
     *
     * @param bool $allowInTestingEnv
     * @return bool
     */
    public function isEnabledForEnv($allowInTestingEnv = true)
    {
        return $this->settings['enableForProduction']  && $this->environment->getContext()->isProduction()
            || $this->settings['enableForDevelopment'] && $this->environment->getContext()->isDevelopment()
            || $allowInTestingEnv && $this->environment->getContext()->isTesting()
        ;
    }

    /**
     * Check if Rollbar should be enabled on the front-end
     *
     * @return bool
     */
    public function isEnabledForFrontend()
    {
        return (bool)$this->settings['enableForFrontend'];
    }

    /**
     * Prepare Rollbar settings for server-side
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
     * Prepare Rollbar JS config (config to use on the front-end, in the browser)
     * filled with extra dynamic data (i.e. environment, currently logged in user)
     *
     * @return array
     */
    public function getRollbarJsSettings()
    {
        $jsSettings = $this->settings['rollbarJsSettings'];
        $jsSettings['payload']['environment'] = strtolower($this->environment->getContext());
        if (($personData = $this->getPersonData())) {
            $jsSettings['payload']['person'] = $personData;
        }

        return $jsSettings;
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
