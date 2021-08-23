<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Exception;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\Service as DocumentService;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Property\Predefined as PredefinedProperty;

class DocumentMigrationHelper extends AbstractMigrationHelper
{
    // bastodo: add support for folders etc
    const TYPE_PAGE = 'page';

    /**
     * @throws InvalidSettingException
     * @throws Exception
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
     * @throws InvalidSettingException
     * @throws Exception
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
     * @throws InvalidSettingException
     * @throws Exception
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

    public function createOrUpdatePredefinedProperty(
        string $key,
        string $name,
        string $description,
        string $type,
        string $contentType,
        bool $isInheritable = true
    ): PredefinedProperty {
        try {
            $property = PredefinedProperty::getByKey($key);
        } catch (Exception $e) {
            $property = null;
        }

        if (!$property) {
            $property = PredefinedProperty::create();
            $property->setKey($key);
        }

        $property->setName($name);
        $property->setDescription($description);
        $property->setType($type);
        $property->setCtype($contentType);
        $property->setInheritable($isInheritable);
        $property->save();

        return $property;
    }

    public function removePredefinedProperty(string $key): void
    {
        $property = PredefinedProperty::getByKey($key);
        if ($property) {
            $property->delete();
        }
    }
}
