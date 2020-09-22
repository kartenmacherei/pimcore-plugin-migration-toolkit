<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Model\Asset\Video\Thumbnail\Config as VideoThumbnailConfig;

class VideoThumbnailMigrationHelper extends AbstractMigrationHelper
{
    public const VIDEO_AVERAGE = 400;
    public const AUDIO_AVERAGE = 128;

    public const VIDEO_GOOD = 600;
    public const AUDIO_GOOD = 128;

    public const VIDEO_BEST = 800;
    public const AUDIO_BEST = 196;

    public function create(
        string $name,
        string $description = '',
        string $group = '',
        int $videoBitrate = self::VIDEO_GOOD,
        int $audioBitrate = self::AUDIO_GOOD
    ): VideoThumbnailConfig
    {
        $videoThumbnail = new VideoThumbnailConfig();
        $videoThumbnail->setName($name);
        $videoThumbnail->setGroup($group);
        $videoThumbnail->setDescription($description);
        $videoThumbnail->setVideoBitrate($videoBitrate);
        $videoThumbnail->setAudioBitrate($audioBitrate);
        $videoThumbnail->save();

        return $videoThumbnail;
    }

    public function delete(string $name): void
    {
        $videoThumbnail = VideoThumbnailConfig::getByName($name);
        if (empty($videoThumbnail)) {
            $message = sprintf('Thumbnail with name "%s" can not be deleted, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $videoThumbnail->doClearTempFiles(PIMCORE_TEMPORARY_DIRECTORY . '/video-thumbnails', $name);
        $videoThumbnail->delete();

        $this->clearCache();
    }
}
