<?php
/*
 * This file is part of the Compressit package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compressit\Handler\Image\Driver;

/**
 * Image compression driver Interface
 *
 * Each image compression driver should implement this to be sure that compress() method is provided.
 */
interface DriverInterface
{
    /**
     * Set class vars
     *
     * @param $file Path to file
     */
    function __construct($file);

    /**
     * Compress/Minify file
     */
    function compress();
}