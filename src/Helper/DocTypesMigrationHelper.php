<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Model\Document\DocType;
use Pimcore\Model\Document\DocType\Listing;

class DocTypesMigrationHelper extends AbstractMigrationHelper
{
    public function create(
        string $name,
        string $controller,
        string $type = 'page',
        string $action = '',
        string $bundle = '',
        string $template = '',
        int $priority = 0,
        string $group = ''
    ): void {
        $docType = $this->getDocTypeByName($name);
        if (!empty($docType)) {
            $message = sprintf('Not creating DocType with name "%s". DocType with this name already exists.', $name);
            $this->getOutput()->writeMessage($message);

            return;
        }

        $docType = new DocType();
        $docType->setName($name);
        $docType->setController($controller);
        $docType->setType($type);
        $docType->setAction($action);
        $docType->setModule($bundle);
        $docType->setTemplate($template);
        $docType->setPriority($priority);
        $docType->setGroup($group);
        $docType->save();
    }

    public function delete(string $name): void
    {
        $docType = $this->getDocTypeByName($name);
        if (empty($docType)) {
            $message = sprintf('DocType with name "%s" can not be deleted, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);

            return;
        }

        $docType->delete();
    }

    public function update(
        string $name,
        string $newName = null,
        string $controller = null,
        string $type = null,
        string $action = null,
        string $bundle = null,
        string $template = null,
        int $priority = null,
        string $group = null
    ): void {
        $docType = $this->getDocTypeByName($name);
        if (empty($docType)) {
            $message = sprintf('DocType with name "%s" can not be updated, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);

            return;
        }

        if ($newName !== null) {
            $docType->setName($newName);
        }
        if ($controller !== null) {
            $docType->setController($controller);
        }
        if ($type !== null) {
            $docType->setType($type);
        }
        if ($action !== null) {
            $docType->setAction($action);
        }
        if ($bundle !== null) {
            $docType->setModule($bundle);
        }
        if ($template !== null) {
            $docType->setTemplate($template);
        }
        if ($priority !== null) {
            $docType->setPriority($priority);
        }
        if ($group !== null) {
            $docType->setGroup($group);
        }

        $docType->save();
    }

    public function getDocTypeByName(string $name): ?DocType
    {
        $docTypes = $this->loadDocTypes();

        return isset($docTypes[$name]) ? $docTypes[$name] : null;
    }

    /**
     * @return DocType[]
     */
    private function loadDocTypes(): array
    {
        $list = new Listing();
        $list->load();

        $docTypes = [];

        /** @var DocType $docType */
        foreach ($list->getDocTypes() as $docType) {
            $docTypes[$docType->getName()] = $docType;
        }

        return $docTypes;
    }
}
