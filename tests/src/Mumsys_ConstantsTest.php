<?php

/**
 * Constants Test
 * Test for required constants in mumsys
 *
 * @see also tests/testconstants.php 4 phpstan
 */
class Mumsys_ConstantsTest
    extends Mumsys_Unittest_Testcase
{


    protected function setUp(): void
    {

    }


    protected function tearDown(): void
    {

    }


    public function testIncludedConstants()
    {
        $this->assertingEquals( MUMSYS_REGEX_AZ09, '/^([0-9a-zA-Z])+$/i' );
        $this->assertingEquals( MUMSYS_REGEX_ALNUM, '/^([_]|[0-9a-zA-Z])+$/i' );
        $this->assertingEquals( MUMSYS_REGEX_AZ09X, '/^([_-]|[0-9a-zA-Z])+$/i' );

        $this->assertingEquals(
            MUMSYS_REGEX_DATETIME_MYSQL, '/^(\d{4})-(\d{2})-(\d{2} (\d{1,2}):(\d{1,2}):(\d{1,2}))$/'
        );

        $this->assertingEquals( MUMSYS_REGEX_AZ09X_, '/^([ ]|[_-]|[0-9a-zA-Z])+$/i' );

        $this->assertingEquals( _NL, "\n" );
    }

}
