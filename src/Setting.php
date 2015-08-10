<?php namespace Thetispro\Setting;

/*
 * ---------------------------------------------
 * | Do not remove!!!!                         |
 * |                                           |
 * | @package   PhoenixCore                    |
 * | @version   2.0                            |
 * | @develper  Phil F (http://www.Weztec.com) |
 * | @author    Phoenix Development Team       |
 * | @license   Free to all                    |
 * | @copyright 2013 Phoenix Group             |
 * | @link      http://www.phoenix-core.com    |
 * ---------------------------------------------
 *
 * Example syntax:
 * use Setting (If you are using namespaces)
 *
 * Single dimension
 * set:         Setting::set('name', 'Phil'))
 * get:         Setting::get('name')
 * forget:      Setting::forget('name')
 * has:         Setting::has('name')
 *
 * Multi dimensional
 * set:         Setting::set('names' , array('firstName' => 'Phil', 'surname' => 'F'))
 * setArray:    Setting::setArray(array('firstName' => 'Phil', 'surname' => 'F'))
 * get:         Setting::get('names.firstName')
 * forget:      Setting::forget('names.surname'))
 * has:         Setting::has('names.firstName')
 *
 * Clear:
 * clear:        Setting::clear()
 *
 * Using a different path (make sure the path exists and is writable) *
 * Setting::path('setting2.json')->set(array('names2' => array('firstName' => 'Phil', 'surname' => 'F')));
 *
 * Using a different filename
 * Setting::filename('setting2.json')->set(array('names2' => array('firstName' => 'Phil', 'surname' => 'F')));
 *
 * Using both a different path and filename (make sure the path exists and is writable)
 * Setting::path(app_path().'/storage/meta/sub')->filename('dummy.json')->set(array('names2' => array('firstName' => 'Phil', 'surname' => 'F')));
 */

/**
 * Class Setting
 * @package Thetispro\Setting
 */
class Setting {

    /**
     * The path to the file
     * @var string
     */
    protected $path;

    /**
     * The filename used to store the config
     * @var string
     */
    protected $filename;

    /**
     * The class working array
     * @var array
     */
    protected $settings;

    /**
     * Create the Setting instance
     * @param string $path      The path to the file
     * @param string $filename  The filename
     * @param interfaces\FallbackInterface $fallback
     */
    public function __construct($path, $filename, $fallback = null)
    {
        $this->path     = $path;
        $this->filename = $filename;
        $this->fallback = $fallback;

        // Load the file and store the contents in $this->settings
        $this->load($this->path, $this->filename);
    }

    /**
     * Set the path to the file to use
     * @param  string $path The path to the file
     * @return \Thetispro\Setting\Setting
     */
    public function path($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set the filename to use
     * @param  string $filename The filename
     * @return \Thetispro\Setting\Setting
     */
    public function filename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get a value and return it
     * @param string $key String using dot notation
     * @param Mixed $default
     * @return Mixed             The value(s) found
     */
    public function get($key = null, $default = null)
    {
        if (empty($key))
        {
            return $this->settings;
        }

        $ts = microtime(true);

        if($ts !== array_get($this->settings, $key, $ts))
        {
            return array_get($this->settings, $key);
        }

        if ( ! is_null($this->fallback) and $this->fallback->fallbackHas($key))
        {
            return $this->fallback->fallbackGet($key, $default);
        }

        return $default;
    }

     /**
     * Store the passed value in to the json file
     * @param $key
     * @param  mixed $value The value(s) to be stored
     * @return void
     */
    public function set($key, $value)
    {
        array_set($this->settings,$key,$value);
        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }

    /**
     * Forget the value(s) currently stored
     * @param  mixed $deleteKey The value(s) to be removed (dot notation)
     * @return void
     */
    public function forget($deleteKey)
    {
        array_forget($this->settings,$deleteKey);
        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }

    /**
     * Check to see if the value exists
     * @param  string  $searchKey The key to search for
     * @return boolean            True: found - False not found
     */
    public function has($searchKey)
    {
        $default = microtime(true);

        if($default == array_get($this->settings, $searchKey, $default) and !is_null($this->fallback))
        {
            return $this->fallback->fallbackHas($searchKey);
        }
        return $default !== array_get($this->settings, $searchKey, $default);
    }

    /**
     * Load the file in to $this->settings so values can be used immediately
     * @param  string $path     The path to be used
     * @param  string $filename The filename to be used
     * @return \Thetispro\Setting\Setting
     */
    public function load($path = null, $filename = null)
    {
        $this->path     = isset($path) ? $path : $this->path;
        $this->filename = isset($filename) ? $filename : $this->filename;

        if (is_file($this->path.'/'.$this->filename))
        {
            $this->settings = json_decode(file_get_contents($this->path.'/'.$this->filename), true);
        }
        else
        {
            $this->settings = [];
        }

        return $this;
    }

    /**
     * Save the file
     * @param  string $path     The path to be used
     * @param  string $filename The filename to be used
     * @return void
     */
    public function save($path = null, $filename = null)
    {
        $this->path     = isset($path) ? $path : $this->path;
        $this->filename = isset($filename) ? $filename : $this->filename;
        if ( ! file_exists($this->path))
        {
            mkdir($this->path, 0755, true);
        }

        $fh = fopen($this->path.'/'.$this->filename, 'w+');
        fwrite($fh, json_encode($this->settings));
        fclose($fh);
    }

    /**
     * Clears the JSON Config file
     */
    public function clear()
    {
        $this->settings = array();
        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }

    /**
     * This will mass assign data to the Setting
     * @param array $data
     */
    public function setArray(array $data)
    {
        foreach ($data as $key => $value)
        {
            array_set($this->settings,$key,$value);
        }

        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }
}