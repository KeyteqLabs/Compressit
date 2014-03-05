<?php
/*
 * This file is part of the Compressit package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compressit\Handler\Image\Driver;

use Compressit\Handler\Image\Image;
use Compressit\Handler\Image\Helper\ShellCommand;

/**
 * Compress Gif files using gifsicle library
 *
 * Gifsicle library must be installed to make benefit of this class
 *
 * @see http://www.lcdf.org/gifsicle/
 */
class Gif implements DriverInterface
{
    /**
     * Output file extension
     * @const string
     */
    const EXT = '.gif';

    /**
     * Path to file
     * @var string
     */
    protected $_file;

    /**
     * Path to tmp file
     * @var string
     */
    protected $_tmpFile;

    /**
     * Original file size
     * @var int
     */
    protected $_fileSize;

    public function __construct($file)
    {
        $this->_file = $file;
        $this->_fileSize = filesize($file);
    }

    /**
     * Compress/Minify file using gifsicle
     * @return array
     * @see http://www.lcdf.org/gifsicle/
     *
     * @uses Compressit\Handler\Image\Helper\ShellCommand
     */
    public function compress()
    {
        if(ShellCommand::exist('gifsicle'))
        {
            // create tmp file to work with
            $this->_tmpFile = Image::TMPDIR.md5(time().microtime()).self::EXT;
            file_put_contents($this->_tmpFile, file_get_contents($this->_file));
            // store original file size
            $this->_fileSize = filesize($this->_tmpFile);
            $args = array($this->_tmpFile);
            $compressedFile = ShellCommand::exec('gifsicle -O2 %s -o -', $args);
            file_put_contents($this->_tmpFile, $compressedFile);
        }
        else
        {
            // Maybe throw an error? But this will make gifsicle library required.
            return false;
        }
        if(file_exists($this->_tmpFile))
        {
            clearstatcache();
            $compressedSize = filesize($this->_tmpFile);
            // If no compression return false
            if($this->_fileSize == $compressedSize)
                return false;
            $savings = round((1 -($compressedSize/$this->_fileSize)) * 100);
            return array(
                'path' => $this->_tmpFile,
                'mimeType' => 'image/gif',
                'originalSize' => $this->_fileSize,
                'compressedSize' => $compressedSize,
                'savings' => $savings.'%'
            );
        }
        else
        {
            // This may happen if result file is under minimum quality or there was no file size reduction.
            return false;
        }
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->_file = $file;
        $this->_fileSize = filesize($file);
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @return int string
     */
    public function getFileSize()
    {
        return $this->_fileSize;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix)
    {
        $this->_suffix = $suffix;
        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->_suffix;
    }
}