<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 6:29 PM
 */

namespace Sm\Core\Internal\Identification;

class Identifiable_mock implements Identifiable {
    use HasObjectIdentityTrait;
    public static function init() {
        $Identity = new static;
        $Identity->setObjectId(Identifier::generateIdentity($Identity));
        return $Identity;
    }
}


class IdentifierTest extends \PHPUnit_Framework_TestCase {
    public function testCanUseIdentifier() {
        $Identifiable   = Identifiable_mock::init();
        $Identifiable_1 = Identifiable_mock::init();
        $Identifiable_2 = Identifiable_mock::init();
        
        $this->assertInternalType("string", $Identifiable->getObjectId());
    }
}
