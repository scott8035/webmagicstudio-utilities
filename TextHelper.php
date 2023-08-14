<?php

/*
 * Text utilities from Web Magic Studio.
 *
 * Changelog:
 *
 * 0.0.5    2022-09-19  Fix aLittleBit() so it doesn't give appearance of adding a space before
 *                      and after the content of a tag (it was a translated newline)
 * 0.0.4    2022-07-28  Make aLittleBit() multi-byte character aware, enclose result in
 *                      quotes, add ellipsis if length of chunk would have exceeded the
 *                      specified limit
 * 0.0.3    2022-07-02  Add aLittleBit()
 * 0.0.2    2022-03-05  Add camelCaseToSlug() and filenameToSlug()
 * 0.0.1                Initial version
 *
 */
declare(strict_types=1);

namespace WebMagicStudio;

class TextHelper {

    public static function dump( int $depth = 0, mixed ...$args ) : string {
        $output = '';

        foreach ( $args as $obj ) {
            $type = gettype( $obj );

            if ( in_array( $type, [ 'boolean' ], true ) ) {
                $output .= self::tabIndent( $depth )  .  $obj ? 'true' : 'false';

            } elseif ( in_array( $type, [ 'string', 'integer', 'double' ], true ) ) {
                $output .= self::tabIndent( $depth )  .  (string) $obj;

            } elseif ( in_array( $type, [ 'array', 'object' ], true ) ) {
                $output .= self::textBlockIndent( var_export( $obj, true ), $depth );

            } else {
                $output .= self::tabIndent( $depth )  .  "Type '{$type}' is invalid for an argument to _dump()";
            }

            $output .= "\n";
        }

        return $output;
    }


    public static function tabIndent( int $depth = 0, int $spaces_per_tab = 4 ) : string {
        if ( $spaces_per_tab < 0 ) {
            throw new \InvalidArgumentException( "Spaces-per-tab parameter < 0: {$spaces_per_tab}" );
        }

        return str_repeat( ' ', $depth * $spaces_per_tab );
    }


    public static function textBlockIndent( string $str, $depth = 0 ) : string {
        $lines  = explode( "\n", $str );
        $indent = self::tabIndent( $depth );

        $lines = array_map( fn( $line ) => $indent . $line, $lines );

        return implode( "\n", $lines );
    }

    public static function camelCaseToSlug( string $cased ) : string {
        return str_replace(
            '\\', '--',
            strtolower( preg_replace( '/([_a-z0-9])([A-Z])/', '\1-\2', $cased ) )
        );
    }

    public static function filenameToSlug( $filename ) : string {
        if ( preg_match( '/^.*\/(.+?)\.php$/', $filename, $matches ) !== false &&
             isset( $matches[ 1 ] )                                            &&
             ! empty( $matches[ 1 ] ) ) {
            return self::camelCaseToSlug( $matches[ 1 ] );
        } else {
            Logger::log( "Couldn't create slug from filename {$filename}" );
        }
    }

    public static function aLittleBit( string $chunk, int $length = 50 ) : string {
        // Presumably 10 * $length is enough text so that after compressing whitespace there is still enough to get $length characters
        $chunk2 = mb_substr( $chunk, 0, $length * 10 );
        // $chunk2 = preg_replace( '/\n/s', '', $chunk2 );
        $chunk2 = preg_replace( '/\s+/s', ' ', $chunk2 );
        $chunk2 = mb_substr( $chunk2, 0, $length );

        $chunk2 = "'{$chunk2}'";

        if ( mb_strlen( $chunk) > ( mb_strlen( $chunk2 ) - 2 ) ) {
            $chunk2 .= '...';
        }

        return $chunk2;
    }
}
