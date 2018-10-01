<?php
namespace M12\Rollbar;

use Neos\Flow\Annotations as Flow;

/**
 * Class Rollbar
 * @Flow\Scope("singleton")
 */
class Rollbar
{
    /**
     * @Flow\Inject
     * @var \Neos\Flow\Utility\Environment
     */
    protected $environment;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Security\Context
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
        return $this->settings['enableForProduction'] && $this->environment->getContext()->isProduction()
            || $this->settings['enableForDevelopment'] && $this->environment->getContext()->isDevelopment()
            || $allowInTestingEnv && $this->environment->getContext()->isTesting();
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
        $settings['person_fn'] = [$this, 'getPersonData'];

        $settings['environment'] = $this->getEnvironment();
        if ($this->getEnvironmentStr() !== $this->getEnvironment()) {
            // If includeSubContextInEnvironment=false and we have a sub-context in FLOW_CONTEXT, add it here.
            // Note: currently there's no possibility to send arbitrary payload via PHP
            // so include that information in _SERVER[argv], which is included in all reports.
            $_SERVER['argv']['FLOW_CONTEXT'] = (string)$this->environment->getContext();
        }

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
        if (($personData = $this->getPersonData())) {
            $jsSettings['payload']['person'] = $personData;
        }

        $jsSettings['payload']['environment'] = $this->getEnvironment();
        if ($this->getEnvironmentStr() !== $this->getEnvironment()) {
            // If includeSubContextInEnvironment=false and we have a sub-context in FLOW_CONTEXT, add it here.
            $jsSettings['payload']['FLOW_CONTEXT'] = (string)$this->environment->getContext();
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
        } catch (\Exception $exception) {
        };

        return isset($account) ? ['id' => $account->getAccountIdentifier()] : [];
    }

    /**
     * Get environment name from FLOW_CONTEXT (lowercase - that's what Rollbar expects)
     * based on `includeSubContextInEnvironment` option
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->settings['includeSubContextInEnvironment'] ? $this->getEnvironmentStr() : explode('/', $this->getEnvironmentStr())[0];
    }

    /**
     * @return string Full FLOW_CONTEXT, lower-cased
     */
    protected function getEnvironmentStr()
    {
        return strtolower($this->environment->getContext());
    }
}
