<?php

/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 18.08.2015
 * Time: 14:41
 */
class FunctionsTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    /**
     * @dataProvider providerArrayCount
     */
    public function testArrayCountSimple($a, $b, $c){
        $curval = $this->fixture->array_count($a);
        $this->assertEquals($b, $curval);
    }

    /**
     * @dataProvider providerArrayCount
     */
    public function testArrayCountRecursive($a, $b, $c){
        $curval = $this->fixture->array_count($a, 1);
        $this->assertEquals($c, $curval);
    }

    public function testSetHttpStatus(){
        $return = $this->fixture->set_http_status(404);
        $this->assertFalse($return);
    }

    public function providerArrayCount(){
        return array(
            array(
                array( 1, 2, 3 ), 3, 3
            ),
            array(
                array( 1, 2, array( 3 ) ), 3, 4
            ),
            array(
                array( 1, 2, array( 3, array( 4, 5 ) ) ), 3, 7
            )
        );
    }

    protected function setUp()
    {
        $this->fixture = new \itcube\Functions();
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }
}
