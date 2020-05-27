<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Exception;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Objectbrick\Definition as ObjectbrickDefinition;

class ObjectbrickMigrationHelper extends AbstractMigrationHelper
{
    /**
     * @param string $key
     * @param string $pathToJsonConfig
     *
     * @throws InvalidSettingException
     */
    public function createOrUpdate(string $key, string $pathToJsonConfig): void
    {
        if (!file_exists($pathToJsonConfig)) {
            $message = sprintf(
                'The Objectbrick "%s" could not be created, because the json file "%s" does not exist.',
                $key,
                $pathToJsonConfig
            );
            throw new InvalidSettingException($message);
        }

        $objectbrick = ObjectbrickDefinition::getByKey($key);
        if (empty($objectbrick)) {
            $objectbrick = $this->create($key);
        }

        $configJson = file_get_contents($pathToJsonConfig);
        Service::importObjectBrickFromJson($objectbrick, $configJson);

        $this->clearCache();
    }

    /**
     * @param string $key
     *
     * @return ObjectbrickDefinition
     *
     * @throws InvalidSettingException
     */
    private function create(string $key): ObjectbrickDefinition
    {
        try {
            $definition = new ObjectbrickDefinition();
            $definition->setKey($key);
            $definition->save();

            return $definition;
        } catch (Exception $exception) {
            $message = sprintf(
                'Objectbrick "%s" could not be created.',
                $key
            );
            throw new InvalidSettingException(
                $message,
                0,
                $exception
            );
        }
    }

    public function delete(string $key): void
    {
        $objectbricks = ObjectbrickDefinition::getByKey($key);

        if (empty($objectbricks)) {
            $message = sprintf('Objectbrick "%s" can not be deleted, because it does not exist.', $key);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $objectbricks->delete();
    }
}
