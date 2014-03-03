<?php
/*
 * This file is part of the Compressit package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compressit\Handler;

/**
 * File handler Interface
 *
 * Each file handler should implement this to be sure that compress() method is provided.
 */
interface HandlerInterface
{
    /**
     * Set class vars, and maybe set proper driver
     *
     * @param $file Path to file
     * @param $type Type of file (eg. image/png).
     */
    function __construct($file, $type);

    /**
     * Compress/Minify file
     */
    function compress();
}