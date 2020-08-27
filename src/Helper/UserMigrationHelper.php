<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Model\User;

class UserMigrationHelper extends AbstractMigrationHelper
{
    /**
     * @param string $name
     * @param string $surname
     * @param string $email
     * @param bool   $isAdmin
     * @param bool   $isActive
     *
     * @return User
     */
    public function create(string $name, string $surname, string $email, bool $isAdmin, bool $isActive = true): User
    {
        $user = User::getByName($this->getLoginName($name, $surname));
        if ($user) {
            $this->getOutput()->writeMessage('User already exists, skipping ...');

            return $user;
        }

        $user = User::create(
            [
                'parentId' => 0,
                'name' => $this->getLoginName($name, $surname),
                'password' => md5(uniqid()),
                'email' => trim($email),
                'firstname' => trim($name),
                'lastname' => trim($surname),
                'active' => $isActive,
            ]
        );
        $user->setAdmin($isAdmin);
        $user->save();

        return $user;
    }

    /**
     * @param string $name
     * @param string $surname
     */
    public function delete(string $name, string $surname): void
    {
        $user = User::getByName($this->getLoginName($name, $surname));
        if (!$user) {
            $this->getOutput()->writeMessage('User does not exist, skipping ...');

            return;
        }

        $user->delete();
    }

    /**
     * @param string $name
     * @param string $surname
     *
     * @return string
     */
    private function getLoginName(string $name, string $surname): string
    {
        return strtolower($name) . '.' . strtolower(str_replace([' '], ['-'], $surname));
    }
}
