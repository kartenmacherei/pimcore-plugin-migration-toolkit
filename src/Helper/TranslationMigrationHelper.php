<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Exception;
use Pimcore\Model\Translation;

class TranslationMigrationHelper extends AbstractMigrationHelper
{
    public function addTranslations(array $translations, string $domain = 'messages'): void
    {
        foreach ($translations as $key => $languages) {
            try {
                $trans = Translation::getByKey($key, $domain);
                if (empty($trans)) {
                    $trans = new Translation();
                    $trans->setKey($key);
                    $trans->setDomain($domain);
                }
                foreach ($languages as $language => $translation) {
                    $trans->addTranslation($language, $translation);
                }
                $trans->save();
            } catch (Exception) {
                // nothing to do
            }
        }
    }

    public function removeTranslationsByKey(array $keys, string $domain = 'messages'): void
    {
        foreach ($keys as $key) {
            try {
                $trans = Translation::getByKey($key, $domain);
                $trans?->delete();
            } catch (Exception) {
                // nothing to do
            }
        }
    }
}
