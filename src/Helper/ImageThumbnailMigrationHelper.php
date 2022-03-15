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
    public const TRANSFORMATION_CONTAIN = 'contain';
    public const TRANSFORMATION_COVER = 'cover';
    public const TRANSFORMATION_FRAME = 'frame';
    public const TRANSFORMATION_RESIZE = 'resize';
    public const TRANSFORMATION_SCALE_BY_HEIGHT = 'scaleByHeight';
    public const TRANSFORMATION_SCALE_BY_WIDTH = 'scaleByWidth';
    public const TRANSFORMATION_SET_BACKGROUND_COLOR = 'setBackgroundColor';

    const TRANSFORMATIONS_AVAILABLE = [
        self::TRANSFORMATION_CONTAIN,
        self::TRANSFORMATION_COVER,
        self::TRANSFORMATION_FRAME,
        self::TRANSFORMATION_RESIZE,
        self::TRANSFORMATION_SCALE_BY_HEIGHT,
        self::TRANSFORMATION_SCALE_BY_WIDTH,
        self::TRANSFORMATION_SET_BACKGROUND_COLOR,
    ];

    protected ?ThumbnailConfig $thumbnailConfig = null;

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    private function getThumbnailByName(string $name): ImageThumbnailMigrationHelper
    {
        $thumbnail = ThumbnailConfig::getByName($name);
        if (empty($thumbnail)) {
            throw new NotFoundException('Thumbnail with name "' . $name . '" does not exist.');
        }

        $this->thumbnailConfig = $thumbnail;
        return $this;
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
    ): ImageThumbnailMigrationHelper {
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

        $this->thumbnailConfig = $thumbnail;

        $this->clearCache();

        return $this;
    }

    /**
     * @throws NotFoundException
     */
    public function resetTransformations(string $name): ImageThumbnailMigrationHelper
    {
        $this->validateThumbnailConfig();
        $this->thumbnailConfig->setItems([]);
        $this->thumbnailConfig->setMedias([]);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationResize(
        int $width,
        int $height,
        ?string $mediaQuery = null
    ): ImageThumbnailMigrationHelper {
        $this->validateThumbnailConfig();

        $parameters = [
            'width'  => $width,
            'height' => $height,
        ];

        $this->thumbnailConfig->addItem(self::TRANSFORMATION_RESIZE, $parameters, $mediaQuery);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationScaleByHeight(
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): ImageThumbnailMigrationHelper {
        $this->validateThumbnailConfig();

        $parameters = [
            'height'      => $height,
            'forceResize' => $forceResize,
        ];

        $this->thumbnailConfig->addItem(self::TRANSFORMATION_SCALE_BY_HEIGHT, $parameters, $mediaQuery);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationScaleByWidth(
        int $width,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): ImageThumbnailMigrationHelper {
        $this->validateThumbnailConfig();

        $parameters = [
            'width'       => $width,
            'forceResize' => $forceResize,
        ];

        $this->thumbnailConfig->addItem(self::TRANSFORMATION_SCALE_BY_WIDTH, $parameters, $mediaQuery);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationCover(
        int $width,
        int $height,
        bool $forceResize = false,
        string $positioning = 'center',
        ?string $mediaQuery = null
    ): ImageThumbnailMigrationHelper {
        $this->validateThumbnailConfig();

        $parameters = [
            'height'      => $height,
            'width'       => $width,
            'positioning' => $positioning,
            'forceResize' => $forceResize,
        ];

        $this->thumbnailConfig->addItem(self::TRANSFORMATION_COVER, $parameters, $mediaQuery);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationContain(
        int $width,
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): ImageThumbnailMigrationHelper {
        $this->validateThumbnailConfig();

        $parameters = [
            'height'      => $height,
            'width'       => $width,
            'forceResize' => $forceResize,
        ];

        $this->thumbnailConfig->addItem(self::TRANSFORMATION_CONTAIN, $parameters, $mediaQuery);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws NotFoundException
     */
    public function addTransformationFrame(
        int $width,
        int $height,
        bool $forceResize = false,
        ?string $mediaQuery = null
    ): ImageThumbnailMigrationHelper {
        $this->validateThumbnailConfig();

        $parameters = [
            'height'      => $height,
            'width'       => $width,
            'forceResize' => $forceResize,
        ];

        $this->thumbnailConfig->addItem(self::TRANSFORMATION_FRAME, $parameters, $mediaQuery);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws MigrationToolkitException
     */
    public function addTransformationSetBackgroundColor(
        string $hexColor,
        ?string $mediaQuery = null
    ): ImageThumbnailMigrationHelper {
        $this->validateThumbnailConfig();

        if (empty($hexColor)) {
            $message = sprintf(
                'Not adding Background Color to Thumbnail with name "%s" Background Color (#hex) is not set.',
                $this->thumbnailConfig->getName()
            );

            throw new InvalidSettingException($message);
        }

        $parameters = [
            'color' => $hexColor
        ];

        $this->thumbnailConfig->addItem(self::TRANSFORMATION_SET_BACKGROUND_COLOR, $parameters, $mediaQuery);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws MigrationToolkitException
     */
    public function removeTransformation(
        string $transformationKey,
        ?string $mediaQuery
    ): ImageThumbnailMigrationHelper {
        $this->validateThumbnailConfig();

        if (!in_array($transformationKey, self::TRANSFORMATIONS_AVAILABLE)) {
            $message = sprintf(
                'Can not remove transformation "%s" from "%s", because it is empty or not supported yet.',
                $transformationKey,
                $this->thumbnailConfig->getName()
            );

            throw new InvalidSettingException($message);
        }

        $items = $this->thumbnailConfig->getItems();
        $medias = $this->thumbnailConfig->getMedias();

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
                    $this->thumbnailConfig->getName(),
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

            $this->thumbnailConfig->setMedias($medias);
        }

        $this->thumbnailConfig->setItems($items);
        $this->thumbnailConfig->setMedias($medias);
        $this->thumbnailConfig->save();

        $this->clearCache();

        return $this;
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $name): void
    {
        $this->getThumbnailByName($name);
        if (empty($this->thumbnailConfig)) {
            $message = sprintf('Thumbnail with name "%s" can not be deleted, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);

            return;
        }

        $this->thumbnailConfig->delete(true);
        $this->clearCache();
    }

    /**
     * @throws NotFoundException
     */
    protected function validateThumbnailConfig()
    {
        if (empty($this->thumbnailConfig)) {
            throw new NotFoundException('missing thumbnailConfig object.');
        }
    }
}
