<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Pimcore\Model\Asset\Image\Thumbnail\Config as ThumbnailConfig;

class ImageThumbnailMigrationHelper extends AbstractMigrationHelper
{
    // bastodo: add support for other transformations

    const TRANSFORMATION_CONTAIN = 'contain';
    const TRANSFORMATION_COVER = 'cover';
    const TRANSFORMATION_FRAME = 'frame';
    const TRANSFORMATION_RESIZE = 'resize';
    const TRANSFORMATION_SCALE_BY_HEIGHT = 'scaleByHeight';
    const TRANSFORMATION_SCALE_BY_WIDTH = 'scaleByWidth';
    const TRANSFORMATION_SET_BACKGROUND_COLOR = 'setBackgroundColor';

    const TRANSFORMATIONS_AVAILABLE = [
        self::TRANSFORMATION_CONTAIN,
        self::TRANSFORMATION_COVER,
        self::TRANSFORMATION_FRAME,
        self::TRANSFORMATION_RESIZE,
        self::TRANSFORMATION_SCALE_BY_HEIGHT,
        self::TRANSFORMATION_SCALE_BY_WIDTH,
        self::TRANSFORMATION_SET_BACKGROUND_COLOR,
    ];

    public function create(
        string $name,
        string $description = '',
        string $quality = '',
        string $format = ''
    ): ThumbnailConfig
    {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (!empty($thumbnail)) {
            $message = sprintf(
                'Not creating Thumbnail with name "%s". Thumbnail with this name already exists.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $thumbnail = new ThumbnailConfig();
        $thumbnail->setName($name);
        $thumbnail->setDescription($description);

        if (!empty($format)) {
            $thumbnail->setFormat($format);
        }

        if (!empty($quality)) {
            $thumbnail->setQuality($quality);
        }

        $thumbnail->save();

        $this->clearCache();

        return $thumbnail;
    }

    public function resetTransformations(string $name): void
    {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not reset transformation for Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $thumbnail->resetItems();
        $thumbnail->save();

        $this->clearCache();
    }

    public function addTransformationResize(
        string $name,
        int $width,
        int $height,
        ?string $mediaQuery = null
    ): void {

        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not add transformation for Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $parameters['width'] = $width;
        $parameters['height'] = $height;

        $thumbnail->addItem(self::TRANSFORMATION_RESIZE, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    public function addTransformationScaleByHeight(
        string $name,
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): void {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not add transformation for Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $parameters['height'] = $height;
        $parameters['forceResize'] = $forceResize;

        $thumbnail->addItem(self::TRANSFORMATION_SCALE_BY_HEIGHT, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    public function addTransformationScaleByWidth(
        string $name,
        int $width,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): void {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not add transformation for Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $parameters['width'] = $width;
        $parameters['forceResize'] = $forceResize;

        $thumbnail->addItem(self::TRANSFORMATION_SCALE_BY_WIDTH, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    public function addTransformationCover(
        string $name,
        int $width,
        int $height,
        bool $forceResize = false,
        string $positioning = 'center',
        ?string $mediaQuery = null
    ): void {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not add transformation for Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $parameters['width'] = $width;
        $parameters['height'] = $height;
        $parameters['forceResize'] = $forceResize;
        $parameters['positioning'] = $positioning;

        $thumbnail->addItem(self::TRANSFORMATION_COVER, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    public function addTransformationContain(
        string $name,
        int $width,
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): void {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not add transformation for Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $parameters['width'] = $width;
        $parameters['height'] = $height;
        $parameters['forceResize'] = $forceResize;

        $thumbnail->addItem(self::TRANSFORMATION_CONTAIN, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    public function addTransformationFrame(
        string $name,
        int $width,
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): void {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not add transformation for Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $parameters['width'] = $width;
        $parameters['height'] = $height;
        $parameters['forceResize'] = $forceResize;

        $thumbnail->addItem(self::TRANSFORMATION_FRAME, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    public function addTransformationSetBackgroundColor(
        string $name,
        string $hexColor,
        ?string $mediaQuery = null
    ): void {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not add transformation for Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        if (empty($hexColor)) {
            $message = sprintf(
                'Not adding Background Color to Thumbnail with name "%s" Background Color (#hex) is not set.',
                $thumbnail->getName()
            );
            throw new InvalidSettingException($message);
        }

        $parameters = [
            'color' => $hexColor
        ];

        $thumbnail->addItem(self::TRANSFORMATION_SET_BACKGROUND_COLOR, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    // bastodo: how to remove transformation with mediaquery?
    public function removeTransformation(string $name, string $transformationKey)
    {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            $message = sprintf(
                'Can not remove transformation from Thumbnail with name "%s", because it does not exist.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        if (empty($transformationKey)) {
            $message = sprintf(
                'Can not remove transformation from Thumbnail with name "%s", because transformation key is not set.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        if (!in_array($transformationKey, self::TRANSFORMATIONS_AVAILABLE)) {
            $message = sprintf(
                'Can not remove transformation "%s" from Thumbnail with name "%s", because this transformation is not supported yet.',
                $transformationKey,
                $name
            );
            throw new InvalidSettingException($message);
        }

        $items = $thumbnail->getItems();
        foreach ($items as $key => $item) {
            if ($item['method'] === $transformationKey) {
                unset($items[$key]);
            }
        }

        $thumbnail->setItems($items);
        $thumbnail->save();

        $this->clearCache();
    }

    public function delete(string $name): void
    {
        $thumbnail = ThumbnailConfig::getByName($name);

        if (empty($thumbnail)) {
            $message = sprintf('Thumbnail with name "%s" can not be deleted, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $thumbnail->doClearTempFiles(PIMCORE_TEMPORARY_DIRECTORY . '/image-thumbnails', $name);

        $thumbnail->delete();

        $this->clearCache();
    }
}
