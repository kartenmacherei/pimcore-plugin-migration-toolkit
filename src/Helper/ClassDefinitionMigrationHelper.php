<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Exception;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;

class ClassDefinitionMigrationHelper extends AbstractMigrationHelper
{
    /**
     * @param string $className
     * @param string $pathToJsonConfig
     *
     * @throws InvalidSettingException
     */
    public function createOrUpdate(string $className, string $pathToJsonConfig)
    {
        if (!file_exists($pathToJsonConfig)) {
            $message = sprintf(
                'The Class Definition "%s" could not be created, because the json file "%s" does not exist.',
                $className,
                $pathToJsonConfig
            );
            throw new InvalidSettingException($message);
        }

        $class = ClassDefinition::getByName($className);
        if (empty($class)) {
            $class = $this->create($className);
        }

        $configJson = file_get_contents($pathToJsonConfig);
        Service::importClassDefinitionFromJson($class, $configJson, true);

        $this->clearCache();
    }

    /**
     * @param string $className
     *
     * @return ClassDefinition
     *
     * @throws InvalidSettingException
     */
    private function create(string $className): ClassDefinition
    {
        try {
            $values = [
                'name'      => $className,
                'userOwner' => 0,
                'id'        => mb_strtolower($className)
            ];

            $class = ClassDefinition::create($values);
            $class->save();

            return $class;
        } catch (Exception $exception) {
            $message = sprintf(
                'Class Definition "%s" could not be created.',
                $className
            );
            throw new InvalidSettingException(
                $message,
                0,
                $exception
            );
        }
    }

    public function delete(string $className): void
    {
        $classDefinition = ClassDefinition::getByName($className);

        if (empty($classDefinition)) {
            $message = sprintf('Class Definition with name "%s" can not be deleted, because it does not exist.', $className);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $classDefinition->delete();
    }
}
