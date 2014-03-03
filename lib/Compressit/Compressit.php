<?php
/*
 * This file is part of the Compressit package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compressit;

/**
 * Compressit - File Compression/Minify Mechanism
 *
 * May be extended to support any file type. Each file type can have own driver.
 */
class Compressit
{
    /**
     * Detect file type and chose proper file Handler.
     *
     * Returns an array which provide info about operation or false if no compression applied eg:
     *
     * array(
     *      'compressed' => true,
     *      'path' => '/path/to/file.ext',
     *      'suffix' => '_compressed',
     *      'mimeType' => 'image/png',
     *      'originalSize' => 2048,
     *      'compressedSize' => 1800,
     *      'savings' => '14%'
     * )
     *
     * @param $file Path to file
     * @param $mimeType Type of file (eg. image/png).
     * @return array
     */
    public static function compress($file, $mimeType = null)
    {
        // In $mimeType is not provided - detect it
        if( ! $mimeType)
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
            $mimeType = finfo_file($finfo, $file);
            finfo_close($finfo);
        }
        // Create path to proper file handler class
        list($mimeGroup, $mimeType) = explode('/', $mimeType);
        $prefix = '\\Compressit\\Handler\\';
        $mimeGroup = ucfirst($mimeGroup);
        $class = $prefix.$mimeGroup.'\\'.$mimeGroup;
        if ( ! class_exists($class, true))
            return false;
        $handler = new $class($file, $mimeType);
        return $handler->compress();
    }
}
