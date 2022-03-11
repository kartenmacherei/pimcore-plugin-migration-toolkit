<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Exception;
use Pimcore;
use Pimcore\Model\DataObject\ClassDefinition\CustomLayout;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CustomLayoutMigrationHelper extends AbstractMigrationHelper
{
    protected string $dataFolder;

    public function __construct(string $dataFolder)
    {
        $this->dataFolder = $dataFolder;
    }

    /**
     * @throws InvalidSettingException
     * @throws Exception
     */
    public function createOrUpdate(string $layoutName, string $classId, string $pathToJsonConfig)
    {
        if (!file_exists($pathToJsonConfig)) {
            $message = sprintf(
                'The Custom Layout "%s" for classId "%s" could not be created, because the json file "%s" does not exist.',
                $layoutName,
                $classId,
                $pathToJsonConfig
            );

            throw new InvalidSettingException($message);
        }

        $customLayout = CustomLayout::getByNameAndClassId($layoutName, $classId);
        if (empty($customLayout)) {
            $customLayout = $this->create($layoutName, $classId);
        }

        try {
            $configJson = $this->decodeJson(file_get_contents($pathToJsonConfig));
            $layoutDefinition = Service::generateLayoutTreeFromArray(
                $configJson['layoutDefinitions'],
                true
            );
            $customLayout->setLayoutDefinitions($layoutDefinition);
            $customLayout->setDescription($configJson['description']);
            $customLayout->setDefault($configJson['default']);
            $customLayout->save();
        } catch (Exception $exception) {
            $message = sprintf(
                'Custom Layout "%s" for classId "%s" could not be saved.',
                $layoutName,
                $classId
            );

            throw new InvalidSettingException(
                $message,
                0,
                $exception
            );
        }

        $this->clearCache();
    }

    /**
     * @throws InvalidSettingException
     */
    private function create(string $layoutName, string $classId): CustomLayout
    {
        try {
            $customLayout = new CustomLayout();
            $customLayout->setId(mb_strtolower($classId . $layoutName));
            $customLayout->setName($layoutName);
            $customLayout->setClassId($classId);
            $customLayout->save();

            return $customLayout;
        } catch (Exception $exception) {
            $message = sprintf(
                'Custom Layout "%s" for classId "%s" could not be created.',
                $layoutName,
                $classId
            );

            throw new InvalidSettingException(
                $message,
                0,
                $exception
            );
        }
    }

    /**
     * @throws Exception
     */
    public function delete(string $layoutName, string $classId)
    {
        $customLayout = CustomLayout::getByNameAndClassId($layoutName, $classId);

        if (empty($customLayout)) {
            $message = sprintf(
                'Custom Layout with name "%s" for classId "%s" can not be deleted, because it does not exist.',
                $layoutName,
                $classId
            );
            $this->getOutput()->writeMessage($message);

            return;
        }

        $customLayout->delete();
    }

    /**
     * Decodes a JSON string into an array/object
     *
     * @param mixed $json The data to be decoded
     * @param bool  $associative Whether to decode into associative array or object
     * @param array $context Context to pass to serializer when using serializer component
     * @param bool  $useAdminSerializer
     *
     * @return array
     */
    protected function decodeJson(
        $json,
        $associative = true,
        array $context = [],
        bool $useAdminSerializer = true
    ): array {
        $container = Pimcore::getKernel()->getContainer();

        if ($useAdminSerializer) {
            $serializer = $container->get('pimcore_admin.serializer');
        } else {
            $serializer = $container->get('serializer');
        }

        if ($associative) {
            $context['json_decode_associative'] = true;
        }

        /** @var SerializerInterface|DecoderInterface $serializer */
        return $serializer->decode($json, 'json', $context);
    }

    public function getJsonDefinitionPathForUpMigration(string $layoutName, string $classId): string
    {
        return $this->getJsonFileNameFor($layoutName, $classId, self::UP);
    }

    public function getJsonDefinitionPathForDownMigration(string $layoutName, string $classId): string
    {
        return $this->getJsonFileNameFor($layoutName, $classId, self::DOWN);
    }

    private function getJsonFileNameFor(string $layoutName, string $classId, string $direction): string
    {
        $dataFolder = $direction === self::DOWN ? $this->dataFolder . '/down/' : $this->dataFolder . '/';
        $dataFolder .= $classId . '/custom_definition_' . $layoutName . '_export.json';

        return $dataFolder;
    }
}
