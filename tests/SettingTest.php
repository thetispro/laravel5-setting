<?php

use Thetispro\Setting\Setting;

/**
 * Class SettingTest
 */
class SettingTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Setting our settings wrapper
     */
    public $setting;

    /**
     * @var string The file name of the config
     */
    private $file = 'test.json';

    /**
     * Set Up
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setting = new Setting(sys_get_temp_dir(), $this->file);
        $this->setting->clear();
    }

    /**
     *  Delete test files
     */
    public function tearDown()
    {
        if (file_exists('test/' . $this->file))
            unlink('test/' . $this->file);
    }

    public function testSet()
    {
        $this->setting->set('testCase.foo', 'bar');
        $this->assertTrue($this->setting->has('testCase.foo'));
        $this->assertEquals('bar', $this->setting->get('testCase.foo'));
        $this->assertEquals(['foo' => 'bar'], $this->setting->get('testCase'));

        $this->setting->set('a.b', 'c');
        $this->assertTrue($this->setting->has('a'));
        $this->assertEquals(['b' => 'c'], $this->setting->get('a'));

        $this->setting->clear();
        $this->setting->set('', 'FOOBAR');
        $this->assertEquals(['' => 'FOOBAR'], $this->setting->get(''));

        $this->setting->set('1.2.3.4.5.6.7.8', 'f');
        $this->assertTrue($this->setting->has('1.2.3.4'));

        $this->setting->set('1.2.3.4.5.6.7.8.', 'f');
        $this->assertTrue($this->setting->has('1.2.3.4.5.6.7.8.'));
        $this->assertEquals('f', $this->setting->get('1.2.3.4.5.6.7.8.'));
    }

    public function testSetBooleans()
    {
        $this->setting->set('isFalse', false);
        $this->assertTrue($this->setting->has('isFalse'));
        $this->assertSame(false, $this->setting->get('isFalse'));

        $this->setting->set('isTrue', true);
        $this->assertTrue($this->setting->has('isTrue'));
        $this->assertSame(true, $this->setting->get('isTrue'));
    }

    public function testForget()
    {
        $this->setting->set('a.b.c.d.e', 'f');
        $this->setting->forget('a.b.c');
        $this->assertFalse($this->setting->has('a.b.c'));

        $this->setting->set('1.2.3.4.5.6', 'f');
        $this->setting->forget('1.2.3.4.5');
        $this->assertFalse($this->setting->has('1.2.3.4.5.6'));
        $this->assertTrue($this->setting->has('1.2.3.4'));

        $this->setting->set('1.2.3.4.5.6.', 'f');
        $this->setting->forget('1.2.3.4.5.6.');
        $this->assertFalse($this->setting->has('1.2.3.4.5.6.'));
        $this->assertTrue($this->setting->has('1.2.3.4.5'));
    }

    public function testUnicode()
    {
        $this->setting->set('a', 'Hälfte');
        $this->setting->set('b', 'Höfe');
        $this->setting->set('c', 'Hüfte');
        $this->setting->set('d', 'saß');
        $this->assertEquals('Hälfte', $this->setting->get('a'));
        $this->assertEquals('Höfe', $this->setting->get('b'));
        $this->assertEquals('Hüfte', $this->setting->get('c'));
        $this->assertEquals('saß', $this->setting->get('d'));
    }

    public function testSetArray()
    {
        $array = [
            'id' => "foo",
            'user_info' => [
                'username' => "bar",
                'recently_viewed' => false,
            ]
        ];
        $this->setting->setArray($array);
        $this->assertEquals($array, $this->setting->get());
    }

    public function testGet()
    {
        $value = $this->setting->get("key that doesn't exist", 0);
        $this->assertSame(0, $value);
    }

}
