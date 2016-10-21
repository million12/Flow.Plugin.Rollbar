<?php
namespace M12\Rollbar\Aspect;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\AOP\JoinPointInterface;

/**
 * Class BackendModuleControllerAspect
 * inserts `rollbarConfig` data into view,
 * when in any Neos backend (sub)module.
 *
 * This is then used in the main Default.html layout,
 * to render Rollbar.html partial.
 *
 * @Flow\Aspect()
 */
class BackendModuleControllerAspect
{
    /**
     * @Flow\Inject
     * @var \M12\Rollbar\Rollbar
     */
    protected $rollbar;

    /**
     * Assign `rollbarConfig` to the view
     *
     * @Flow\After("method(TYPO3\Neos\Controller\Backend\ModuleController->initializeView())")
     * @param JoinPointInterface $joinPoint
     */
    public function initializeView(JoinPointInterface $joinPoint)
    {
        /** @var \TYPO3\Fluid\View\TemplateView $view */
        $view = $joinPoint->getMethodArgument('view');

        if ($this->rollbar->isEnabledForFrontend() && $this->rollbar->isEnabledForEnv()) {
            $view->assign('rollbarConfig', $this->rollbar->getRollbarJsSettings());
        } else {
            $view->assign('rollbarConfig', false);
        }
    }
}
