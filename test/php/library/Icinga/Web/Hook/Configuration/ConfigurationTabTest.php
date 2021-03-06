<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Tests\Icinga\Web\Hook\Configuration;

use Icinga\Test\BaseTestCase;
use Icinga\Web\Hook\Configuration\ConfigurationTab;

class ConfigurationTabTest extends BaseTestCase
{
    public function testCreate1()
    {
        $tab = new ConfigurationTab(
            'test1',
            '/test/$555',
            'TEST_TITLE'
        );

        $this->assertEquals('test1', $tab->getModuleName());

        $testArray = array(
            'title' => 'TEST_TITLE',
            'url' => '/test/$555'
        );

        $this->assertEquals($testArray, $tab->getTab());
    }

    public function testCreate2()
    {
        $tab = new ConfigurationTab(
            'test2',
            '/test/$666'
        );

        $this->assertEquals('test2', $tab->getModuleName());

        $testArray = array(
            'title' => 'test2',
            'url' => '/test/$666'
        );

        $this->assertEquals($testArray, $tab->getTab());
    }

    /**
     * @expectedException Icinga\Exception\ProgrammingError
     * @expectedExceptionMessage moduleName is missing
     */
    public function testException1()
    {
        $tab = new ConfigurationTab();
        $tab->getTab();
    }

    /**
     * @expectedException Icinga\Exception\ProgrammingError
     * @expectedExceptionMessage url is missing
     */
    public function testException2()
    {
        $tab = new ConfigurationTab();
        $tab->setModuleName('DING1');
        $tab->getTab();
    }

    /**
     * @expectedException Icinga\Exception\ProgrammingError
     * @expectedExceptionMessage title is missing
     */
    public function testException3()
    {
        $tab = new ConfigurationTab();
        $tab->setModuleName('DING1');
        $tab->setUrl('/ding/dong');
        $tab->getTab();
    }
}
