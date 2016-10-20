<?php
namespace M12\Rollbar\Error;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class ProductionExceptionHandler extends \TYPO3\Flow\Error\ProductionExceptionHandler
{
    public function handleException($exception)
    {
        $res = \Rollbar::report_exception($exception);
//        \TYPO3\Flow\var_dump('Rollbar report res: ' . $res);
        return parent::handleException($exception);
    }
}
