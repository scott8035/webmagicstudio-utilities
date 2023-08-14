<?php

/*
 * Utility to process the WP wp-config.php from Web Magic Studio
 *
 * Changelog:
 *
 * 0.0.1    2022-03-05  Initial version
 *
 */

namespace WebMagicStudio;

use Exception;
use WebMagicStudio\Logger;

class WPConfigReader
{
    private string $file_content = '';

    public function __construct() {
        $docroot = $_SERVER[ 'DOCUMENT_ROOT' ];
        $path_to_config = $docroot  .  DIRECTORY_SEPARATOR  .  'wp-config.php';

        if ( ! file_exists( $path_to_config ) ) {
            $path_to_config = $docroot  .  DIRECTORY_SEPARATOR  .  '..'  .  DIRECTORY_SEPARATOR  .  'wp-config.php';
            if ( ! file_exists( $path_to_config ) ) {
                throw new Exception( "Cannot find wp-config.php at {$path_to_config}" );
            }
        }

        $this->file_content = file_get_contents(
            $path_to_config,
            false,
            null,
            0,
            1024 * 1024  // Cap filesize at 1MB to avoid gigantic content
        );

    }

    public function content_as_string() : string {
        return $this->file_content;
    }

    public function content_as_array() : array {
        $lines = preg_split( '/\r?\n/s', $this->file_content );
        if ( $lines === false ) {
            $lines = [];
        }

        return $lines;
    }

    // Only attempts SIMPLE defines on one line with two quoted literal parameters and no escapes or comments.
    // We're not going to write a parser here.
    public function define_statements() : array {
        $output = [];

        foreach ( $this->content_as_array() as $line ) {
        //  if ( preg_match( '/^\s*define\(\s*[\'\"][0-9a-zA-Z_]+[\'\"]\s*,\s*[\'\"].*[\'\"]\s*\)\s*\;\s*$/', $line, $matches ) ) {
        //      $output[] = $line;
        //  }

            // Step one: Extract the full define
            if ( preg_match( '/^(define\(.+?\))/', trim( $line ), $matches ) ) {
                $line = $matches[ 1 ];
                $output[] = $line;
            } else {
                continue;
            }
        }

        return $output;
    }

    public function defined_constants() : array {
        $output = [];

        foreach ( $this->define_statements() as $line ) {
            // Step two: Extract the literals
            if ( preg_match( '/^define\((.+?)\)$/', trim( $line ), $matches ) ) {
                $args = $matches[ 1 ];
            } else {
                continue;
            }

            // Step three: Extract the key/value pair
            if ( preg_match( '/[\'\"]([a-zA-Z0-9_]+)[\'\"]\s*,\s*[\'\"](.*?)[\'\"]$/', trim( $args ), $matches ) ) {
                $key = $matches[ 1 ];
                $val = $matches[ 2 ];
                $output[ $key ] = $val;
            } else {
                continue;
            }
        }

        ksort( $output );
        return $output;
    }
}
