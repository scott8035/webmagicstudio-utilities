<?php

/*
 * Logging utility from Web Magic Studio
 *
 * Changelog:
 *
 * 0.0.1    2022-03-05  Initial version of Logger::log and Logger::getTS
 *
 */

declare(strict_types=1);

namespace WebMagicStudio;

use DateTime;

class Logger
{
    public static function log(
        string  $message,
        bool    $needTimestamp  = false,
        bool    $onlyOnDebug    = true,
        bool    $noOutput       = false,
        bool    $useID          = true,
    ) : string {

        static $id = '';

        if ( $onlyOnDebug && ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
            return '';
        }

        if ( $needTimestamp ) {
            $ts = self::getTS()  .  ' ';
        } else {
            $ts = '';
        }

        if ( $useID ) {
            if ( empty( $id ) ) {
                $id = ( new DateTime() )->format( "i:s.u" )  .  ' ';
            }
        }

        $output = "{$ts}{$id}{$message}";

        if ( ! $noOutput ) {
            error_log( $output );
        }

        return $output;
    }

    public static function getTS() : string {
        return ( new DateTime() )->format( "Y-m-d H:i:s.u O" );
    }
}
