<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;
use Eboreum\Caster\Caster;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Common\DataType\Integer\PositiveInteger;
use Eboreum\CasterDoctrineEntityFormatter\EntityFormatter;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use ReflectionProperty;
use stdClass;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAnnotations;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAnnotationsWithDebugIdentifier;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributes;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesIdBeingMultidimensional;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithBothIdAndDebugIdentifierOnTheSameProperty;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithCompositeId;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithDebugIdentifier;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithDebugIdentifierOnTheSameNamePropertyOnParentClass;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithIdOnParent;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithoutId;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithUninitializedId;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityWithDebugIdentifierOnStaticProperty;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityWithDebugIdentifierOnStaticPropertyOfParent;

class EntityFormatterTest extends TestCase
{
    public function testFormatReturnsNullWhenObjectIsNotHandled(): void
    {
        $entityFormatter = new EntityFormatter();
        $this->assertNull($entityFormatter->format(Caster::getInstance(), new stdClass));
    }

    /**
     * @dataProvider dataProvider_testFormatWorks
     */
    public function testFormatWorks(
        string $expected,
        EntityFormatter $entityFormatter,
        Caster $caster,
        object $entity,
    ): void {
        $this->assertSame($expected, $entityFormatter->format($caster, $entity));
    }

    /**
     * @return array<array{string, EntityFormatter, Caster, object}>
     */
    public function dataProvider_testFormatWorks(): array
    {
        return [
            [
                sprintf(
                    '\\%s {$id = (null) null}',
                    EntityUsingAnnotations::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAnnotations(),
            ],
            [
                sprintf(
                    '\\%s {$id = (null) null}',
                    EntityUsingAttributes::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAttributes(),
            ],
            [
                sprintf(
                    '\\%s {$id = (null) null, $foo = (uninitialized)}',
                    EntityUsingAnnotationsWithDebugIdentifier::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAnnotationsWithDebugIdentifier(),
            ],
            [
                sprintf(
                    '\\%s {$id = (null) null, $foo = (string(5)) "lorem"}',
                    EntityUsingAnnotationsWithDebugIdentifier::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                (static function (): EntityUsingAnnotationsWithDebugIdentifier {
                    $entity = new EntityUsingAnnotationsWithDebugIdentifier();
                    $entity->foo = 'lorem';

                    return $entity;
                })(),
            ],
            [
                sprintf(
                    '\\%s {$id = (null) null, $foo = (uninitialized)}',
                    EntityUsingAttributesWithDebugIdentifier::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAttributesWithDebugIdentifier(),
            ],
            [
                sprintf(
                    '\\%s {$id = (null) null, $foo = (string(5)) "lorem"}',
                    EntityUsingAttributesWithDebugIdentifier::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                (static function (): EntityUsingAttributesWithDebugIdentifier {
                    $entity = new EntityUsingAttributesWithDebugIdentifier();
                    $entity->foo = 'lorem';

                    return $entity;
                })(),
            ],
            [
                sprintf(
                    '\\%s {$a = (null) null, $b = (null) null}',
                    EntityUsingAttributesWithCompositeId::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAttributesWithCompositeId(),
            ],
            [
                sprintf(
                    '\\%s {$a = (int) 42, $b = (int) 11}',
                    EntityUsingAttributesWithCompositeId::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                (static function (): EntityUsingAttributesWithCompositeId {
                    $entity = new EntityUsingAttributesWithCompositeId();
                    $entity->a = 42;
                    $entity->b = 11;

                    return $entity;
                })(),
            ],
            [
                sprintf(
                    '\\%s {\\%s->$id = (null) null, $foo = (string(5)) "ipsum"}',
                    EntityUsingAttributesWithIdOnParent::class,
                    EntityUsingAttributesWithIdOnParent\ParentClass::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAttributesWithIdOnParent(),
            ],
            [
                sprintf(
                    '\\%s {$id = (null) null, $foo = (uninitialized)}',
                    EntityUsingAnnotationsWithDebugIdentifier::class,
                ),
                (new EntityFormatter())->withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(true),
                Caster::getInstance(),
                new EntityUsingAnnotationsWithDebugIdentifier(),
            ],
            [
                sprintf(
                    '\\%s {$id = (int) 42}',
                    EntityUsingAnnotationsWithDebugIdentifier::class,
                ),
                (new EntityFormatter())->withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(true),
                Caster::getInstance(),
                (static function (): EntityUsingAnnotationsWithDebugIdentifier {
                    $entity = new EntityUsingAnnotationsWithDebugIdentifier();
                    $entity->id = 42;

                    return $entity;
                })(),
            ],
            [
                sprintf(
                    '\\%s {$foo = (uninitialized)}',
                    EntityUsingAttributesWithoutId::class,
                ),
                (new EntityFormatter())->withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(true),
                Caster::getInstance(),
                new EntityUsingAttributesWithoutId(),
            ],
            [
                sprintf(
                    '\\%s {$foo = (string(5)) "lorem"}',
                    EntityUsingAttributesWithoutId::class,
                ),
                (new EntityFormatter())->withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(true),
                Caster::getInstance(),
                (static function (): EntityUsingAttributesWithoutId {
                    $entity = new EntityUsingAttributesWithoutId();
                    $entity->foo = 'lorem';

                    return $entity;
                })(),
            ],
            [
                sprintf(
                    '\\%s {$id = (object) \\%s}',
                    EntityUsingAttributesIdBeingMultidimensional::class,
                    EntityUsingAttributesIdBeingMultidimensional\A::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                (static function (): EntityUsingAttributesIdBeingMultidimensional {
                    $entity = new EntityUsingAttributesIdBeingMultidimensional();
                    $entity->id = new EntityUsingAttributesIdBeingMultidimensional\A();
                    $entity->id->id = new EntityUsingAttributesIdBeingMultidimensional\A\B();
                    $entity->id->id->id = 42;

                    return $entity;
                })(),
            ],
            (function (): array {
                $entityFormatter = new EntityFormatter();

                return [
                    sprintf(
                        '\\%s {$id = (object) \\%s {$id = (object) \\%s {$id = (int) 42}}}',
                        EntityUsingAttributesIdBeingMultidimensional::class,
                        EntityUsingAttributesIdBeingMultidimensional\A::class,
                        EntityUsingAttributesIdBeingMultidimensional\A\B::class,
                    ),
                    $entityFormatter,
                    Caster::getInstance()->withCustomObjectFormatterCollection(new ObjectFormatterCollection([
                        $entityFormatter,
                    ])),
                    (static function (): EntityUsingAttributesIdBeingMultidimensional {
                        $entity = new EntityUsingAttributesIdBeingMultidimensional();
                        $entity->id = new EntityUsingAttributesIdBeingMultidimensional\A();
                        $entity->id->id = new EntityUsingAttributesIdBeingMultidimensional\A\B();
                        $entity->id->id->id = 42;

                        return $entity;
                    })(),
                ];
            })(),
            (function (): array {
                $entityFormatter = new EntityFormatter();

                return [
                    /**
                     * We see 2 levels here and not 1, because we call the `format` method on the formatter, and not
                     * (initially) the `cast` method on Caster.
                     */
                    sprintf(
                        '\\%s {$id = (object) \\%s {$id = (object) \\%s: ** OMITTED ** (maximum depth of 1 reached)}}',
                        EntityUsingAttributesIdBeingMultidimensional::class,
                        EntityUsingAttributesIdBeingMultidimensional\A::class,
                        EntityUsingAttributesIdBeingMultidimensional\A\B::class,
                    ),
                    $entityFormatter,
                    Caster::getInstance()
                        ->withCustomObjectFormatterCollection(new ObjectFormatterCollection([
                            $entityFormatter,
                        ]))
                        ->withDepthMaximum(new PositiveInteger(1)),
                    (static function (): EntityUsingAttributesIdBeingMultidimensional {
                        $entity = new EntityUsingAttributesIdBeingMultidimensional();
                        $entity->id = new EntityUsingAttributesIdBeingMultidimensional\A();
                        $entity->id->id = new EntityUsingAttributesIdBeingMultidimensional\A\B();
                        $entity->id->id->id = 42;

                        return $entity;
                    })(),
                ];
            })(),
            [
                sprintf(
                    '\\%s {$id = (null) null, $foo = (string(5)) "lorem"}',
                    EntityWithDebugIdentifierOnStaticProperty::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityWithDebugIdentifierOnStaticProperty(),
            ],
            [
                sprintf(
                    '\\%s {$id = (null) null, \\%s::$foo = (string(5)) "lorem"}',
                    EntityWithDebugIdentifierOnStaticPropertyOfParent::class,
                    EntityWithDebugIdentifierOnStaticPropertyOfParent\ParentClass::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityWithDebugIdentifierOnStaticPropertyOfParent(),
            ],
            [
                sprintf(
                    '\\%s {$id = (uninitialized)}',
                    EntityUsingAttributesWithUninitializedId::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAttributesWithUninitializedId(),
            ],
            [
                sprintf(
                    '\\%s {$id = (null) null}',
                    EntityUsingAttributesWithBothIdAndDebugIdentifierOnTheSameProperty::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAttributesWithBothIdAndDebugIdentifierOnTheSameProperty(),
            ],
            [
                sprintf(
                    '\\%s {$foo = (string(5)) "lorem"}',
                    EntityUsingAttributesWithDebugIdentifierOnTheSameNamePropertyOnParentClass::class,
                ),
                new EntityFormatter(),
                Caster::getInstance(),
                new EntityUsingAttributesWithDebugIdentifierOnTheSameNamePropertyOnParentClass(),
            ],
        ];
    }

    public function testWithIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSetWorks(): void
    {
        $entityFormatterA = new EntityFormatter();
        $entityFormatterB = $entityFormatterA->withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(false);
        $entityFormatterC = $entityFormatterA->withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(true);

        $this->assertNotSame($entityFormatterA, $entityFormatterB);
        $this->assertNotSame($entityFormatterA, $entityFormatterC);
        $this->assertNotSame($entityFormatterB, $entityFormatterC);
        $this->assertFalse($entityFormatterA->isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet());
        $this->assertFalse($entityFormatterB->isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet());
        $this->assertTrue($entityFormatterC->isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet());
    }

    /**
     * @dataProvider dataProvider_testDoesReflectionPropertyHaveAttributeWorks
     * @param bool $expected
     * @param class-string $className
     */
    public function testDoesReflectionPropertyHaveAttributeWorks(
        bool $expected,
        ReflectionProperty $reflectionProperty,
        string $className,
    ): void {
        $entityFormatter = new EntityFormatter();
        $this->assertSame(
            $expected,
            $entityFormatter->doesReflectionPropertyHaveAttribute($reflectionProperty, $className),
        );
    }

    /**
     * @return array<array{bool, ReflectionProperty, class-string}>
     */
    public function dataProvider_testDoesReflectionPropertyHaveAttributeWorks(): array
    {
        return [
            [
                false,
                (static function () {
                    $entity = new EntityUsingAnnotations();
                    $reflectionObject = new ReflectionObject($entity);
                    $reflectionProperty = $reflectionObject->getProperty('id');

                    return $reflectionProperty;
                })(),
                ORM\Id::class,
            ],
            [
                true,
                (static function () {
                    $entity = new EntityUsingAttributes();
                    $reflectionObject = new ReflectionObject($entity);
                    $reflectionProperty = $reflectionObject->getProperty('id');

                    return $reflectionProperty;
                })(),
                ORM\Id::class,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider_testDoesReflectionPropertyHaveClassAnnotationAnnotationWorks
     * @param bool $expected
     * @param class-string $className
     */
    public function testDoesReflectionPropertyHaveClassAnnotationAnnotationWorks(
        bool $expected,
        ReflectionProperty $reflectionProperty,
        string $className,
    ): void {
        $entityFormatter = new EntityFormatter();
        $this->assertSame(
            $expected,
            $entityFormatter->doesReflectionPropertyHaveClassAnnotationAnnotation($reflectionProperty, $className),
        );
    }

    /**
     * @return array<array{bool, ReflectionProperty, class-string}>
     */
    public function dataProvider_testDoesReflectionPropertyHaveClassAnnotationAnnotationWorks(): array
    {
        return [
            [
                true,
                (static function () {
                    $entity = new EntityUsingAnnotations();
                    $reflectionObject = new ReflectionObject($entity);
                    $reflectionProperty = $reflectionObject->getProperty('id');

                    return $reflectionProperty;
                })(),
                ORM\Id::class,
            ],
            [
                false,
                (static function () {
                    $entity = new EntityUsingAttributes();
                    $reflectionObject = new ReflectionObject($entity);
                    $reflectionProperty = $reflectionObject->getProperty('id');

                    return $reflectionProperty;
                })(),
                ORM\Id::class,
            ],
        ];
    }


    /**
     * @dataProvider dataProvider_testIsHandlingWorks
     */
    public function testIsHandlingWorks(bool $expected, object $object): void
    {
        $entityFormatter = new EntityFormatter();
        $this->assertSame($expected, $entityFormatter->isHandling($object));
    }

    /**
     * @return array<array{bool, object}>
     */
    public function dataProvider_testIsHandlingWorks(): array
    {
        return [
            [
                false,
                new stdClass,
            ],
            [
                true,
                new EntityUsingAnnotations,
            ],
            [
                true,
                new EntityUsingAttributes,
            ],
        ];
    }
}