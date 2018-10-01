<?php
namespace M12\Rollbar\Error;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class DebugExceptionHandler extends \Neos\Flow\Error\DebugExceptionHandler
{
    /**
     * Handles the given exception
     *
     * @param object $exception The exception object - can be \Exception, or some type of \Throwable in PHP 7
     * @return void
     */
    public function handleException($exception)
    {
        $res = \Rollbar::report_exception($exception);
        parent::handleException($exception);
    }
}
