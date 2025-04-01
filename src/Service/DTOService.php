<?php

namespace App\Service;

use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class DTOService
{

    public function __construct(
        private PropertyAccessorInterface $propertyAccessor
    )
    {
    }

    public function transferDataTo(&$origin, &$destination, $callbackFieldName = null, $fields = null)
    {
        if(!$callbackFieldName) {
            $callbackFieldName = fn($f)=>$f;
        }
        $openingBracketOr = is_array($origin) ? '[' : '';
        $closingBracketOr = is_array($origin) ? ']' : '';
        $openingBracketDest = is_array($destination) ? '[' : '';
        $closingBracketDest = is_array($destination) ? ']' : '';
        $originFields = $fields ?: $this->getFieldNames($origin);
        $destinationFields = $this->getFieldNames($destination);
        foreach ($originFields as $of) {
            $orFieldTransformed = call_user_func($callbackFieldName, $of);
            if(is_array($destination) || in_array($orFieldTransformed, $destinationFields)){
                $this->propertyAccessor->setValue(
                    $destination,
                    $openingBracketDest.$orFieldTransformed.$closingBracketDest,
                    $this->propertyAccessor->getValue($origin, $openingBracketOr.$of.$closingBracketOr)
                );
            }
        }
    }
    public function getFieldNames($object)
    {
        if(is_array($object)) {
            return array_keys($object);
        }
        if( ! is_string($object)){
            $class = get_class($object);
        } else {
            $class = $object;
        }
        $reflectionClass = new ReflectionClass($class);
        $props = $reflectionClass->getProperties();
        return array_map(fn($p) => $p->getName(), $props);
    }
}
