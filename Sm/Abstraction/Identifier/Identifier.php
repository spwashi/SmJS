<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 6:17 PM
 */

namespace Sm\Abstraction\Identifier;


use Sm\Util;

class Identifier {
    /** @var  array $items_by_class An array, indexed by classname, of Identifiable items, indexed by id */
    protected static $items_by_class;
    /** @var  array $items An array, indexed by id, of items with ids generated by us. */
    protected static $items;
    /**
     * Generate and register an identifier for this item
     *
     * @param Identifiable $item
     *
     * @return string
     */
    public static function generateIdentity(Identifiable $item) {
        if ($id = $item->getObjectId()) return $id;
        $class_name                                   = get_class($item);
        $random_string                                = Util::generateRandomString(15, Util::getAlphaCharacters() . '_');
        static::$items_by_class[ $class_name ]        = static::$items_by_class[ $class_name ] ??[];
        $number                                       = count(static::$items_by_class[ $class_name ]);
        $id                                           = "{{object:{$class_name}|{$random_string}|{$number}}}";
        static::$items[ $id ]                         = $item;
        static::$items_by_class[ $class_name ][ $id ] =& static::$items[ $id ];
        return $id;
    }
    public static function setItemInRegistry($id, $item) {
        static::$items[ $id ] = $item;
    }
    /**
     * Get an item from the registry by identifier.
     *
     * @param $identity
     *
     * @return mixed|null
     */
    public static function identify($identity) {
        return static::$items[ $identity ] ?? null;
    }
    /**
     * Check to see if this class contains an Identity.
     *
     * @param $identity
     *
     * @return bool
     */
    public static function has($identity) {
        return isset(static::$items[ $identity ]);
    }
    /**
     * Get a reference to something with a given identity. Must check to see if it exists first.
     *
     * @todo this should probably throw an error.
     *
     * @param $identity
     *
     * @return mixed
     */
    public static function &identify_ref($identity) {
        return static::$items[ $identity ];
    }
}