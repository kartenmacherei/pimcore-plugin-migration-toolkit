<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Basilicom\PimcorePluginMigrationToolkit\Exceptions\MigrationToolkitException;
use Basilicom\PimcorePluginMigrationToolkit\Exceptions\NotFoundException;
use Exception;
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

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    private function getThumbnailByName(string $name): ThumbnailConfig
    {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            throw new NotFoundException('Thumbnail with name "' . $name . '" does not exist.');
        }

        return $thumbnail;
    }

    /**
     * @throws InvalidSettingException
     * @throws Exception
     */
    public function create(
        string $name,
        string $description = '',
        string $quality = '',
        string $format = ''
    ): ThumbnailConfig {
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

    /**
     * @throws NotFoundException
     */
    public function resetTransformations(string $name): void
    {
        $thumbnail = $this->getThumbnailByName($name);
        $thumbnail->setItems([]);
        $thumbnail->setMedias([]);
        $thumbnail->save();

        $this->clearCache();
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationResize(
        string $name,
        int $width,
        int $height,
        ?string $mediaQuery = null
    ): void {
        $parameters = [
            'width' => $width,
            'height' => $height,
        ];

        $thumbnail = $this->getThumbnailByName($name);
        $thumbnail->addItem(self::TRANSFORMATION_RESIZE, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationScaleByHeight(
        string $name,
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): void {
        $parameters = [
            'height' => $height,
            'forceResize' => $forceResize,
        ];

        $thumbnail = $this->getThumbnailByName($name);
        $thumbnail->addItem(self::TRANSFORMATION_SCALE_BY_HEIGHT, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationScaleByWidth(
        string $name,
        int $width,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): void {
        $parameters = [
            'width' => $width,
            'forceResize' => $forceResize,
        ];

        $thumbnail = $this->getThumbnailByName($name);
        $thumbnail->addItem(self::TRANSFORMATION_SCALE_BY_WIDTH, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationCover(
        string $name,
        int $width,
        int $height,
        bool $forceResize = false,
        string $positioning = 'center',
        ?string $mediaQuery = null
    ): void {
        $parameters = [
            'height' => $height,
            'width' => $width,
            'positioning' => $positioning,
            'forceResize' => $forceResize,
        ];

        $thumbnail = $this->getThumbnailByName($name);
        $thumbnail->addItem(self::TRANSFORMATION_COVER, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationContain(
        string $name,
        int $width,
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): void {
        $parameters = [
            'height' => $height,
            'width' => $width,
            'forceResize' => $forceResize,
        ];

        $thumbnail = $this->getThumbnailByName($name);
        $thumbnail->addItem(self::TRANSFORMATION_CONTAIN, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationFrame(
        string $name,
        int $width,
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): void {
        $parameters = [
            'height' => $height,
            'width' => $width,
            'forceResize' => $forceResize,
        ];

        $thumbnail = $this->getThumbnailByName($name);
        $thumbnail->addItem(self::TRANSFORMATION_FRAME, $parameters, $mediaQuery);
        $thumbnail->save();

        $this->clearCache();
    }

    /**
     * @throws MigrationToolkitException
     */
    public function addTransformationSetBackgroundColor(
        string $name,
        string $hexColor,
        ?string $mediaQuery = null
    ): void {
        $thumbnail = $this->getThumbnailByName($name);

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

    /**
     * @throws MigrationToolkitException
     */
    public function removeTransformation(string $name, string $transformationKey, ?string $mediaQuery)
    {
        if (!in_array($transformationKey, self::TRANSFORMATIONS_AVAILABLE)) {
            $message = sprintf(
                'Can not remove transformation "%s" from "%s", because it is empty or not supported yet.',
                $transformationKey,
                $name
            );

            throw new InvalidSettingException($message);
        }

        $thumbnail = $this->getThumbnailByName($name);
        $items = $thumbnail->getItems();
        $medias = $thumbnail->getMedias();

        if (empty($mediaQuery)) {
            foreach ($items as $key => $item) {
                if ($item['method'] === $transformationKey) {
                    unset($items[$key]);
                }
            }
        } else {
            if (!isset($medias[$mediaQuery])) {
                $message = sprintf(
                    'Media query "%s" is not registered in "%s". ' . PHP_EOL . 'Available: ' . PHP_EOL . '%s',
                    $mediaQuery,
                    $name,
                    implode(PHP_EOL, array_keys($medias))
                );

                throw new InvalidSettingException($message);
            }

            foreach ($medias[$mediaQuery] as $key => $item) {
                if ($item['method'] === $transformationKey) {
                    unset($medias[$mediaQuery][$key]);
                }
            }

            if (empty($medias[$mediaQuery])) {
                unset($medias[$mediaQuery]);
            }

            $thumbnail->setMedias($medias);
        }

        $thumbnail->setItems($items);
        $thumbnail->setMedias($medias);
        $thumbnail->save();

        $this->clearCache();
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $name): void
    {
        $thumbnail = $this->getThumbnailByName($name);
        if (empty($thumbnail)) {
            $message = sprintf('Thumbnail with name "%s" can not be deleted, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);

            return;
        }

        $thumbnail->delete(true);
        $this->clearCache();
    }
}
