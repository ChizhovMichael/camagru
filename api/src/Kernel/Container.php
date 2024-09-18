<?php

declare(strict_types=1);

namespace Camagru\Kernel;

use Closure; 
use Exception; 
use ReflectionClass;

class Container 
{ 
    private const SCALARS = [
        'string',
        'int',
        'bool',
        'float',
        'array',
        'object'
    ];

    protected array $instances = []; 
 
    public function set($abstract, $concrete = NULL): void
    { 
        if ($concrete === null) { 
            $concrete = $abstract; 
        } 
        $this->instances[$abstract] = $concrete; 
    } 
 
    public function get($abstract, array $parameters = [], bool $save = true) 
    {
        if (!$save) {
            return $this->resolve($abstract, $parameters);
        } 

        // if we don't have it, just register it 
        if (!isset($this->instances[$abstract])) { 
            $this->set($abstract); 
        } 
 
        return $this->resolve($this->instances[$abstract], $parameters); 
    } 

    public function has($abstract): bool
    {
        return isset($this->instances[$abstract]);
    }
 
    public function resolve($concrete, $parameters) 
    { 
        if ($concrete instanceof Closure) { 
            return $concrete($this, $parameters); 
        } 
 
        $reflector = new ReflectionClass($concrete);
        // check if class is instantiable 
        if (!$reflector->isInstantiable()) { 
            throw new Exception("Class {$concrete} is not instantiable"); 
        } 
 
        // get class constructor 
        $constructor = $reflector->getConstructor(); 
        if (is_null($constructor)) { 
            // get new instance from class 
            return $reflector->newInstance(); 
        } 
 
        // get constructor params 
        $parameters   = $constructor->getParameters(); 
        $dependencies = $this->getDependencies($parameters); 
        
 
        // get new instance with dependencies resolved 
        return $reflector->newInstanceArgs($dependencies); 
    } 
 
    public function getDependencies($parameters) 
    { 
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            if (null === $parameter->getType()) { 
                continue; 
            } 
 
            $dependency = $parameter->getType()->getName();
 
            if (null === $dependency || \in_array($dependency, self::SCALARS, true)) {
                if ($parameter->isDefaultValueAvailable()) { 
                    // get default value of parameter 
                    $dependencies[] = $parameter->getDefaultValue(); 
                } else { 
                    throw new Exception("Can not resolve class dependency {$parameter->name}"); 
                } 
            } else { 
                $dependencies[] = $this->get($dependency); 
            } 
        } 

        return $dependencies; 
    }
}
