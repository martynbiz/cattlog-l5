<?php

use Cattlog\Cattlog;
use Cattlog\Filter\Json;
use Cattlog\Filter\Php;

class CattlogTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function testGetInstanceOfClass()
    {
        $cattlog = new Cattlog();

        $this->assertTrue($cattlog instanceof Cattlog);
    }

    public function testDiffKeys()
    {
        $cattlog = new Cattlog();

        // test data
        $old = array(
            'REMOVED_2',
            'REMAIN_1',
            'REMAIN_2',
            'REMOVED_1',
            'REMAIN_3',
        );

        // newly scanned keys
        $new = array(
            'REMAIN_3',
            'REMAIN_2',
            'NEW_1',
            'REMAIN_1',
            'NEW_2',
            'NEW_3',
        );

        // assert added
        $added = $cattlog->getAddedKeys($old, $new);
        sort($added);
        $this->assertEquals(array(
            'NEW_1',
            'NEW_2',
            'NEW_3',
        ), $added);

        // assert removed
        $added = $cattlog->getRemovedKeys($old, $new);
        sort($added);
        $this->assertEquals(array(
            'REMOVED_1',
            'REMOVED_2',
        ), $added);
    }

    public function testRemoveKeysFromData()
    {
        $cattlog = new Cattlog();

        $data = array(
            'TEST_1' => 'test 1',
            'TEST_2' => 'test 2',
            'TEST_3' => 'test 3',
            'TEST_4' => 'test 4',
            'TEST_5' => 'test 5',
        );
        $keysToRemove = array('TEST_2', 'TEST_4');

        $expected = array(
            'TEST_1' => 'test 1',
            'TEST_3' => 'test 3',
            'TEST_5' => 'test 5',
        );

        $actual = $cattlog->removeKeys($data, $keysToRemove);

        $this->assertEquals($expected, $actual);
    }

    public function testAddKeysToData()
    {
        $cattlog = new Cattlog();

        $data = array(
            'TEST_1' => 'test 1',
            'TEST_2' => 'test 2',
            'TEST_3' => 'test 3',
            'TEST_4' => 'test 4',
            'TEST_5' => 'test 5',
        );
        $keysToAdd = array('TEST_6', 'TEST_7');

        $expected = array(
            'TEST_1' => 'test 1',
            'TEST_2' => 'test 2',
            'TEST_3' => 'test 3',
            'TEST_4' => 'test 4',
            'TEST_5' => 'test 5',
            'TEST_6' => '',
            'TEST_7' => '',
        );

        $actual = $cattlog->addKeys($data, $keysToAdd);

        $this->assertEquals($expected, $actual);
    }

    // json filter

    public function testGetFilterReturnsJsonFilter()
    {
        $cattlog = new Cattlog(array(
            'filter' => 'json'
        ));

        $filter = $cattlog->getFilter();

        $this->assertTrue($filter instanceof Json);
    }

    public function testDecodingJson()
    {
        $cattlog = new Cattlog(array(
            'filter' => 'json'
        ));

        $expected = array(
            'TEST_1' => 'test 1',
            'TEST_2' => 'test 2',
            'TEST_3' => 'test 3',
        );
        $text = json_encode($expected, JSON_PRETTY_PRINT);

        $filter = $cattlog->getFilter();
        $actual = $filter->decode($text);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getEncodingData
     */
    public function testEncodingJson($data=array())
    {
        $cattlog = new Cattlog(array(
            'filter' => 'json'
        ));

        $expected = json_encode($data, JSON_PRETTY_PRINT);

        $filter = $cattlog->getFilter();
        $actual = $filter->encode($data);

        $this->assertEquals($expected, $actual);
    }

    // php filter

    public function testGetFilterReturnsPhpFilter()
    {
        $cattlog = new Cattlog(array(
            'filter' => 'php'
        ));

        $filter = $cattlog->getFilter();

        $this->assertTrue($filter instanceof Php);
    }

    public function testDecodingPhp()
    {
        $cattlog = new Cattlog(array(
            'filter' => 'php'
        ));

        $expected = array(
            'TEST_1' => 'test 1',
            'TEST_2' => 'test 2',
            'TEST_3' => 'test 3',
        );
        $text = var_export($expected, true);

        $filter = $cattlog->getFilter();
        $actual = $filter->decode($text);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getEncodingData
     */
    public function testEncodingPhp($data=array())
    {
        $cattlog = new Cattlog(array(
            'filter' => 'php'
        ));
        
        $expected = '<'.'?php' . PHP_EOL .
            PHP_EOL .
            'return ' . var_export($data, true) . ';';

        $filter = $cattlog->getFilter();
        $actual = $filter->encode($data);

        $this->assertEquals($expected, $actual);
    }

    public function getEncodingData()
    {
        return array(
            array(
                'TEST_1' => 'test 1',
                'TEST_2' => 'test 2',
                'TEST_3' => 'test 3',
            ),
            array(

            ),
        );
    }
}
