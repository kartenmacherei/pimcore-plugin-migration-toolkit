<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Element\Service;

class DocumentMigrationHelper extends AbstractMigrationHelper
{
    // bastodo: add support for folders etc
    const TYPE_PAGE = 'page';

    public function createPageByParentId(
        string $key,
        string $name,
        string $controller,
        int $parentId
    ): void {
        $parent = Document::getById($parentId);
        $this->create($parent, $name, $key, $controller, self::TYPE_PAGE);
    }

    public function createPageByParentPath(
        string $key,
        string $name,
        string $controller,
        string $parentPath
    ): void {
        $parent = Document::getByPath($parentPath);
        $this->create($parent, $name, $key, $controller, self::TYPE_PAGE);
    }

    /**
     * @param Document|null $parent
     * @param string $name
     * @param string $key
     * @param string $controller
     *
     * @see project/vendor/pimcore/pimcore/bundles/AdminBundle/Controller/Admin/Document/DocumentController.php ->addAction()
     *
     * @throws InvalidSettingException
     */
    private function create(
        ?Document $parent,
        string $name,
        string $key,
        string $controller,
        string $type
    ): void {
        if (empty($parent)) {
            $message = sprintf(
                'The Document "%s" (%s) could not be created, because the parent was not found.',
                $name,
                $key
            );
            throw new InvalidSettingException($message);
        }

        $intendedPath = $parent->getRealFullPath() . '/' . $key;
        if (Document\Service::pathExists($intendedPath)) {
            $message = sprintf(
                'The Document "%s" (%s) could not be created, because already exists with path "%s".',
                $name,
                $key,
                $intendedPath
            );
            throw new InvalidSettingException($message);
        }

        $createValues = [
            'userOwner'        => 0,
            'userModification' => 0,
            'published'        => false,
            'key'              => Service::getValidKey($key, 'document'),
            'controller'       => $controller
        ];

        if ($type === self::TYPE_PAGE) {
            $document = Page::create($parent->getId(), $createValues, false);
            $document->setTitle($name);
            $document->setProperty('navigation_name', 'text', $name, false, false);
            $document->save();
        }
    }

    public function deleteById(int $id): void
    {
        if ($id === 0) {
            throw new InvalidSettingException('You cannot delete the root document.');
        }

        $document = Document::getById($id);

        if (empty($document)) {
            $message = sprintf('Document with id "%s" can not be deleted, because it does not exist.', $id);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $document->delete();
    }

    public function deleteByPath(string $path): void
    {
        if (empty($path)) {
            throw new InvalidSettingException('Document can not be deleted, because path needs to be defined');
        }

        $document = Document::getByPath($path);

        if (empty($document)) {
            $message = sprintf('Document with path "%s" can not be deleted, because it does not exist.', $path);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $document->delete();
    }
}
