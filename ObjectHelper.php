<?php

/*
 * Object utilities from Web Magic Studio.
 *
 * Changelog:
 *
 * 0.0.1    2022-03-13  Initial version with
 *
 */
namespace WebMagicStudio;

class ObjectHelper {

    // As found at https://stackoverflow.com/questions/4790453/php-recursive-array-to-object#4790485
    public static function arrayToObject( array $array ) : \stdClass {
        $obj = new \stdClass;
        foreach ( $array as $k => $v ) {
            if ( strlen( $k ) ) {
                if ( is_array( $v ) ) {
                    $obj->{ $k } = self::arrayToObject( $v ); //RECURSION
                } else {
                    $obj->{ $k } = $v;
                }
            }
        }
        return $obj;
    }

}
