<?php

use Cattlog\FileSystem;

class FileSystemTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function testGetInstanceOfClass()
    {
        $fileSystem = new FileSystem();

        $this->assertTrue($fileSystem instanceof FileSystem);
    }

    // public function testDiffKeys()
    // {
    //     // test data
    //     $old = array(
    //         'REMOVED_2',
    //         'REMAIN_1',
    //         'REMAIN_2',
    //         'REMOVED_1',
    //         'REMAIN_3',
    //     );
    //
    //     // newly scanned keys
    //     $new = array(
    //         'REMAIN_3',
    //         'REMAIN_2',
    //         'NEW_1',
    //         'REMAIN_1',
    //         'NEW_2',
    //         'NEW_3',
    //     );
    //
    //     // assert added
    //     $added = $this->cattlog->getAddedKeys($old, $new);
    //     sort($added);
    //     $this->assertEquals(array(
    //         'NEW_1',
    //         'NEW_2',
    //         'NEW_3',
    //     ), $added);
    //
    //     // assert removed
    //     $added = $this->cattlog->getRemovedKeys($old, $new);
    //     sort($added);
    //     $this->assertEquals(array(
    //         'REMOVED_1',
    //         'REMOVED_2',
    //     ), $added);
    // }
    //
    // public function testRemoveKeysFromData()
    // {
    //     $data = array(
    //         'TEST_1' => 'test 1',
    //         'TEST_2' => 'test 2',
    //         'TEST_3' => 'test 3',
    //         'TEST_4' => 'test 4',
    //         'TEST_5' => array(
    //             'NEST_1' => 'Nest 1',
    //             'NEST_2' => array(
    //                 'DEEP_1' => 'Deep 1',
    //                 'DEEP_2' => 'Deep 2',
    //             )
    //         ),
    //         'TEST_6' => array(
    //             'NEST_6' => array(), // an empty to be cleaned
    //         ),
    //     );
    //
    //     $keysToRemove = array(
    //         'TEST_2',
    //         'TEST_4',
    //         'TEST_5.NEST_2.DEEP_2',
    //     );
    //
    //     $expected = array(
    //         'TEST_1' => 'test 1',
    //         'TEST_3' => 'test 3',
    //         'TEST_5' => array(
    //             'NEST_1' => 'Nest 1',
    //             'NEST_2' => array(
    //                 'DEEP_1' => 'Deep 1',
    //             )
    //         ),
    //     );
    //
    //     $actual = $this->cattlog->removeKeys($data, $keysToRemove);
    //
    //     $this->assertEquals($expected, $actual);
    // }
    //
    // public function testRemoveEmptyKeysFromData()
    // {
    //     $data = array(
    //         'TEST_1' => 'test 1',
    //         'TEST_2' => array(),
    //         'TEST_3' => 'test 3',
    //         'TEST_4' => array(
    //             'NESTED_1' => array(),
    //             'NESTED_2' => array(
    //                 'DEEP_1' => array(),
    //             ),
    //         ),
    //         'TEST_5' => array(
    //             'NESTED_1' => array(),
    //             'NESTED_2' => array(
    //                 'DEEP_1' => 'test nested deep 1',
    //             ),
    //         ),
    //     );
    //
    //     $expected = array(
    //         'TEST_1' => 'test 1',
    //         'TEST_3' => 'test 3',
    //         'TEST_5' => array(
    //             'NESTED_2' => array(
    //                 'DEEP_1' => 'test nested deep 1',
    //             ),
    //         ),
    //     );
    //
    //     $actual = $this->cattlog->removeEmptyKeys($data);
    //
    //     $this->assertEquals($expected, $actual);
    // }
    //
    // public function testAddKeysToData()
    // {
    //     $data = array(
    //         'TEST_1' => 'test 1',
    //         'TEST_2' => 'test 2',
    //         'TEST_3' => 'test 3',
    //         'TEST_4' => 'test 4',
    //         'TEST_5' => 'test 5',
    //     );
    //
    //     $keysToAdd = array('TEST_2', 'TEST_6', 'TEST_7', 'TEST_8.NEST_1');
    //
    //     $expected = array(
    //         'TEST_1' => 'test 1',
    //         'TEST_2' => 'test 2', // this one shouldn't be blank
    //         'TEST_3' => 'test 3',
    //         'TEST_4' => 'test 4',
    //         'TEST_5' => 'test 5',
    //         'TEST_6' => '',
    //         'TEST_7' => '',
    //         'TEST_8' => array(
    //             'NEST_1' => '',
    //         )
    //     );
    //
    //     $actual = $this->cattlog->addKeys($data, $keysToAdd);
    //
    //     $this->assertEquals($expected, $actual);
    // }
    //
    // public function testGroupKeysByFile()
    // {
    //     $keys = array(
    //         'messages.hello.title',
    //         'messages.hello.image',
    //         'errors.email',
    //         'errors.required.something',
    //     );
    //
    //     $expected = array(
    //         'messages' => array(
    //             'hello.title',
    //             'hello.image',
    //         ),
    //         'errors' => array(
    //             'email',
    //             'required.something',
    //         ),
    //     );
    //
    //     $actual = $this->cattlog->groupKeysByFile($keys);
    //
    //     $this->assertEquals($expected, $actual);
    //
    //     // empty
    //
    //     $keys = array();
    //
    //     $expected = array();
    //
    //     $actual = $this->cattlog->groupKeysByFile($keys);
    //
    //     $this->assertEquals($expected, $actual);
    // }
    //
    // public function testSetValue()
    // {
    //     $actual = array(
    //         'errors' => array(
    //             'email' => 'Email',
    //             'req' => array(
    //                 'nested' => 'Nested'
    //             ),
    //         ),
    //     );
    //
    //     // we'll create a copy from actual first, setValue will alter it
    //     $expected = array(
    //         'errors' => array(
    //             'email' => 'Email SET',
    //             'req' => array(
    //                 'nested' => 'Nested SET'
    //             ),
    //         ),
    //         'shunsuke' => 'GOAL',
    //     );
    //
    //     $this->cattlog->setValue($actual, 'errors.email', 'Email SET');
    //     $this->cattlog->setValue($actual, 'errors.req.nested', 'Nested SET');
    //     $this->cattlog->setValue($actual, 'shunsuke', 'GOAL');
    //
    //     $this->assertEquals($expected, $actual);
    // }

    public function testGetDestFiles()
    {
        $fileSystem = new FileSystem(array(
            'dest' => array(
                'resources/lang/{lang}/messages.php',
                'resources/lang/{lang}/errors.php',
            ),
            'project_dir' => '/var/www/myproject/',
        ));

        $lang = 'en';

        // also checks that project_dir right slash is trimmed :)
        $expected = array(
            '/var/www/myproject/resources/lang/en/messages.php',
            '/var/www/myproject/resources/lang/en/errors.php',
        );

        $actual = $fileSystem->getDestFiles($lang);

        $this->assertEquals($expected, $actual);
    }

    public function testGetDestFilesWhenDestIsString()
    {
        $fileSystem = new FileSystem(array(
            'dest' => 'resources/lang/{lang}/messages.php',
            'project_dir' => '/var/www/myproject/',
        ));

        $lang = 'en';

        // also checks that project_dir right slash is trimmed :)
        $expected = array(
            '/var/www/myproject/resources/lang/en/messages.php',
        );

        $actual = $fileSystem->getDestFiles($lang);

        $this->assertEquals($expected, $actual);
    }

    public function testGetDestFileByCollection()
    {
        $fileSystem = new FileSystem(array(
            'dest' => array(
                'resources/lang/{lang}/messages.php',
                'resources/lang/{lang}/errors.php',
            ),
            'project_dir' => '/var/www/myproject/',
        ));

        // existing collection

        $lang = 'en';
        $collection = 'messages';

        $expected = '/var/www/myproject/resources/lang/en/messages.php';

        $actual = $fileSystem->getDestFileByCollection($lang, $collection);

        $this->assertEquals($expected, $actual);

        // missing collection

        $lang = 'en';
        $collection = 'missing';

        $expected = null;

        $actual = $fileSystem->getDestFileByCollection($lang, $collection);

        $this->assertEquals($expected, $actual);
    }

    public function testGetDestArrayByKey()
    {
        $fileSystem = new FileSystem(array(
            'dest' => array(
                'resources/lang/{lang}/messages.php',
                'resources/lang/{lang}/errors.php',
            ),
            'project_dir' => '/var/www/myproject/',
        ));

        // existing collection

        $lang = 'en';
        $key = 'messages.header.title';

        $expected = array(
            '/var/www/myproject/resources/lang/en/messages.php',
            'header.title',
        );

        $actual = $fileSystem->getDestArrayByKey($lang, $key);

        $this->assertEquals($expected, $actual);

        // missing collection

        $lang = 'en';
        $key = 'missing.header.title';

        $expected = array(
            null,
            'header.title',
        );

        $actual = $fileSystem->getDestArrayByKey($lang, $key);

        $this->assertEquals($expected, $actual);
    }

    // public function testSetValueWithOverwriteFalseOption()
    // {
    //     $actual = array(
    //         'errors' => array(
    //             'email' => 'Email',
    //             'req' => array(
    //                 'nested' => 'Nested'
    //             ),
    //         ),
    //     );
    //
    //     // we'll create a copy from actual first, setValue will alter it
    //     $expected = array(
    //         'errors' => array(
    //             'email' => 'Email',
    //             'req' => array(
    //                 'nested' => 'Nested'
    //             ),
    //         ),
    //         'shunsuke' => 'GOAL',
    //     );
    //
    //     $options = array(
    //         'overwrite' => false,
    //     );
    //
    //     $this->cattlog->setValue($actual, 'errors.email', 'Email SET', $options);
    //     $this->cattlog->setValue($actual, 'errors.req.nested', 'Nested SET', $options);
    //     $this->cattlog->setValue($actual, 'shunsuke', 'GOAL', $options);
    //
    //     $this->assertEquals($expected, $actual);
    // }
    //
    // public function testHasValue()
    // {
    //     $data = array(
    //         'errors' => array(
    //             'email' => 'Email',
    //             'req' => array(
    //                 'nested' => 'Nested',
    //             ),
    //         ),
    //     );
    //
    //     $this->assertTrue( $this->cattlog->hasValue($data, 'errors.email') );
    //     $this->assertTrue( $this->cattlog->hasValue($data, 'errors.req.nested') );
    //     $this->assertFalse( $this->cattlog->hasValue($data, 'shunsuke') );
    //     $this->assertFalse( $this->cattlog->hasValue($data, 'shunsuke.nested') );
    // }
}
