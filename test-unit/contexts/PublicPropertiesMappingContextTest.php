<?php

namespace Mnemesong\MapperTestUnit\contexts;

use Mnemesong\Mapper\Mapper;
use Mnemesong\MapperStubs\ClassWithV1V2;
use Mnemesong\MapperStubs\ClassWithV1V2V3V4;
use Mnemesong\MapperStubs\ClassWithV1V5;
use Mnemesong\MapperStubs\ClassWithV3V4;
use PHPUnit\Framework\TestCase;

class PublicPropertiesMappingContextTest extends TestCase
{
    /**
     * @return void
     */
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

        $obj3 = new ClassWithV1V2V3V4();
        $obj3 = Mapper::fromAllPublicPropsObjects([$obj1])->toClassObject($obj3);
        $this->assertEquals('val1', $obj3->var1);
        $this->assertEquals(12, $obj3->var2);
        $this->assertEquals([
            'sub1' => null,
            'sub2' => -2,
        ], $obj3->var3);
        $this->assertEquals(null, $obj3->var4);
    }

    /**
     * @return void
     */
    public function testMultipleClassesToClassMapping(): void
    {
        $in1 = new ClassWithV1V2();
        $in1->var1 = 'hello';
        $in1->var2 = 412;

        $in2 = new ClassWithV3V4();
        $in2->var3 = ['aboba'];
        $in2->var4 = null;

        $out = new ClassWithV1V2V3V4();
        $result = Mapper::fromAllPublicPropsObjects([$in1, $in2])->toClassObject($out);
        /* @var ClassWithV1V2V3V4 $result */
        $this->assertEquals('hello', $result->var1);
        $this->assertEquals(412, $result->var2);
        $this->assertEquals(['aboba'], $result->var3);
        $this->assertEquals(null, $result->var4);
    }

    /**
     * @return void
     */
    public function testClassWithAnonimousToClassMapping(): void
    {
        $in1 = new ClassWithV1V2();
        $in1->var1 = 'hello';
        $in1->var2 = 412;

        $in2 = (object) [
            'var3' => ['aboba'],
            'var4' => null
        ];

        $out = new ClassWithV1V2V3V4();
        $result = Mapper::fromAllPublicPropsObjects([$in1, $in2])->toClassObject($out);
        /* @var ClassWithV1V2V3V4 $result */
        $this->assertEquals('hello', $result->var1);
        $this->assertEquals(412, $result->var2);
        $this->assertEquals(['aboba'], $result->var3);
        $this->assertEquals(null, $result->var4);
    }

    /**
     * @return void
     */
    public function testMissingDataException(): void
    {
        $in1 = new ClassWithV1V2();
        $in1->var1 = 'hello';
        $in1->var2 = 412;

        $out = new ClassWithV1V5();

        $this->expectException(\RuntimeException::class);
        $result = Mapper::fromAllPublicPropsObjects([$in1])->toClassObject($out);
    }

    public function testConflictDataException(): void
    {
        $in1 = new ClassWithV1V2();
        $in1->var1 = 'hello';
        $in1->var2 = 412;

        $in2 = (object) [
            'var2' => 512,
            'var3' => ['aboba'],
            'var4' => null
        ];

        $out = new ClassWithV1V2V3V4();
        $this->expectException(\RuntimeException::class);
        $result = Mapper::fromAllPublicPropsObjects([$in1, $in2])->toClassObject($out);
    }
}