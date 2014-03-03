<?php
/*
 * This file is part of the Compressit package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compressit\Handler\Image;

use Compressit\Handler\HandlerInterface;
use Compressit\Exception\DriverNotFoundException;

/**
 * Image Handler for Compressit
 *
 * Allows to compress supported image types.
 */
class Image implements HandlerInterface
{
    /**
     * Path to file
     * @var string
     */
    private $_file;

    /**
     * File specific content type (second part eg. png, jpeg, gif)
     * @see http://pl1.php.net/mime_content_type
     * @var string
     */
    private $_type;

    /**
     * Allowed content types which can be handled (compressed) by this class
     * content type => driver class name
     * @var array
     */
    private $_allowed_types = array(
        'png'  => 'Png',
        'gif'  => 'Gif',
        'jpeg' => 'Jpeg'
    );

    /**
     * Compression driver for selected file type
     * @var object Compressit\Handler\Image\Driver
     */
    private $_driver;

    /**
     * Set class vars
     *
     * @param $file Path to file
     * @param $type Type of file (eg. image/png).
     */
    public function __construct($file, $type)
    {
        if( ! isset($this->_allowed_types[$type]))
            return false;
        $this->_file = $file;
        $this->_type = $type;
        $this->_driver = $this->_getDriver();
    }

    /**
     * Compress/Minify file using proper driver
     */
    public function compress()
    {
        return $this->_driver->compress();
    }

    /**
     * Instantiate Driver class
     */
    private function _getDriver()
    {
        $prefix = '\\'.__NAMESPACE__.'\\Driver\\';
        $driver = $this->_allowed_types[$this->_type];
        $class = $prefix.$driver;
        if ( ! class_exists($class, true))
            throw new DriverNotFoundException(sprintf("Selected driver class %s doesn't exist", $class));
        return new $class($this->_file);
    }
}
