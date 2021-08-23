<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Exception;
use Pimcore\Model\Staticroute;
use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;

class StaticRoutesMigrationHelper extends AbstractMigrationHelper
{
    /**
     * @throws InvalidSettingException
     * @throws Exception
     */
    public function create(
        string $name,
        string $pattern,
        string $reverse,
        string $controller,
        ?string $action = null,
        ?string $variables = null,
        ?string $defaults = null,
        ?string $bundle = null,
        ?int $priority = null
    ): void {
        $route = Staticroute::getByName($name);
        if (!empty($route)) {
            $message = sprintf(
                'Not creating Static Route with name "%s". Static Route with this name already exists.',
                $name
            );
            throw new InvalidSettingException($message);
        }

        $route = new Staticroute();
        $route->setName($name);
        $route->setPattern($pattern);
        $route->setReverse($reverse);
        $route->setController($controller);

        if (!empty($action)) {
            $route->setAction($action);
        }
        if (!empty($variables)) {
            $route->setVariables($variables);
        }
        if (!empty($defaults)) {
            $route->setDefaults($defaults);
        }
        if (!empty($bundle)) {
            $route->setModule($bundle);
        }
        if (!empty($priority)) {
            $route->setPriority($priority);
        }

        $route->save();
    }

    /**
     * @throws Exception
     */
    public function delete(string $name): void
    {
        $route = Staticroute::getByName($name);

        if (empty($route)) {
            $message = sprintf('Static Route with name "%s" can not be deleted, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $route->delete();
    }
}
