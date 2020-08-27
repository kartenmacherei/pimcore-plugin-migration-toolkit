<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Model\User;

class UserMigrationHelper extends AbstractMigrationHelper
{
    /**
     * @param string $name
     * @param string $surname
     * @param string $email
     * @param bool   $isActive
     * @param bool   $isAdmin
     */
    public function create(string $name, string $surname, string $email, bool $isAdmin, bool $isActive = true): void
    {
        $user = User::create(
            [
                'parentId' => 0,
                'name' => strtolower($name) . strtolower(str_replace([' '], ['-'], $surname)),
                'password' => md5(uniqid()),
                'email' => trim($email),
                'firstname' => trim($name),
                'lastname' => trim($surname),
                'active' => $isActive,
            ]
        );
        $user->setAdmin($isAdmin);
        $user->save();
    }
}
