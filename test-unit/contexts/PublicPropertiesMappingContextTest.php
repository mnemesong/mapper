<?php

namespace Mnemesong\MapperTestUnit\contexts;

use Mnemesong\Mapper\Mapper;
use Mnemesong\MapperStubs\Class1;
use PHPUnit\Framework\TestCase;

class PublicPropertiesMappingContextTest extends TestCase
{
    public function testSingleAnonimusObjectToPublicClass(): void
    {
        $obj1 = (object) [
            'var1' => 'val1',
            'var2' => 12,
            'var3' => [
                'sub1' => null,
                'sub2' => -2,
            ],
            'var4' => null,
        ];

        $obj2 = new class() {
            public string $var1;
            public int $var2;
            public array $var3;
            public ?string $var4;
        };

        $obj2 = Mapper::fromAllPublicPropsObjects([$obj1])->toClassObject($obj2);
        $this->assertEquals('val1', $obj2->var1);
        $this->assertEquals(12, $obj2->var2);
        $this->assertEquals([
            'sub1' => null,
            'sub2' => -2,
        ], $obj2->var3);
        $this->assertEquals(null, $obj2->var4);

        $obj3 = new Class1();
        $obj3 = Mapper::fromAllPublicPropsObjects([$obj1])->toClassObject($obj3);
        $this->assertEquals('val1', $obj3->var1);
        $this->assertEquals(12, $obj3->var2);
        $this->assertEquals([
            'sub1' => null,
            'sub2' => -2,
        ], $obj3->var3);
        $this->assertEquals(null, $obj3->var4);
    }
}