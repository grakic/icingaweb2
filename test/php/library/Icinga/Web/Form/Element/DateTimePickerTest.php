<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Tests\Icinga\Web\Form\Element;

use Icinga\Test\BaseTestCase;
use Icinga\Web\Form\Element\DateTimePicker;

class DateTimePickerTest extends BaseTestCase
{
    /**
     * Test that DateTimePicker::isValid() returns false if the input is not valid in terms of being a date/time string
     * or a timestamp
     */
    public function testValidateInvalidInput()
    {
        $dt = new DateTimePicker(
            'foo',
            array(
                'patterns' => array(
                    'd/m/Y g:i A',
                    'd.m.Y H:i:s'
                )
            )
        );

        $this->assertFalse(
            $dt->isValid('08/27/2013 12:40 PM'),
            'Wrong placed month/day must not be valid input'
        );
        $this->assertFalse(
            $dt->isValid('bar'),
            'Arbitrary strings must not be valid input'
        );
        $this->assertFalse(
            $dt->isValid('12:40 AM'),
            'Time strings must not be valid input'
        );
        $this->assertFalse(
            $dt->isValid('27/08/2013'),
            'Date strings must not be valid input'
        );
        $this->assertFalse(
            $dt->isValid('13736a16223'),
            'Invalid Unix timestamps must not be valid input'
        );
    }

    /**
     * Test that DateTimePicker::isValid() returns true if the input is valid in terms of being a date/time string
     * or a timestamp
     */
    public function testValidateValidInput()
    {
        $dt = new DateTimePicker(
            'foo',
            array(
                'patterns' => array(
                    'd/m/Y g:i A',
                    'd.m.Y H:i:s'
                )
            )
        );

        $this->assertTrue(
            $dt->isValid('27/08/2013 12:40 PM'),
            'Using a valid date/time string must not fail'
        );
        $this->assertTrue(
            $dt->isValid('12.07.2013 08:03:43'),
            'Using a valid date/time string must not fail'
        );
        $this->assertTrue(
            $dt->isValid(1373616223),
            'Using valid Unix timestamps must not fail'
        );
        $this->assertTrue(
            $dt->isValid('1373616223'),
            'Using strings as Unix timestamps must not fail'
        );
    }

    /**
     * Test that DateTimePicker::getValue() returns a timestamp after a successful call to isValid
     */
    public function testGetValueReturnsUnixTimestampAfterSuccessfulIsValidCall()
    {
        $dt = new DateTimePicker(
            'foo',
            array(
                'patterns' => array(
                    'd.m.Y H:i:s'
                )
            )
        );
        $dt->isValid('12.07.2013 08:03:43');

        $this->assertEquals(
            1373616223,
            $dt->getValue(),
            'getValue did not return the correct Unix timestamp according to the given date/time string'
        );
    }
}
