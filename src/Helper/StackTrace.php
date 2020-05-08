<?php

namespace PhilKra\Helper;

/**
 *
 * Stack Trace manipulation and creation functions
 *
 */
class StackTrace {

    /**
     * Function to convert debug_backtrace results to an array of stack frames
     *
     * @param array $backtrace
     * @return array
     */
    public static function convertBacktraceToStackFrames(array $backtrace) {
        $return_value = array();
        foreach ($backtrace as $index => $single_backtrace) {
            $return_value = [
                'abs_path' => $single_backtrace['file'],
                'filename' => basename($single_backtrace['file']),
                'function' => (isset($single_backtrace['function'])  ? $single_backtrace['function'] : null),
                'lineno'   => (isset($single_backtrace['line']) ? $single_backtrace['line'] : null),
                'module'   => (isset($single_backtrace['class'])? addslashes( $single_backtrace['class'] ) : null),
                'vars'     => null,
            ];
        }

        return $return_value;
    }
}