<?php

namespace PimcorePluginMigrationToolkit\Helper;

use Pimcore\Model\User\Role;
use PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;

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
