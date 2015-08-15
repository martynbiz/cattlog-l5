<?php

use Cattlog\Cattlog;

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
            'TEST_5' => array(
                'NEST_1' => 'Nest 1',
                'NEST_2' => array(
                    'DEEP_1' => 'Deep 1',
                    'DEEP_2' => 'Deep 2',
                )
            ),
        );

        $keysToRemove = array(
            'TEST_2',
            'TEST_4',
            'TEST_5.NEST_2.DEEP_2',
        );

        $expected = array(
            'TEST_1' => 'test 1',
            'TEST_3' => 'test 3',
            'TEST_5' => array(
                'NEST_1' => 'Nest 1',
                'NEST_2' => array(
                    'DEEP_1' => 'Deep 1',
                )
            ),
        );

        $actual = $cattlog->removeKeys($data, $keysToRemove);

        $this->assertEquals($expected, $actual);
    }

    public function testRemoveEmptyKeysFromData()
    {
        $cattlog = new Cattlog();

        $data = array(
            'TEST_1' => 'test 1',
            'TEST_2' => array(),
            'TEST_3' => 'test 3',
            'TEST_4' => array(
                'NESTED_1' => array(),
                'NESTED_2' => array(
                    'DEEP_1' => array(),
                ),
            ),
            'TEST_5' => array(
                'NESTED_1' => array(),
                'NESTED_2' => array(
                    'DEEP_1' => 'test nested deep 1',
                ),
            ),
        );

        $expected = array(
            'TEST_1' => 'test 1',
            'TEST_3' => 'test 3',
            'TEST_5' => array(
                'NESTED_2' => array(
                    'DEEP_1' => 'test nested deep 1',
                ),
            ),
        );

        $actual = $cattlog->removeEmptyKeys($data);

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

        $keysToAdd = array('TEST_2', 'TEST_6', 'TEST_7', 'TEST_8.NEST_1');

        $expected = array(
            'TEST_1' => 'test 1',
            'TEST_2' => 'test 2', // this one shouldn't be blank
            'TEST_3' => 'test 3',
            'TEST_4' => 'test 4',
            'TEST_5' => 'test 5',
            'TEST_6' => '',
            'TEST_7' => '',
            'TEST_8' => array(
                'NEST_1' => '',
            )
        );

        $actual = $cattlog->addKeys($data, $keysToAdd);

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

    public function testGroupKeysByFile()
    {
        $cattlog = new Cattlog();

        $keys = array(
            'messages.hello.title',
            'messages.hello.image',
            'errors.email',
            'errors.required.something',
        );

        $expected = array(
            'messages' => array(
                'hello.title',
                'hello.image',
            ),
            'errors' => array(
                'email',
                'required.something',
            ),
        );

        $actual = $cattlog->groupKeysByFile($keys);

        $this->assertEquals($expected, $actual);

        // empty

        $keys = array();

        $expected = array();

        $actual = $cattlog->groupKeysByFile($keys);

        $this->assertEquals($expected, $actual);
    }

    // data providers

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
