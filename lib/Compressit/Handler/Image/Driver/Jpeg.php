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
 * Compress Gif files using jpegoptim library
 *
 * Jpegoptim library must be installed to make benefit of this class
 *
 * @see https://github.com/tjko/jpegoptim
 */
class Jpeg implements DriverInterface
{

    /**
     * File extension
     * @var string
     */
    const EXT = '.jpg';

    /**
     * Path to original file
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

    /**
     * Image quality. Use loseless compression if $_quality = 0
     * @var int
     */
    protected $_quality = 90;

    public function __construct($file)
    {
        $this->_file = $file;
    }

    /**
     * Compress/Minify file using jpegoptim
     * @return array
     * @see https://github.com/tjko/jpegoptim
     *
     * @uses Compressit\Handler\Image::TMPDIR
     * @uses Compressit\Handler\Image\Helper\ShellCommand
     */
    public function compress()
    {
        if(ShellCommand::exist('jpegoptim'))
        {
            // create tmp file to work with
            $this->_tmpFile = Image::TMPDIR.md5(time().microtime()).self::EXT;
            file_put_contents($this->_tmpFile, file_get_contents($this->_file));
            // store original file size
            $this->_fileSize = filesize($this->_tmpFile);
            $args = array($this->_tmpFile, $this->_quality);
            ShellCommand::exec('jpegoptim %s -m%d --strip-all', $args);
        }
        else
        {
            // Maybe throw an error? But this will make jpegoptim library required.
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
                'mimeType' => 'image/jpeg',
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
     * @return string
     */
    public function getFileSize()
    {
        return $this->_fileSize;
    }

    /**
     * @return string
     */
    public function getTmpFile()
    {
        return $this->_tmpFile;
    }


    /**
     * @param int $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->_quality = (int) $quality;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuality()
    {
        return $this->_quality;
    }

    /**
     * @param int $tmpDir
     * @return $this
     */
    public function setTmpDir($tmpDir)
    {
        $this->_tmpDir = $tmpDir;
        return $this;
    }

    /**
     * @return int
     */
    public function getTmpDir()
    {
        return $this->_tmpDir;
    }
}