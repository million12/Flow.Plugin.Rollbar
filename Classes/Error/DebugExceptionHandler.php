<?php
namespace M12\Rollbar\Error;

use Neos\Flow\Annotations as Flow;
use Rollbar\Rollbar;
use Rollbar\Payload\Level;

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
        Rollbar::log(Level::ERROR, $exception);
        parent::handleException($exception);
    }
}
