<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Model\DataObject\ClassDefinition\Data as ClassDefinitionData;
use Pimcore\Model\DataObject\Classificationstore;

class ClassificationStoreMigrationHelper extends AbstractMigrationHelper
{
    public function createOrUpdateStore(
        int $id,
        string $name,
        string $description
    ): Classificationstore\StoreConfig {
        $store = Classificationstore\StoreConfig::getById($id);
        if (empty($store)) {
            $store = new Classificationstore\StoreConfig();
            $store->setId($id);
        }

        $store->setName($name);
        $store->setDescription($description);
        $store->save();

        return $store;
    }

    public function deleteStore(int $id)
    {
        $store = Classificationstore\StoreConfig::getById($id);
        if (empty($store)) {
            $message = sprintf('Store with id "%s" can not be deleted, because it does not exist.', $id);
            $this->getOutput()->writeMessage($message);

            return;
        }

        $store->delete();
    }

    public function createOrUpdateGroup(
        string $name,
        string $description,
        int $storeId
    ): Classificationstore\GroupConfig {
        $groupConfig = Classificationstore\GroupConfig::getByName($name, $storeId);
        if (empty($groupConfig)) {
            $groupConfig = new Classificationstore\GroupConfig();
            $groupConfig->setStoreId($storeId);
            $groupConfig->setName($name);
        }
        $groupConfig->setDescription($description);
        $groupConfig->save();

        return $groupConfig;
    }

    public function deleteGroup(string $name, int $storeId)
    {
        $groupConfig = Classificationstore\GroupConfig::getByName($name, $storeId);
        if (empty($groupConfig)) {
            $message = sprintf('Group with name "%s" (store %s) can not be deleted, because it does not exist.', $name, $storeId);
            $this->getOutput()->writeMessage($message);

            return;
        }

        $groupConfig->delete();
    }

    public function createOrUpdateKey(
        string $name,
        string $title,
        string $description,
        ClassDefinitionData $fieldDefinition,
        int $storeId,
        string $groupName,
    ) {
        $keyConfig = Classificationstore\KeyConfig::getByName($name, $storeId);
        if (empty($keyConfig)) {
            $keyConfig = new Classificationstore\KeyConfig();
        }

        $keyConfig->setName($name);
        $keyConfig->setTitle($title);
        $keyConfig->setDescription($description);
        $keyConfig->setEnabled(true);
        $keyConfig->setType($fieldDefinition->getFieldtype());
        $keyConfig->setDefinition(json_encode($fieldDefinition));
        $keyConfig->setStoreId($storeId);
        $keyConfig->save();

        $groupConfig = Classificationstore\GroupConfig::getByName($groupName, $storeId);
        $keyGroupRelation = new Classificationstore\KeyGroupRelation();
        $keyGroupRelation->setKeyId($keyConfig->getId());
        $keyGroupRelation->setGroupId($groupConfig->getId());
        $keyGroupRelation->save();
    }

    public function deleteKey(string $name, int $storeId)
    {
        $keyConfig = Classificationstore\KeyConfig::getByName($name, $storeId);
        if (empty($keyConfig)) {
            $message = sprintf('Key with name "%s" (store %s) can not be deleted, because it does not exist.', $name, $storeId);
            $this->getOutput()->writeMessage($message);

            return;
        }

        $keyConfig->delete();
    }
}
