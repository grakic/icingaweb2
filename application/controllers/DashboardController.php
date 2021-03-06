<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

use \Zend_Config;
use Icinga\Web\Url;
use Icinga\Logger\Logger;
use Icinga\Config\PreservingIniWriter;
use Icinga\Application\Config as IcingaConfig;
use Icinga\Web\Widget\Dashboard;
use Icinga\Form\Dashboard\AddUrlForm;
use Icinga\Exception\NotReadableError;
use Icinga\Exception\ConfigurationError;
use Icinga\Web\Controller\ActionController;

/**
 * Handle creation, removal and displaying of dashboards, panes and components
 *
 * @see Icinga\Web\Widget\Dashboard for more information about dashboards
 */
class DashboardController extends ActionController
{
    /**
     * Default configuration
     */
    const DEFAULT_CONFIG = 'dashboard/dashboard';

    /**
     * Retrieve a dashboard from the provided config
     *
     * @param   string $config The config to read the dashboard from, or 'dashboard/dashboard' if none is given
     *
     * @return  \Icinga\Web\Widget\Dashboard
     */
    private function getDashboard($config = self::DEFAULT_CONFIG)
    {
        $dashboard = new Dashboard();
        try {
            $dashboardConfig = IcingaConfig::app($config);
            if (count($dashboardConfig) === 0) {
                return null;
            }
            $dashboard->readConfig($dashboardConfig);
        } catch (NotReadableError $e) {
            Logger::error(new Exception('Cannot load dashboard configuration. An exception was thrown:', 0, $e));
            return null;
        }
        return $dashboard;
    }

    /**
     * Remove a component from the pane identified by the 'pane' parameter
     */
    public function removecomponentAction()
    {
        $pane =  $this->_getParam('pane');
        $dashboard = $this->getDashboard();
        try {
            $dashboard->removeComponent(
                $pane,
                $this->_getParam('component')
            )->store();
            $this->redirectNow(Url::fromPath('dashboard', array('pane' => $pane)));
        } catch (ConfigurationError $exc ) {
            $this->_helper->viewRenderer('show_configuration');
            $this->view->exceptionMessage = $exc->getMessage();
            $this->view->iniConfigurationString = $dashboard->toIni();
        }
    }

    /**
     * Display the form for adding new components or add the new component if submitted
     */
    public function addurlAction()
    {
        $this->getTabs()->add(
            'addurl',
            array(
                'title' => 'Add Dashboard URL',
                'url' => Url::fromRequest()
            )
        )->activate('addurl');

        $form = new AddUrlForm();
        $form->setRequest($this->getRequest());
        $form->setAction(Url::fromRequest()->setParams(array())->getAbsoluteUrl());
        $this->view->form = $form;

        if ($form->isSubmittedAndValid()) {
            $dashboard = $this->getDashboard();
            $dashboard->setComponentUrl(
                $form->getValue('pane'),
                $form->getValue('component'),
                ltrim($form->getValue('url'), '/')
            );

            $configFile = IcingaConfig::app('dashboard/dashboard')->getConfigFile();
            if ($this->writeConfiguration(new Zend_Config($dashboard->toArray()), $configFile)) {
                $this->redirectNow(Url::fromPath('dashboard', array('pane' => $form->getValue('pane'))));
            } else {
                $this->render('show-configuration');
            }
        }
    }

    /**
     * Display the dashboard with the pane set in the 'pane' request parameter
     *
     * If no pane is submitted or the submitted one doesn't exist, the default pane is
     * displayed (normally the first one)
     */
    public function indexAction()
    {
        $dashboard = $this->getDashboard();
        if ($this->_getParam('pane')) {
            $pane = $this->_getParam('pane');
            $dashboard->activate($pane);
        }

        $this->view->configPath = IcingaConfig::resolvePath(self::DEFAULT_CONFIG);

        if ($dashboard === null) {
            $this->view->title = 'Dashboard';
        } else {
            $this->view->title = $dashboard->getActivePane()->getTitle() . ' :: Dashboard';
            $this->view->tabs = $dashboard->getTabs();

                /* Temporarily removed
                $this->view->tabs->add(
                    'Add',
                    array(
                        'title' => '+',
                        'url' => Url::fromPath('dashboard/addurl')
                    )
                );
                */

            $this->view->dashboard = $dashboard;

        }
    }

    /**
     * Store the given configuration as INI file
     *
     * @param   Zend_Config     $config     The configuration to store
     * @param   string          $target     The path where to store the configuration
     *
     * @return  bool                        Whether the configuartion has been successfully stored
     */
    protected function writeConfiguration(Zend_Config $config, $target)
    {
        $writer = new PreservingIniWriter(array('config' => $config, 'filename' => $target));

        try {
            $writer->write();
        } catch (Exception $e) {
            Logger::error(new ConfiguationError("Cannot write dashboard to $target", 0, $e));
            $this->view->iniConfigurationString = $writer->render();
            $this->view->exceptionMessage = $e->getMessage();
            $this->view->file = $target;
            return false;
        }

        return true;
    }
}
