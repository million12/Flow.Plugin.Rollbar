<?php
namespace M12\Rollbar\ViewHelpers;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\View\StandaloneView;

/**
 * Class RenderRollbarViewHelper
 * which renders Rollbar config and JS snippet.
 *
 * = Examples =
 *
 * <code title="Rendering partials">
 *  {namespace rollbar=M12\Rollbar\ViewHelpers}
 *  <rollbar:renderRollbar />
 * </code>
 */
class RenderRollbarViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @Flow\Inject()
     * @var \M12\Rollbar\Rollbar
     */
    protected $rollbar;

    /**
     * @Flow\InjectConfiguration(path="templatePath")
     * @var string
     */
    protected $settingTemplatePath;

    /**
     * Disable output escaping for this view helper
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return string
     */
    public function render()
    {
        if ($this->rollbar->isEnabledForFrontend() && $this->rollbar->isEnabledForEnv()) {
            $view = new StandaloneView($this->controllerContext->getRequest());
            $view->setTemplateSource(file_get_contents($this->settingTemplatePath));
            $view->assign('rollbarConfig', $this->rollbar->getRollbarJsSettings());
            return $view->render();
        }

        return null;
    }
}
