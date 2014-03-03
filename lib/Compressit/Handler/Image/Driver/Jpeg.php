<?php
/*
 * This file is part of the Compressit package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compressit\Handler\Image\Driver;

use Compressit\Handler\Image\Helper as Helper;

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
     * Path to file
     * @var string
     */
    protected $_file;

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

    /**
     * Compressed file extension
     * @var int
     */
    protected $_tmpDir = "jpegoptim";

    public function __construct($file)
    {
        $this->_file = $file;
        $this->_fileSize = filesize($file);
    }

    /**
     * Compress/Minify file using jpegoptim
     * @return array
     * @see https://github.com/tjko/jpegoptim
     *
     * @uses Compressit\Handler\Image\Helper\ShellCommand
     */
    public function compress()
    {
        if(ShellCommand::exist('jpegoptim'))
        {
            $args = array($this->_file, $this->_tmpDir, $this->_quality);
            ShellCommand::exec('jpegoptim %s -d %s -m %d --strip-all -o', $args);
        }
        else
        {
            // Maybe throw an error? But this will make jpegoptim library required.
            return false;
        }
        $pathInfo = pathinfo($this->_file);
        $compressedFile = implode(DIRECTORY_SEPARATOR, array(
                $pathInfo['dirname'],
                $this->_tmpDir,
                $pathInfo['basename']
            ));
        if(file_exists($compressedFile))
        {
            $compressedSize = filesize($compressedFile);
            $savings = round((1 -($compressedSize/$this->_fileSize)) * 100);
            return array(
                'path' => $compressedFile,
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