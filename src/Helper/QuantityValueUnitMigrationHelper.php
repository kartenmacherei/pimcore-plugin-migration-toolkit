<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Model\DataObject\QuantityValue\Unit as QuantityValueUnit;

class QuantityValueUnitMigrationHelper extends AbstractMigrationHelper
{
    public function createOrUpdate(
        string $abbreviation,
        string $longname,
        QuantityValueUnit $baseunit = null,
        float $factor = null,
        float $conversionOffset = null,
        string $converter = ''
    ) : void {
        $unit = QuantityValueUnit::getByAbbreviation($abbreviation);

        if (empty($unit)) {
            $unit = new QuantityValueUnit();
        }

        $unit->setAbbreviation($abbreviation);
        $unit->setLongname($longname);

        if (!empty($baseunit)) {
            $unit->setBaseunit($baseunit);
        }
        if (!empty($factor)) {
            $unit->setFactor($factor);
        }
        if (!empty($conversionOffset)) {
            $unit->setConversionOffset($conversionOffset);
        }
        if (!empty($converter)) {
            $unit->setConverter($converter);
        }

        $unit->save();
    }

    public function delete(string $abbreviation): void
    {
        $unit = QuantityValueUnit::getByAbbreviation($abbreviation);

        if (empty($unit)) {
            $message = sprintf('Quantity Value Unit with name "%s" can not be deleted, because it does not exist.', $abbreviation);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $unit->delete();
    }
}
