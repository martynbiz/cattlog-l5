<?php

use Cattlog\Cattlog;
use Cattlog\FileSystem;

class CattlogTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Cattlog $cattlog Object we'll be testing
     */
     protected $cattlog;

     /**
      * @var FileSystem_mock $fs
      */
      protected $fsMock;

    public function setUp()
    {
        // mock file system
        $this->fsMock = new FileSystem($config);

        // instantiate the cattlog obj
        $this->cattlog = new Cattlog($this->fsMock);
    }

    public function testGetInstanceOfClass()
    {
        $this->assertTrue($this->cattlog instanceof Cattlog);
    }

    public function testDiffKeys()
    {
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
        $added = $this->cattlog->getAddedKeys($old, $new);
        sort($added);
        $this->assertEquals(array(
            'NEW_1',
            'NEW_2',
            'NEW_3',
        ), $added);

        // assert removed
        $added = $this->cattlog->getRemovedKeys($old, $new);
        sort($added);
        $this->assertEquals(array(
            'REMOVED_1',
            'REMOVED_2',
        ), $added);
    }

    public function testRemoveKeysFromData()
    {
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

        $actual = $this->cattlog->removeKeys($data, $keysToRemove);

        $this->assertEquals($expected, $actual);
    }

    public function testRemoveEmptyKeysFromData()
    {
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

        $actual = $this->cattlog->removeEmptyKeys($data);

        $this->assertEquals($expected, $actual);
    }

    public function testAddKeysToData()
    {
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

        $actual = $this->cattlog->addKeys($data, $keysToAdd);

        $this->assertEquals($expected, $actual);
    }

    public function testGroupKeysByFile()
    {
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

        $actual = $this->cattlog->groupKeysByFile($keys);

        $this->assertEquals($expected, $actual);

        // empty

        $keys = array();

        $expected = array();

        $actual = $this->cattlog->groupKeysByFile($keys);

        $this->assertEquals($expected, $actual);
    }
}
