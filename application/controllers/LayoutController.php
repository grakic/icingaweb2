<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

use Icinga\Web\MenuRenderer;
use Icinga\Web\Controller\ActionController;
use Icinga\Web\Hook;
use Icinga\Web\Menu;
use Icinga\Web\Url;

/**
 * Create complex layout parts
 */
class LayoutController extends ActionController
{
    /**
     * Render the menu
     */
    public function menuAction()
    {
        $this->view->menuRenderer = new MenuRenderer(
            Menu::fromConfig()->order(), Url::fromRequest()->without('renderLayout')->getRelativeUrl()
        );
    }

    /**
     * Render the top bar
     */
    public function topbarAction()
    {
        $topbarHtmlParts = array();

        /** @var Hook\Layout\TopBar $hook */
        $hook = null;

        foreach (Hook::all('TopBar') as $hook) {
            $topbarHtmlParts[] = $hook->getHtml($this->getRequest(), $this->view);
        }

        $this->view->topbarHtmlParts = $topbarHtmlParts;


        $this->renderScript('parts/topbar.phtml');
    }
}
