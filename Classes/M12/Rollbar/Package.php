<?php
namespace M12\Rollbar;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Package\Package as BasePackage;

/**
 * The M12.Rollbar package
 *
 */
class Package extends BasePackage
{
    /**
     * Boot the package. We wire some signals to slots here.
     *
     * @param \TYPO3\Flow\Core\Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(\TYPO3\Flow\Core\Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();
        $dispatcher->connect(
            \TYPO3\Flow\Mvc\Dispatcher::class, 'beforeControllerInvocation',
            \M12\Rollbar\Rollbar::class, 'init'
        );
    }
}
