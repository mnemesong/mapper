<?php

namespace Mnemesong\Mapper\contexts;

use Webmozart\Assert\Assert;

class PublicPropertiesMappingContext
{
    /* @var object[] $objects */
    /* @phpstan-ignore-next-line */
    protected array $objects;

    /**
     * @param object[] $objects
     */
    public function __construct(array $objects)
    {
        Assert::allObject($objects);
        $this->objects = $objects;
    }

    /**
     * @param object $target
     * @param bool $hydrateFullStrictly
     * @return object
     */
    public function toClassObject(object $target, bool $hydrateFullStrictly = true): object
    {
        $targetClass = get_class($target);
        Assert::notEq(get_class($target), get_class((object) []), "hydrated object should not be stdObject class");
        $propsOfTarget = get_class_vars($targetClass);

        $propValues = [];
        foreach($propsOfTarget as $propName => $propVal)
        {
            $valVariants = $this->collectValueVariants($propName);
            if(count($valVariants) === 0) {
                if($hydrateFullStrictly === true) {
                    throw new \RuntimeException("Had not found value for preperty " . $propName);
                } else {
                    continue;
                }
            }
            $propValues[$propName] = $this->assertValue($valVariants);
        }
        return $this->hydrateObjectByProps($propValues, $target);
    }

    /**
     * @param string $propName
     * @return array
     */
    /* @phpstan-ignore-next-line */
    protected function collectValueVariants(string $propName): array
    {
        $valVariants = [];
        foreach ($this->objects as $initObj)
        {
            if(property_exists($initObj, $propName)) {
                $valVariants[] = $initObj->$propName;
            }
        }
        return $valVariants;
    }

    /**
     * @param array $valVariants
     * @return mixed
     */
    /* @phpstan-ignore-next-line */
    protected function assertValue(array $valVariants)
    {
        $nominal = current($valVariants);
        foreach ($valVariants as $val)
        {
            if($val !== $nominal) {
                throw new \RuntimeException("Values conflict at mapping: some objects have different "
                    . "values: " . $nominal . ' and ' . $val);
            }
        }
        return current($valVariants);
    }

    /**
     * @param string[] $props
     * @param object $hydrated
     * @return object
     */
    protected function hydrateObjectByProps(array $props, object $hydrated): object
    {
        foreach ($props as $propName => $val)
        {
            $hydrated->$propName = $val;
        }
        return $hydrated;
    }
}