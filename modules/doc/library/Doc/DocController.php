<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Module\Doc;

use Icinga\Web\Controller\ModuleActionController;

class DocController extends ModuleActionController
{
    /**
     * Render a chapter
     *
     * @param string    $path           Path to the documentation
     * @param string    $chapterTitle   Title of the chapter
     * @param string    $url
     * @param array     $urlParams
     */
    protected function renderChapter($path, $chapterTitle, $url, array $urlParams = array())
    {
        $parser = new DocParser($path);
        $this->view->sectionRenderer = new SectionRenderer(
            $parser->getDocTree(),
            SectionRenderer::decodeUrlParam($chapterTitle),
            $url,
            $urlParams
        );
        $this->_helper->viewRenderer('chapter', null, true);
    }

    /**
     * Render a toc
     *
     * @param string    $path           Path to the documentation
     * @param string    $name           Name of the documentation
     * @param string    $url
     * @param array     $urlParams
     */
    protected function renderToc($path, $name, $url, array $urlParams = array())
    {
        $parser = new DocParser($path);
        $this->view->tocRenderer = new TocRenderer($parser->getDocTree(), $url, $urlParams);
        $this->view->docName = $name;
        $this->_helper->viewRenderer('toc', null, true);
    }

    /**
     * Render a pdf
     *
     * @param string    $path           Path to the documentation
     * @param string    $name           Name of the documentation
     * @param string    $url
     * @param array     $urlParams
     */
    protected function renderPdf($path, $name, $url, array $urlParams = array())
    {
        $parser = new DocParser($path);
        $docTree = $parser->getDocTree();
        $this->view->tocRenderer = new TocRenderer($docTree, $url, $urlParams);
        $this->view->sectionRenderer = new SectionRenderer(
            $docTree,
            null,
            $url,
            $urlParams
        );
        $this->view->docName = $name;
        $this->_helper->viewRenderer('pdf', null, true);
        $this->_request->setParam('format', 'pdf');
    }
}