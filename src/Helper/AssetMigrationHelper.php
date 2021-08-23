<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Exception;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\Asset\Service as AssetService;

class AssetMigrationHelper extends AbstractMigrationHelper
{
    // bastodo: add support for asset files

    /**
     * @throws InvalidSettingException
     */
    public function createFolderByParentId(string $name, int $parentId): void
    {
        $parent = Folder::getById($parentId);

        if (empty($parent)) {
            $message = sprintf(
                'The Folder "%s" could not be created, because the parent does not exist',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $intendedPath = $parent->getRealFullPath() . '/' . $name;

        $this->createFolderByPath($intendedPath);
    }

    /**
     * @throws InvalidSettingException
     */
    public function createFolderByPath(string $path): void
    {
        try {
            AssetService::createFolderByPath($path);
        } catch (Exception $exception) {
            $message = sprintf('The Folder "%s" could not be created.', $path);
            throw new InvalidSettingException($message);
        }
    }

    /**
     * @throws InvalidSettingException
     * @throws Exception
     */
    public function deleteById(int $id): void
    {
        if ($id === 1) {
            throw new InvalidSettingException('You cannot delete the root asset.');
        }

        $asset = Asset::getById($id);

        if (empty($asset)) {
            $message = sprintf('Asset with id "%s" can not be deleted, because it does not exist.', $id);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $asset->delete();
    }

    /**
     * @throws InvalidSettingException
     * @throws Exception
     */
    public function deleteByPath(string $path): void
    {
        if (empty($path)) {
            throw new InvalidSettingException('Asset can not be deleted, because path needs to be defined');
        }

        $asset = Asset::getByPath($path);

        if (empty($asset)) {
            $message = sprintf('Asset with path "%s" can not be deleted, because it does not exist.', $path);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $asset->delete();
    }
}
