<?php

declare(strict_types=1);

namespace Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\AnnotationReader;
use Eboreum\Caster\Abstraction\Formatter\AbstractObjectFormatter;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;
use Eboreum\Caster\Caster;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\Formatter\Object_\DebugIdentifierAttributeInterfaceFormatter;
use ReflectionObject;
use ReflectionProperty;

/**
 * {@inheritDoc}
 *
 * Will always print ORM\Id attributes/annotations, and – if the entity implements DebugIdentifierAttributeInterface –
 * it will also print properties with the attribute DebugIdentifier.
 *
 * Does not require an entity manager to work.
 */
class EntityFormatter extends AbstractObjectFormatter
{
    protected bool $isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet = false;

    /**
     * {@inheritDoc}
     */
    public function format(CasterInterface $caster, object $entity): ?string
    {
        if (false === $this->isHandling($entity)) {
            return null;
        }

        $reflectionObject = new ReflectionObject($entity);
        $idPropertiesAsFormattedStrings = [];
        $reflectionObjectCurrent = $reflectionObject;
        $idCountTotal = 0;
        $idCountNotSet = 0;

        do {
            foreach ($reflectionObjectCurrent->getProperties() as $reflectionProperty) {
                if (array_key_exists($reflectionProperty->getName(), $idPropertiesAsFormattedStrings)) {
                    continue;
                }

                if (
                    $this->doesReflectionPropertyHaveAttribute($reflectionProperty, ORM\Id::class)
                    || $this->doesReflectionPropertyHaveClassAnnotationAnnotation($reflectionProperty, ORM\Id::class)
                ) {
                    $idCountTotal++;

                    if (false === $reflectionProperty->isInitialized($entity)) {
                        $idCountNotSet++;
                    } else {
                        $reflectionProperty->setAccessible(true);

                        if (null === $reflectionProperty->getValue($entity)) {
                            $idCountNotSet++;
                        }
                    }

                    $idPropertiesAsFormattedStrings[$reflectionProperty->getName()] = $this->makeSegment(
                        $caster,
                        $entity,
                        $reflectionObject,
                        $reflectionProperty,
                    );
                }
            }

            $reflectionObjectCurrent = $reflectionObjectCurrent->getParentClass();
        } while ($reflectionObjectCurrent);

        $debugIdentifierPropertiesAsFormattedStrings = [];

        if ($entity instanceof DebugIdentifierAttributeInterface) {
            $isRenderingDebugIdentifier = (
                false === $this->isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet
                || (
                    0 === $idCountTotal
                    || $idCountNotSet > 0
                )
            );

            if ($isRenderingDebugIdentifier) {
                $debugIdentifierAttributeInterfaceFormatter = new DebugIdentifierAttributeInterfaceFormatter();
                $map = $debugIdentifierAttributeInterfaceFormatter->getPropertyNameToReflectionProperties(
                    $reflectionObject
                );

                foreach ($map as $propertyName => $reflectionProperties) {
                    if (array_key_exists($propertyName, $idPropertiesAsFormattedStrings)) {
                        continue;
                    }

                    foreach ($reflectionProperties as $reflectionProperty) {
                        $debugIdentifierPropertiesAsFormattedStrings[$propertyName] = $this->makeSegment(
                            $caster,
                            $entity,
                            $reflectionObject,
                            $reflectionProperty,
                        );
                    }
                }
            }
        }

        $propertiesNormalized = array_merge(
            array_values($idPropertiesAsFormattedStrings),
            array_values($debugIdentifierPropertiesAsFormattedStrings),
        );

        $str = sprintf(
            '%s {%s}',
            Caster::makeNormalizedClassName($reflectionObject),
            implode(', ', $propertiesNormalized),
        );

        return $str;
    }

    /**
     * Returns a clone.
     */
    public function withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(
        bool $isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet
    ): static {
        $clone = clone $this;
        $clone->isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet = $isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet;

        return $clone;
    }

    /**
     * @param class-string $className
     */
    public function doesReflectionPropertyHaveAttribute(
        ReflectionProperty $reflectionProperty,
        string $className,
    ): bool {
        $reflectionAttributes = $reflectionProperty->getAttributes($className);

        return (bool)$reflectionAttributes;
    }

    /**
     * @param class-string $className
     */
    public function doesReflectionPropertyHaveClassAnnotationAnnotation(
        ReflectionProperty $reflectionProperty,
        string $className,
    ): bool {
        $annotationReader = new AnnotationReader();
        $annotations = $annotationReader->getPropertyAnnotations($reflectionProperty);

        foreach ($annotations as $annotation) {
            if (is_a($annotation, $className)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isHandling(object $entity): bool
    {
        $reflectionObject = new ReflectionObject($entity);
        $reflectionAttributes = $reflectionObject->getAttributes(ORM\Entity::class);

        if ($reflectionAttributes) {
            return true;
        }

        $annotationReader = new AnnotationReader();
        $annotations = $annotationReader->getClassAnnotations($reflectionObject);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof ORM\Entity) {
                return true;
            }
        }

        return false;
    }

    public function isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(): bool
    {
        return $this->isRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet;
    }

    protected function makeSegment(
        CasterInterface $caster,
        object $entity,
        ReflectionObject $reflectionObject,
        ReflectionProperty $reflectionProperty
    ): string {
        $reflectionProperty->setAccessible(true);

        $segment = sprintf(
            '$%s = %s',
            $reflectionProperty->getName(),
            (
                $reflectionProperty->isInitialized($entity)
                ? $caster->castTyped($reflectionProperty->getValue($entity))
                : '(uninitialized)'
            ),
        );

        $hasClassPrefix = (
            $reflectionProperty->getDeclaringClass()->getName() !== $reflectionObject->getName()
        );

        if ($hasClassPrefix) {
            $segment = sprintf(
                '%s%s%s',
                Caster::makeNormalizedClassName($reflectionProperty->getDeclaringClass()),
                (
                    $reflectionProperty->isStatic()
                    ? '::'
                    : '->'
                ),
                $segment,
            );
        }

        return $segment;
    }
}
