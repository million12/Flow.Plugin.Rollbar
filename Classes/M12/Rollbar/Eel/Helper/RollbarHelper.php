<?php
namespace M12\Rollbar\Eel\Helper;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Eel\ProtectedContextAwareInterface;

class RollbarHelper implements ProtectedContextAwareInterface
{
    /**
     * @Flow\Inject()
     * @var \M12\Rollbar\Rollbar
     */
    protected $rollbar;

    public function getRollbarConfig()
    {
        return $this->rollbar->getRollbarJsSettings();
    }

    /**
     * All methods are considered safe, i.e. can be executed from within Eel
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName) {
        return true;
    }

}
