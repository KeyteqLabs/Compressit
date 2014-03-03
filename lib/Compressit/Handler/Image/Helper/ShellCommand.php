<?php
/*
 * This file is part of the Compressit package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compressit\Handler\Image\Helper;

use Compressit\Exception\ShellCommandException;

/**
 * Helper for shell command executing.
 */
class ShellCommand
{
    /**
     * Check if terminal command exists
     *
     * @param $cmd
     * @return bool
     * @throws ShellCommandException
     */
    public static function exist($cmd) {
        if( ! preg_match('#^[\w-_]+$#i', $cmd))
            throw new ShellCommandException("Provide command name using only letters, - and _");
        $returnVal = shell_exec("which {$cmd}");
        return (empty($returnVal) ? false : true);
    }

    /**
     * Execute shell command
     *
     * User input ($args) will be escaped by escapeshellarg()
     *
     * @param $cmd
     * @param $args
     * @return mixed
     * @throws ShellCommandException
     */
    public static function exec($cmd, $args = array()) {
        if( ! is_array($args))
            throw new ShellCommandException("Command arguments must be an array");
        foreach($args as $k => $v)
        {
            if(is_numeric($v))
                continue;
            $args[$k] = escapeshellarg($v);
        }
        $cmd = vsprintf($cmd, $args);
        return shell_exec($cmd);
    }
}
