<?php

namespace Mnemesong\Mapper;

use Mnemesong\Mapper\contexts\PublicPropertiesMappingContext;

class Mapper
{
    /**
     * @param object[] $objects
     * @return PublicPropertiesMappingContext
     */
    public static function fromAllPublicPropsObjects(array $objects): PublicPropertiesMappingContext
    {
        return new PublicPropertiesMappingContext($objects);
    }
}