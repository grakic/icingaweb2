<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Tests\Icinga\Module\Monitoring\Application\Forms\Command;

use Icinga\Test\BaseTestCase;

class RescheduleNextCheckFormTest extends BaseTestCase
{
    const FORM_CLASS = 'Icinga\Module\Monitoring\Form\Command\RescheduleNextCheckForm';

    public function testFormInvalidWhenChecktimeIsIncorrect()
    {
        $form = $this->createForm(
            self::FORM_CLASS,
            array(
                'checktime'     => '2013-24-12 17:30:00',
                'forcecheck'    => 0,
                'btn_submit'    => 'Submit'
            )
        );
        $this->assertFalse(
            $form->isSubmittedAndValid(),
            'Asserting a logically incorrect checktime as invalid'
        );

        $form2 = $this->createForm(
            self::FORM_CLASS,
            array(
                'checktime'     => 'Captain Morgan',
                'forcecheck'    => 1,
                'btn_submit'    => 'Submit'
            )
        );
        $this->assertFalse(
            $form2->isSubmittedAndValid(),
            'Providing arbitrary strings as checktime must be considered invalid'
        );

        $form3 = $this->createForm(
            self::FORM_CLASS,
            array(
                'checktime'     => '',
                'forcecheck'    => 0,
                'btn_submit'    => 'Submit'
            )
        );
        $this->assertFalse(
            $form3->isSubmittedAndValid(),
            'Missing checktime must be considered invalid'
        );
    }
}
