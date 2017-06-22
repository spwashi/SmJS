<?php
/**
 * User: Sam Washington
 * Date: 6/19/17
 * Time: 8:41 PM
 */

namespace Sm\Core\Context;


use Sm\Application\PathContainer;
use Sm\Core\Error\UnimplementedError;
use Sm\Core\Factory\FactoryContainer;

/**
 * Class ResolutionContext
 *
 * A Context that tells us about what we, in the development enviroment, have access to resolve.
 * Primarily Core things like Factories or
 *
 * @property-read PathContainer                                              $Paths
 * @property-read FactoryContainer                                           $Factories
 */
class ResolutionContext implements Context {
    /**
     * @var \Sm\Core\Factory\FactoryContainer A Container that allows to register different factories for different purposes
     */
    protected $FactoryContainer;
    /**
     * @var \Sm\Application\PathContainer A Container that allows us to create and name different Paths
     */
    protected $PathContainer;
    
    public function __get($name) {
        if ($name === 'Paths') return $this->PathContainer;
        if ($name === 'Factories') return $this->FactoryContainer;
        throw new UnimplementedError("Cannot resolve {$name}");
    }
    /**
     * Set the FactoryContainer for this class for FactoryResolution
     *
     * @param \Sm\Core\Factory\FactoryContainer $FactoryContainer
     *
     * @return $this
     */
    public function setFactoryContainer(FactoryContainer $FactoryContainer) {
        $this->FactoryContainer = $FactoryContainer;
        return $this;
    }
    /**
     * Set the PathContainer of this class for Path resolution
     *
     * @param \Sm\Application\PathContainer $PathContainer
     *
     * @return $this
     */
    public function setPathContainer(PathContainer $PathContainer) {
        $this->PathContainer = $PathContainer;
        return $this;
    }
}