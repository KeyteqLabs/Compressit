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
 * Compress PNG files using pngquant library
 *
 * Pngquant library must be installed to make benefit of this class
 *
 * @see http://pngquant.org/
 */
class Png implements DriverInterface
{
    /**
     * Output file extension
     * @const string
     */
    const EXT = '.png';

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

    /**
     * Minimum quality
     * @var int
     */
    protected $_qualityMin = 45;

    /**
     * Maximum quality
     * @var int
     */
    protected $_qualityMax = 65;


    public function __construct($file)
    {
       $this->_file = $file;
       $this->_fileSize = filesize($file);
    }

    /**
     * Compress/Minify file using Pngquant
     * @return array
     * @see http://pngquant.org/
     *
     * @uses Compressit\Handler\Image\Helper\ShellCommand
     */
    public function compress()
    {
        if(ShellCommand::exist('pngquant'))
        {
            // create tmp file to work with
            $this->_tmpFile = Image::TMPDIR.md5(time().microtime()).self::EXT;
            file_put_contents($this->_tmpFile, file_get_contents($this->_file));
            // store original file size
            $this->_fileSize = filesize($this->_tmpFile);
            $args = array($this->_qualityMin, $this->_qualityMax, $this->_tmpFile);
            $compressedFile = ShellCommand::exec('pngquant --quality=%d-%d - < %s', $args);
            file_put_contents($this->_tmpFile, $compressedFile);
        }
        else
        {
            // Maybe throw an error? But this will make pngquant and libpng libraries required.
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
                'mimeType' => 'image/png',
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
     * @param int $qualityMax
     * @return $this
     */
    public function setQualityMax($qualityMax)
    {
        $this->_qualityMax = (int) $qualityMax;
        return $this;
    }

    /**
     * @return int
     */
    public function getQualityMax()
    {
        return $this->_qualityMax;
    }

    /**
     * @param int $qualityMin
     * @return $this
     */
    public function setQualityMin($qualityMin)
    {
        $this->_qualityMin = (int) $qualityMin;

        return $this;
    }

    /**
     * @return int
     */
    public function getQualityMin()
    {
        return $this->_qualityMin;
    }

    /**
     * @param int $suffix
     * @return $this
     */
    public function setSuffix($suffix)
    {
        $this->_suffix = $suffix;

        return $this;
    }

    /**
     * @return int
     */
    public function getSuffix()
    {
        return $this->_suffix;
    }
}