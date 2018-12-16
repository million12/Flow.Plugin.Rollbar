<?php
namespace M12\Rollbar\Eel\Helper;

use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;

class RollbarHelper implements ProtectedContextAwareInterface
{
    /**
     * @Flow\Inject
     * @var \M12\Rollbar\Rollbar
     */
    protected $rollbar;

    /**
     * Get Rollbar JS config, filled with extra dynamic data
     * (i.e. environment, currently logged in user).
     * When Rollbar is disabled, it returns false.
     *
     * @return array|bool
     */
    public function getRollbarConfig()
    {
        if ($this->rollbar->isEnabledForFrontend() && $this->rollbar->isEnabledForEnv()) {
            return $this->rollbar->getRollbarJsSettings();
        }

        return false;
    }

    /**
     * All methods are considered safe, i.e. can be executed from within Eel
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
