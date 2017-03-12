<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:38 PM
 */

namespace Sm;


class Util {
    /**
     * Is there a good way for us to convert this into a string?
     *
     * @param $var
     *
     * @return bool
     */
    public static function canBeString($var) {
        return $var === null || is_scalar($var) || is_callable([ $var, '__toString' ]);
    }
    
    public static function getAncestorClasses($class_name, $append_interfaces = false) {
        $classes = [];
        $class   = $class_name;
        for ($classes[] = $class; $class = get_parent_class($class); $classes[] = $class) ;
        if ($append_interfaces) $classes += class_implements($class_name);
        return $classes;
    }
    /**
     * Return a "backtrace" array with the most relevant information about a line
     *
     * @todo reconsider moving this function
     *
     * @param int $level
     * @param int $count
     *
     * @return array
     */
    public static function backtrace($level = 0, $count = 1) {
        $max_len   = 1 + $level + $count + 1;
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//        array_shift($backtrace);
        $end           = [];
        $previous_line = null;
        $previous_file = null;
        
        # iterate backwards (to keep track of line/file info) and construct an array with only the relevant information.
        for ($i = $max_len; $i--;) {
            $item                  = $backtrace[ $i ];
            $repl_item             = [];
            $previous_file         = $repl_item['file'] = $item['file'] ?? $previous_file;
            $previous_line         = $repl_item['line'] = $item['line'] ?? $previous_line;
            $repl_item['function'] = $item['function'] ?? null;
            $repl_item['class']    = $item['class'] ?? null;
            if ($i < $level) break;
            if ($i > $max_len || $i - $level >= $count) continue;
            $end[] = $s = array_filter($repl_item, function ($r) { return !!($r??false); });
        }
        $end = array_reverse($end);
        if ($count === 1) return $end[0] ?? [];
        return $end;
    }
}