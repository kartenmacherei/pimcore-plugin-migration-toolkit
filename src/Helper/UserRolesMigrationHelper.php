<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\User\Role;
use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Pimcore\Model\User\Workspace\DataObject as WorkspaceDataObject;
use Pimcore\Model\User\Workspace\Document as WorkspaceDocument;
use Pimcore\Model\User\Workspace\Asset as WorkspaceAsset;

class UserRolesMigrationHelper extends AbstractMigrationHelper
{
    /**
     * @param string $name
     * @param array $permissions see database table users_permission_definitions
     * @param array $docTypes
     * @param array $classes
     * @param array $viewWebsiteTranslations
     * @param array $editWebsiteTranslations
     * @param array $perspectives
     * @param int $parentId
     *
     * @throws InvalidSettingException
     */
    public function create(
        string $name,
        array $permissions = [],
        array $docTypes = [],
        array $classes = [],
        array $viewWebsiteTranslations = [],
        array $editWebsiteTranslations = [],
        array $perspectives = [],
        int $parentId = 0
    ): void {

        $role = Role::getByName($name);

        if ($role) {
            $message = sprintf('Not creating User Role with name "%s". User Role with this name already exists.', $name);
            throw new InvalidSettingException($message);
        }

        $role = new Role();
        $role->setParentId($parentId);
        $role->setName($name);
        $role->setPermissions($permissions);
        $role->setDocTypes($docTypes);
        $role->setClasses($classes);
        $role->setPerspectives($perspectives);

        $this->addSharedTranslationSettings($role, $viewWebsiteTranslations, $editWebsiteTranslations);

        $role->save();

        $this->clearCache();
    }

    public function addWorkspaceDataObject(
        string $roleName,
        string $path,
        bool $list = false,
        bool $view = false,
        bool $save = false,
        bool $publish = false,
        bool $unpublish = false,
        bool $delete = false,
        bool $rename = false,
        bool $create = false,
        bool $settings = false,
        bool $versions = false,
        bool $properties = false
    ): void {
        $role = Role::getByName($roleName);

        if (empty($role)) {
            $message = sprintf(
                'Not adding WorkspaceDataObject to User Role with name "%s", because User Role does not exists.',
                $roleName
            );
            throw new InvalidSettingException($message);
        }

        $dataObject = DataObject::getByPath($path);

        if (empty($dataObject)) {
            $message = sprintf(
                'Not adding WorkspaceDataObject to User Role with name "%s", because the object path "%s" does not exists.',
                $roleName,
                $path
            );
            throw new InvalidSettingException($message);
        }

        $workspaceDataObject = new WorkspaceDataObject();
        $workspaceDataObject->setUserId($role->getId());
        $workspaceDataObject->setCid($dataObject->getId());
        $workspaceDataObject->setCpath($dataObject->getPath());
        $workspaceDataObject->setList($list);
        $workspaceDataObject->setView($view);
        $workspaceDataObject->setSave($save);
        $workspaceDataObject->setPublish($publish);
        $workspaceDataObject->setUnpublish($unpublish);
        $workspaceDataObject->setDelete($delete);
        $workspaceDataObject->setRename($rename);
        $workspaceDataObject->setCreate($create);
        $workspaceDataObject->setSettings($settings);
        $workspaceDataObject->setVersions($versions);
        $workspaceDataObject->setProperties($properties);
        $workspaceDataObject->save();
    }

    public function addWorkspaceDocument(
        string $roleName,
        string $path,
        bool $list = false,
        bool $view = false,
        bool $save = false,
        bool $publish = false,
        bool $unpublish = false,
        bool $delete = false,
        bool $rename = false,
        bool $create = false,
        bool $settings = false,
        bool $versions = false,
        bool $properties = false
    ): void {
        $role = Role::getByName($roleName);

        if (empty($role)) {
            $message = sprintf(
                'Not adding WorkspaceDataDocument to User Role with name "%s", because User Role does not exists.',
                $roleName
            );
            throw new InvalidSettingException($message);
        }

        $document = Document::getByPath($path);

        if (empty($document)) {
            $message = sprintf(
                'Not adding WorkspaceDataDocument to User Role with name "%s", because the document path "%s" does not exists.',
                $roleName,
                $path
            );
            throw new InvalidSettingException($message);
        }

        $workspaceDocument = new WorkspaceDocument();
        $workspaceDocument->setUserId($role->getId());
        $workspaceDocument->setCid($document->getId());
        $workspaceDocument->setCpath($document->getPath());
        $workspaceDocument->setList($list);
        $workspaceDocument->setView($view);
        $workspaceDocument->setSave($save);
        $workspaceDocument->setPublish($publish);
        $workspaceDocument->setUnpublish($unpublish);
        $workspaceDocument->setDelete($delete);
        $workspaceDocument->setRename($rename);
        $workspaceDocument->setCreate($create);
        $workspaceDocument->setSettings($settings);
        $workspaceDocument->setVersions($versions);
        $workspaceDocument->setProperties($properties);
        $workspaceDocument->save();
    }

    public function addWorkspaceAsset(
        string $roleName,
        string $path,
        bool $list = false,
        bool $view = false,
        bool $publish = false,
        bool $delete = false,
        bool $rename = false,
        bool $create = false,
        bool $settings = false,
        bool $versions = false,
        bool $properties = false
    ): void {

        $role = Role::getByName($roleName);

        if (empty($role)) {
            $message = sprintf(
                'Not adding WorkspaceDataAsset to User Role with name "%s", because User Role does not exists.',
                $roleName
            );
            throw new InvalidSettingException($message);
        }

        $asset = Asset::getByPath($path);

        if (empty($asset)) {
            $message = sprintf(
                'Not adding WorkspaceDataAsset to User Role with name "%s", because the asset path "%s" does not exists.',
                $roleName,
                $path
            );
            throw new InvalidSettingException($message);
        }

        $workspaceAsset = new WorkspaceAsset();
        $workspaceAsset->setUserId($role->getId());
        $workspaceAsset->setCid($asset->getId());
        $workspaceAsset->setCpath($asset->getPath());
        $workspaceAsset->setList($list);
        $workspaceAsset->setView($view);
        $workspaceAsset->setPublish($publish);
        $workspaceAsset->setDelete($delete);
        $workspaceAsset->setRename($rename);
        $workspaceAsset->setCreate($create);
        $workspaceAsset->setSettings($settings);
        $workspaceAsset->setVersions($versions);
        $workspaceAsset->setProperties($properties);
        $workspaceAsset->save();
    }

    public function delete(string $name): void
    {
        $role = Role::getByName($name);

        if (empty($role)) {
            $message = sprintf('User Role with name "%s" can not be deleted, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $role->delete();
    }

    private function addSharedTranslationSettings(
        Role &$role,
        array $viewWebsiteTranslations,
        array $editWebsiteTranslations
    ): void {
        $messageTemplate = 'The language "%s" is not a valid language and cannot be set for user roles.';

        foreach ($viewWebsiteTranslations as $key => $language) {
            if (!$this->isLanguageValid($language)) {
                $message = sprintf(
                    $messageTemplate,
                    $language
                );
                $this->getOutput()->writeMessage($message);
                unset($viewWebsiteTranslations[$key]);
            }
        }

        foreach ($editWebsiteTranslations as $key => $language) {
            if (!$this->isLanguageValid($language)) {
                $message = sprintf(
                    $messageTemplate,
                    $language
                );
                $this->getOutput()->writeMessage($message);
                unset($editWebsiteTranslations[$key]);
            }
        }

        $role->setWebsiteTranslationLanguagesView($viewWebsiteTranslations);
        $role->setWebsiteTranslationLanguagesEdit($editWebsiteTranslations);
    }
}
