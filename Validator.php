<?php

/*
 * Validation utilities from Web Magic Studio.
 *
 * Changelog:
 *
 * 0.0.1    2022-03-13  Initial version
 *
 */
namespace WebMagicStudio;

class Validator {

    public static function validCssColor( string $color ) : bool {
        if (
            self::validCssColorHex( $color )  ||
            self::validCssColorHexa( $color ) ||
            self::validCssColorRgb( $color )  ||
            self::validCssColorRgba( $color )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function validCssColorHex( string $color ) : bool {
        if (
            ( strlen( $color ) === 4 || strlen( $color ) === 7 )  &&
            substr( $color, 0, 1 ) === '#'                        &&
            ctype_xdigit( substr( $color, 1 ) )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function validCssColorHexa( string $color ) : bool {
        if (
            strlen( $color ) === 9              &&
            substr( $color, 0, 1 ) === '#'      &&
            ctype_xdigit( substr( $color, 1 ) )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function validCssColorRgb( string $color ) : bool {
        if (
            preg_match( '/^rgb\((\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*)\)$/', $color, $match ) &&
            self::validCssColorRgbArgs( $match[ 1 ] )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function validCssColorRgba( string $color ) : bool {
        if (
            preg_match( '/^rgb\((\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*)\)$/', $color, $match ) &&
            self::validCssColorRgbArgs( $match[ 1 ] )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function validCssColorRgbArgs( string $rgbArgs ) : bool {
        $rgbArgs = str_replace( ' ', '', $rgbArgs );
        $chunks  = explode( ',', $rgbArgs );

        if ( count( $chunks ) !== 3 ) {
            return false;
        }

        foreach ( $chunks as $chunk ) {
            if ( ! ctype_digit( $chunk ) ) {
                return false;
            }

            $val = (int) $chunk;
            if ( $val < 0 || $val > 255 ) {
                return false;
            }
        }

        return true;
    }

    public static function validCssLength( string $length ) : bool {
        if (
            preg_match( '/^(.*?)(cm|mm|in|px|pt|pc|em|ex|ch|rem|vw|vh|vmin|vmax|%)$/', $length, $match ) &&
            is_numeric( $match[ 1 ] )
        ) {
            return true;
        }

        return false;
    }

}
