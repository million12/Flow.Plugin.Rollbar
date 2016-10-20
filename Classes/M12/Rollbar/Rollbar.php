<?php
namespace M12\Rollbar;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Party\Domain\Model\AbstractParty;

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
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Initialise Rollbar
     */
    public function init()
    {
        if ($this->settings['enableForProduction'] && $this->environment->getContext()->isProduction()
            || $this->settings['enableForDevelopment'] && $this->environment->getContext()->isDevelopment()
        ) {
            $rollbarSettings = $this->settings['rollbarSettings'];
            $rollbarSettings['root'] = rtrim(FLOW_PATH_ROOT, '/');
            $rollbarSettings['environment'] = strtolower($this->environment->getContext());
            $rollbarSettings['person_fn'] = [$this, 'getCurrentUser'];

            // Note: don't set_exception_handler() - Flow does it
            // Note: don't set_error_handler() - Flow does it
            \Rollbar::init($rollbarSettings, false, false);
        }
    }

    /**
     * Get info about currently logged in user
     *
     * @return array with id,
     */
    public function getCurrentUser()
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
