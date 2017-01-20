<?php
namespace M12\Rollbar;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\Package as BasePackage;

/**
 * The M12.Rollbar package
 *
 */
class Package extends BasePackage
{
    /**
     * Boot the package. We wire some signals to slots here.
     *
     * @param \Neos\Flow\Core\Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(\Neos\Flow\Core\Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();
        $dispatcher->connect(
            \Neos\Flow\Mvc\Dispatcher::class, 'beforeControllerInvocation',
            \M12\Rollbar\Rollbar::class, 'init'
        );
    }
}
