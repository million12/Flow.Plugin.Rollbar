<?php
namespace M12\Rollbar\TypoScript;

use TYPO3\Flow\Annotations as Flow;

/**
 * Class RollbarJsSettings
 */
class RollbarJsSettingsImplementation extends \TYPO3\TypoScript\TypoScriptObjects\RawArrayImplementation
{
    /**
     * @Flow\Inject()
     * @var \M12\Rollbar\Rollbar
     */
    protected $rollbar;


    /**
     * Prepare Rollbar js config
     *
     * @return array|bool
     */
    public function evaluate()
    {
        if ($this->rollbar->isEnabledForFrontend() && $this->rollbar->isEnabledForEnv()) {
            return $this->rollbar->getRollbarJsSettings();
        }
        return false;
    }
}
