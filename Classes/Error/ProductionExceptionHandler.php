<?php
namespace M12\Rollbar\Error;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class ProductionExceptionHandler extends \Neos\Flow\Error\ProductionExceptionHandler
{
    public function handleException($exception)
    {
        $res = \Rollbar::report_exception($exception);
        return parent::handleException($exception);
    }
}
