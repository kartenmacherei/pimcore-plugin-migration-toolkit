<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Exception;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\Service as DocumentService;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Property\Predefined;

class DocumentMigrationHelper extends AbstractMigrationHelper
{
    // bastodo: add support for folders etc
    const TYPE_PAGE = 'page';

    /**
     * @param string $key
     * @param string $name
     * @param string $controller
     * @param int    $parentId
     *
     * @return Page
     * @throws Exception
     * @throws InvalidSettingException
     */
    public function createPageByParentId(
        string $key,
        string $name,
        string $controller,
        int $parentId
    ): Page {
        $parent = Document::getById($parentId);

        return $this->create($parent, $name, $key, $controller, self::TYPE_PAGE);
    }

    /**
     * @param string $key
     * @param string $name
     * @param string $controller
     * @param string $parentPath
     *
     * @return Page
     * @throws Exception
     * @throws InvalidSettingException
     */
    public function createPageByParentPath(
        string $key,
        string $name,
        string $controller,
        string $parentPath
    ): Page {
        $parent = Document::getByPath($parentPath);

        return $this->create($parent, $name, $key, $controller, self::TYPE_PAGE);
    }

    /**
     * @see \Pimcore\Bundle\AdminBundle\Controller\Admin\Document\DocumentController::addAction()
     *
     * @param Document|null $parent
     * @param string        $name
     * @param string        $key
     * @param string        $controller
     *
     * @return Page
     * @throws InvalidSettingException
     * @throws Exception
     */
    private function create(
        ?Document $parent,
        string $name,
        string $key,
        string $controller,
        string $type
    ): Page {
        if ($type !== self::TYPE_PAGE) {
            $message = sprintf('Unsupported type "%s".', $type);
            throw new InvalidSettingException($message);
        }

        if (empty($parent)) {
            $message = sprintf(
                'The Document "%s" (%s) could not be created, because the parent was not found.',
                $name,
                $key
            );
            throw new InvalidSettingException($message);
        }

        $intendedPath = $parent->getRealFullPath() . '/' . $key;
        if (DocumentService::pathExists($intendedPath)) {
            $message = sprintf(
                'The Document "%s" (%s) could not be created, because already exists with path "%s".',
                $name,
                $key,
                $intendedPath
            );
            throw new InvalidSettingException($message);
        }

        $createValues = [
            'userOwner' => 0,
            'userModification' => 0,
            'published' => false,
            'key' => Service::getValidKey($key, 'document'),
            'controller' => $controller,
        ];

        $page = Page::create($parent->getId(), $createValues, false);
        $page->setTitle($name);
        $page->setProperty('navigation_name', 'text', $name, false, false);
        $page->save();

        return $page;
    }

    /**
     * @param int $id
     *
     * @throws InvalidSettingException
     */
    public function deleteById(int $id): void
    {
        if ($id === 1) {
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

    /**
     * @param string $path
     *
     * @throws InvalidSettingException
     */
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

    /**
     * @param string $key
     * @param string $name
     * @param string $type
     * @param string $contentType
     * @param bool   $isInheritable
     */
    public function createOrUpdatePredefinedProperty(
        string $key,
        string $name,
        string $type,
        string $contentType,
        bool $isInheritable = true
    ): void {
        try {
            $property = Predefined::getByKey($key);
        } catch (Exception $e) {
            $property = null;
        }

        if (!$property) {
            $property = Predefined::create();
            $property->setKey($key);
        }

        $property->setName($name);
        $property->setType($type);
        $property->setCtype($contentType);
        $property->setInheritable($isInheritable);
        $property->save();
    }

    public function removePredefinedProperty(string $key): void
    {
        $property = Predefined::getByKey($key);
        if ($property) {
            $property->delete();
        }
    }
}
