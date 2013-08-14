<?php

namespace Icinga\Form\Dashboard;

use \Icinga\Application\Config as IcingaConfig;
use Icinga\Web\Form;
use Icinga\Web\Widget\Dashboard;
use Zend_Form_Element_Text;
use Zend_Form_Element_Submit;
use Zend_Form_Element_Hidden;
use Zend_Form_Element_Select;

/**
 * Form to add an url a dashboard pane
 *
 */
class AddUrlForm extends Form
{

    /**
     * Add a selection box for different panes to the form
     *
     * @param Dashboard $dashboard      The dashboard to retrieve the panes from
     */
    private function addPaneSelectionBox(Dashboard $dashboard)
    {

        $selectPane = new Zend_Form_Element_Select(
            'pane',
            array(
                'label'     => 'Dashboard',
                'required'  => true,
                'style'     => 'display:inline-block',
                'multiOptions' => $dashboard->getPaneKeyTitleArray()
            )
        );

        $newDashboardBtn = new Zend_Form_Element_Submit(
            'create_new_pane',
            array(
                'label'     => '+',
                'required'  => false,
                'style'     => 'display:inline-block'
            )
        );

        $newDashboardBtn->removeDecorator('DtDdWrapper');
        $selectPane->removeDecorator('DtDdWrapper');
        $selectPane->removeDecorator('htmlTag');


        $this->addElement($selectPane);
        $this->addElement($newDashboardBtn);
        $this->enableAutoSubmit(array('create_new_pane'));
    }

    /**
     *  Add a textfield for creating a new pane to this form
     *
     */
    private function addNewPaneTextField()
    {
        $txtCreatePane = new Zend_Form_Element_Text(
            'pane',
            array(
                'label'     => 'New dashboard title',
                'required'  => true,
                'style'     => 'display:inline-block'
            )
        );

        // Marks this field as a new pane (and prevents the checkbox being displayed when validation errors occur)
        $markAsNewPane = new Zend_Form_Element_Hidden(
            'create_new_pane',
            array(
                'required'  => true,
                'value'     => 1
            )
        );

        $cancelDashboardBtn = new Zend_Form_Element_Submit(
            'use_existing_dashboard',
            array(
                'label'     => 'X',
                'required'  => false,
                'style'     => 'display:inline-block'
            )
        );

        $cancelDashboardBtn->removeDecorator('DtDdWrapper');
        $txtCreatePane->removeDecorator('DtDdWrapper');
        $txtCreatePane->removeDecorator('htmlTag');

        $this->addElement($txtCreatePane);
        $this->addElement($cancelDashboardBtn);
        $this->addElement($markAsNewPane);
    }

    /**
     * Add elements to this form (used by extending classes)
     *
     */
    protected function create()
    {
        $dashboard = new Dashboard();
        $dashboard->readConfig(IcingaConfig::app('dashboard/dashboard'));
        $this->addElement(
            'text',
            'url',
            array(
                'label'    => 'Url',
                'required' => true,
            )
        );
        $elems = $dashboard->getPaneKeyTitleArray();

        if (empty($elems) ||    // show textfield instead of combobox when no pane is available
            ($this->getRequest()->getPost('create_new_pane', '0') &&  // or when a new pane should be created (+ button)
            !$this->getRequest()->getPost('use_existing_dashboard', '0')) // and the user didn't click the 'use existing' button
        ) {
            $this->addNewPaneTextField();
        } else {
            $this->addPaneSelectionBox($dashboard);
        }

        $this->addElement(
            'text',
            'component',
            array(
                'label'    => 'Title',
                'required' => true,
            )
        );
        $this->setSubmitLabel("Add to dashboard");
    }
}