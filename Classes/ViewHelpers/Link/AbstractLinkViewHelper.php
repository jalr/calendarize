<?php
/**
 * Link to anything ;)
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Calendarize\ViewHelpers\Link;

use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Link to anything ;)
 *
 * @author Tim Lochmüller
 */
abstract class AbstractLinkViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * Tag type
     *
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Store the last href to avoid escaping for the URI view Helper
     *
     * @var string
     */
    protected $lastHref = '';

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('target', 'string', 'Target of link', false);
        $this->registerTagAttribute(
            'rel',
            'string',
            'Specifies the relationship between the current document and the linked document',
            false
        );
    }

    /**
     * render the link
     *
     * @param int|NULL $pageUid          target page. See TypoLink destination
     * @param array    $additionalParams query parameters to be attached to the resulting URI
     *
     * @return string Rendered page URI
     */
    public function renderLink($pageUid = null, array $additionalParams = [])
    {
        $uriBuilder = $this->controllerContext->getUriBuilder();
        $this->lastHref = (string)$uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setArguments($additionalParams)
            ->build();
        if ($this->lastHref !== '') {
            $this->tag->addAttribute('href', $this->lastHref);
            $this->tag->setContent($this->renderChildren());
            $result = $this->tag->render();
        } else {
            $result = $this->renderChildren();
        }
        return $result;
    }

    /**
     * Get the right page Uid
     *
     * @param int         $pageUid
     * @param string|NULL $contextName
     *
     * @return int
     */
    protected function getPageUid($pageUid, $contextName = null)
    {
        if (MathUtility::canBeInterpretedAsInteger($pageUid)) {
            return (int)$pageUid;
        }

        // by settings
        if ($contextName && $this->templateVariableContainer->exists('settings')) {
            $settings = $this->templateVariableContainer->get('settings');
            if (isset($settings[$contextName]) && MathUtility::canBeInterpretedAsInteger($settings[$contextName])) {
                return (int)$settings[$contextName];
            }
        }

        return (int)$GLOBALS['TSFE']->id;
    }
}
