<?php declare(strict_types=1);

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Lib\Graph;

use Psr\Log\LoggerInterface;

/**
 * A basic logger class compatible with PHP Psr 3 standards
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Logger
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private static $logger;

    /**
     * @var int
     */
    private static $verbosity = 0; // 0: silent, 1: warning, 2: info 

    /**
     * Sets up the logger
     * 
     * Works like a constructor, but it's not. This is an all static class.
     *
     * @param LoggerInterface $logger    Psr3 compatible logger object
     * @param int             $verbosity 0: silent, 1: warning, 2: info 
     * 
     * @return void
     */
    public static function setup(LoggerInterface $logger, int $verbosity = 1): void
    {
        self::$logger = $logger;
        self::setVerbosity($verbosity);
    }

    /**
     * Sets the verbosity level.
     *
     * @param int $verbosity $verbosity 0: silent, 1: warning, 2: info 
     * 
     * @return void
     */
    public static function setVerbosity(int $verbosity): void
    {
        self::$verbosity = $verbosity;
    }

    /**
     * Logs warning or more critical messages.
     *
     * @param ...$params  If a single parameter is passed, treats as a string, otherwise acts as sprintf
     *
     * @return void
     */
    public static function warning(...$params): void
    {
        if(self::$verbosity < 1) {
            return;
        }
        $msg = self::processParams($params);
        if(isset(self::$logger)) {
            self::$logger->warning($msg);
        } else {
            echo $msg.PHP_EOL;
        }
    }

    /**
     * Logs informational or debug messages.
     *
     * @param ...$params  If a single parameter is passed, treats as a string, otherwise acts as sprintf
     *
     * @return void
     */
    public static function info(...$params): void
    {
        if(self::$verbosity < 2) {
            return;
        }
        $msg = self::processParams($params);
        if(isset(self::$logger)) {
            self::$logger->info($msg);
        } else { 
            echo $msg.PHP_EOL;
        }
    }

    /**
     * A helper method to process log parameters.
     *
     * @param array $message Message parametrized similarly to how sprintf works.
     * 
     * @return void
     */
    private static function processParams(array $message): string
    {
        $msg_type = count($message);
        if($msg_type==1) {
            return (string) $message[0];
        } else if($msg_type<1) {
            return "";
        } else {
            $params = $message;
            $message = array_shift($params);
            return sprintf($message, ...$params);
        }
    }
}