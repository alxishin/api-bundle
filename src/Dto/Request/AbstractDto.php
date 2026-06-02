<?php

declare(strict_types=1);

namespace ApiBundle\Dto\Request;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Attribute\Ignore;

class AbstractDto
{
    private function getInitializedProperties(): array
    {
        $reflection = new \ReflectionClass($this);
        $initializedProperties = [];

        foreach ($reflection->getProperties() as $property) {

            if ($property->isInitialized($this)) {
                $initializedProperties[] = $property->getName();
            }
        }

        return $initializedProperties;
    }

    #[Ignore]
    final public function mapDtoToEntity(object $entity, array $config = []): void
    {
        $pa = new PropertyAccessor();
        /**
         * С помощью рефлексии получаем все свойства и автоматически сопоставляем существующие поля через setters.
         */
        foreach ($this->getInitializedProperties() as $fieldName) {
            if (isset($config[$fieldName]) && is_callable($config[$fieldName])) {
                $config[$fieldName]($pa->getValue($this, $fieldName), $entity);
                continue;
            }

            $pa->setValue($entity, $fieldName, $pa->getValue($this, $fieldName));
        }
    }
}
