<?php
namespace M12\Rollbar\Error;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class DebugExceptionHandler extends \TYPO3\Flow\Error\DebugExceptionHandler
{
    public function handleException($exception)
    {
        $res = \Rollbar::report_exception($exception);
//        \TYPO3\Flow\var_dump('Rollbar report res: ' . $res);
        return parent::handleException($exception);
    }
}
