# Pimcore Plugin Migration Toolkit

## Why?
In every project we have migrations for the same things.
Like System Settings, Classes, etc.
This plugin provides you with the migration helpers and further tools.

## Versioning
| **Version** | **Function**  | **Pimcore Version** | **Implemented** |
| ----------- |:--------------|:--------------| ---------------:|
| 0.0.0 | initial Setup                          |           | yes |
| 0.1.0 | System Settings Migration              | `> 6.6.x` | yes |
| 0.2.0 | Language Settings Migration            | `> 6.6.x` | yes |
| 0.3.0 | Website Settings Migration             | `> 6.6.x` | yes |
| 0.4.0 | Static Routes Migration                | `> 6.6.x` | yes |
| 0.5.0 | User Role Migration                    | `> 6.6.x` | yes |
| 0.6.0 | Document Types Migration               | `> 6.6.x` | yes |
| 1.0.0 | Refactor Naming                        | `> 6.6.x` | yes |
| 1.1.0 | Command: Migrate in separate process   | `> 6.6.x` | yes |
| 1.2.0 | Bundle Migration                       | `> 6.6.x` | yes |
| 1.3.0 | Class Definition Migration             | `> 6.6.x` | yes |
| 1.4.0 | Object Brick Migration                 | `> 6.6.x` | yes |
| 1.5.0 | Fieldcollection Migration              | `> 6.6.x` | yes |
| 1.6.0 | Custom Layouts Migration               | `> 6.6.x` | yes |
| 1.7.0 | Document Migration (Page)              | `> 6.6.x` | yes |
| 1.8.0 | Object Migration (Folder)              | `> 6.6.x` | yes |
| 1.9.0 | Asset Migration (Folder)               | `> 6.6.x` | yes |
| 1.10.0 | Image Thumbnail Migration             | `> 6.6.x` | yes |
| 1.11.0 | QuantityValue Unit Migration          | `> 6.6.x` | yes |
| 1.?.0 | User Role Workspaces Migration         | `> 6.6.x` | yes |

## Usage Migration Helpers

For all migrations extend them from the class ```AbstractAdvancedPimcoreMigration```.

### Migration Data
If a migration needs data it needs to be located in the following folder:
```/project/app/Migrations/data/<classname-of-the-migration>```


### System Settings
Example: Up
```
$systemSettingsMigrationHelper = $this->getSystemSettingsMigrationHelper();
$systemSettingsMigrationHelper->setAdminColor('#003366');
$systemSettingsMigrationHelper->setLoginColor('#003366');
$systemSettingsMigrationHelper->setInvertColorsForLoginScreen(true);
$systemSettingsMigrationHelper->setHideEditImageTab(true);
```
Example: Down
```
$systemSettingsMigrationHelper = $this->getSystemSettingsMigrationHelper();
$systemSettingsMigrationHelper->removeAdminColor();
$systemSettingsMigrationHelper->removeLoginColor();
$systemSettingsMigrationHelper->removeInvertColorsForLoginScreen();
$systemSettingsMigrationHelper->removeHideEditImageTab();
```

### Language Settings
Example: Up
``` 
$languageSettingsMigrationHelper = $this->getLanguageSettingsMigrationHelper();
$languageSettingsMigrationHelper->setDefaultLanguageInAdminInterface('de');
$languageSettingsMigrationHelper->addLanguageWithFallback('de', 'en');
$languageSettingsMigrationHelper->setDefaultLanguage('de');
```
Example: Down
```
$languageSettingsMigrationHelper = $this->getLanguageSettingsMigrationHelper();
$languageSettingsMigrationHelper->setDefaultLanguageInAdminInterface('en');
$languageSettingsMigrationHelper->removeLanguage('de');
$languageSettingsMigrationHelper->setDefaultLanguage('en');
```

### Website Settings
Example: Up
``` 
$websiteSettingsMigrationHelper = $this->getWebsiteSettingsMigrationHelper();
$websiteSettingsMigrationHelper->createOfTypeText('text', 'text hier');
$websiteSettingsMigrationHelper->createOfTypeDocument('document', 1);
$websiteSettingsMigrationHelper->createOfTypeAsset('asset', 1);
$websiteSettingsMigrationHelper->createOfTypeObject('object', 1);
$websiteSettingsMigrationHelper->createOfTypeBool('bool', false);
```
Example: Down
```
$websiteSettingsMigrationHelper = $this->getWebsiteSettingsMigrationHelper();
$websiteSettingsMigrationHelper->delete('text');
$websiteSettingsMigrationHelper->delete('document');
$websiteSettingsMigrationHelper->delete('asset');
$websiteSettingsMigrationHelper->delete('object');
$websiteSettingsMigrationHelper->delete('bool');
```

### Static Routes
Example: Up
``` 
$staticRoutesMigrationHelper = $this->getStaticRoutesMigrationHelper();
$staticRoutesMigrationHelper->create(
    'route',
    '/pattern',
    '/reverse',
    'controller',
    'action',
    'variable1,variable2',
    'default1,default2',
    'bundle',
    10
);
$staticRoutesMigrationHelper->create(
    'route1',
    '/pattern1',
    '/reverse1',
    'controller1'
);
```
Example: Down
```
$staticRoutesMigrationHelper = $this->getStaticRoutesMigrationHelper();
$staticRoutesMigrationHelper->delete('route');
$staticRoutesMigrationHelper->delete('route1');
```

### UserRoles
There is no way to remove the workspaces (dataobjects, documents or assets).

Even when deleting a user role in the pimcore backend the workspace data stays in the database.

Example: Up
``` 
$userRolesMigrationHelper = $this->getUserRolesMigrationHelper();
$userRolesMigrationHelper->create(
    'migrationRole',
    ['dashboards', 'admin_translations'],
    ['doctype'],
    ['class'],
    ['de', 'en'],
    ['de']
);
$userRolesMigrationHelper->addWorkspaceDataObject($role,$path,true,true,false,true,false,true,true,true,true,true,true);
$userRolesMigrationHelper->addWorkspaceDocument($role,$path,true,true,false,true,false,true,true,true,true,true,true);
$userRolesMigrationHelper->addWorkspaceAsset($role,$path,true,true,false,true,false,true,true,true,true);
```
Example: Down
```
$userRolesMigrationHelper = $this->getUserRolesMigrationHelper();
$userRolesMigrationHelper->delete('migrationRole');
```

### DocTypes
Example: Up
``` 
$docTypesMigrationHelper = $this->getDocTypesMigrationHelper();
$docTypesMigrationHelper->create('doktype', 'controller');
$docTypesMigrationHelper->update('doktype', 'newDoctype', 'newcontroller');
```
Example: Down
```
$docTypesMigrationHelper = $this->getDocTypesMigrationHelper();
$docTypesMigrationHelper->delete('newDoctype');
```

### Bundle
It is not possible to enable and install one bundle in one migration!

You need to make two migrations one with enable (disable) and one with install (uninstall) and then run it with the command 
[Migrate in separate process](#migrate-in-separate-process). Otherwise it would not find the newly enabled bundle for the installation.

Example: Up
``` 
$bundleMigrationHelper = $this->getBundleMigrationHelper();
$bundleMigrationHelper->enable('Basilicom\PimcorePluginMigrationToolkit\PimcorePluginMigrationToolkitBundle');
```
Example: Down
```
$bundleMigrationHelper = $this->getBundleMigrationHelper();
$bundleMigrationHelper->disable('Basilicom\PimcorePluginMigrationToolkit\PimcorePluginMigrationToolkitBundle');
```

### Class Definitions
Example: Up
``` 
$className = 'testing';
$classDefinitionMigrationHelper = $this->getClassDefinitionMigrationHelper();
$jsonPath = $classDefinitionMigrationHelper->getJsonFileNameForUp($className);
$classDefinitionMigrationHelper->createOrUpdate($className, $jsonPath);
```
Example: Down
```
$className = 'testing';
$classDefinitionMigrationHelper = $this->getClassDefinitionMigrationHelper();
$classDefinitionMigrationHelper->delete($className);
// OR
$jsonPath = $classDefinitionMigrationHelper->getJsonFileNameForDown($className);
$classDefinitionMigrationHelper->createOrUpdate($className, $jsonPath);
```

### Objectbricks
Example: Up
``` 
$objectbrickName = 'brick';
$objectbrickMigrationHelper = $this->getObjectbrickMigrationHelper();
$objectbrickMigrationHelper->createOrUpdate($objectbrickName, $this->dataFolder . '/objectbrick_' . $objectbrickName . '_export.json');
```
Example: Down
```
$objectbrickName = 'brick';
$objectbrickMigrationHelper = $this->getObjectbrickMigrationHelper();
$objectbrickMigrationHelper->delete($objectbrickName);
// OR
$objectbrickMigrationHelper->createOrUpdate($objectbrickName, $this->dataFolder . '/down/objectbrick_' . $objectbrickName . '_export.json');
```

### Fieldcollection
Example: Up
``` 
$key = 'test';
$fieldcollectionMigrationHelper = $this->getFieldcollectionMigrationHelper();
$fieldcollectionMigrationHelper->createOrUpdate($key, $this->dataFolder . '/fieldcollection_' . $key . '_export.json');
```
Example: Down
```
$key = 'test';
$fieldcollectionMigrationHelper = $this->getFieldcollectionMigrationHelper();
$fieldcollectionMigrationHelper->delete($key);
// OR
$fieldcollectionMigrationHelper->createOrUpdate($key, $this->dataFolder . '/down/fieldcollection_' . $key . '_export.json');
```

### Custom Layouts
``` 
const CUSTOM_LAYOUT = [
    'classId' => 'EF_OTCP',
    'name' => 'test'
];
``` 
Example: Up
``` 
$customLayoutMigrationHelper = $this->getCustomLayoutMigrationHelper();
$customLayoutMigrationHelper->createOrUpdate(
    self::CUSTOM_LAYOUT['name'],
    self::CUSTOM_LAYOUT['classId'],
    $this->dataFolder . '/custom_definition_' . self::CUSTOM_LAYOUT['name'] . '_export.json'
);
```
Example: Down
```
$customLayoutMigrationHelper = $this->getCustomLayoutMigrationHelper();
$customLayoutMigrationHelper->delete(
    self::CUSTOM_LAYOUT['name'],
    self::CUSTOM_LAYOUT['classId']
);
// OR
$customLayoutMigrationHelper->createOrUpdate(
    self::CUSTOM_LAYOUT['name'],
    self::CUSTOM_LAYOUT['classId'],
    $this->dataFolder . '/down/custom_definition_' . self::CUSTOM_LAYOUT['name'] . '_export.json'
);
```

### Document (Page)
``` 
const PAGE = [
    'key' => 'diga',
    'name' => 'DiGA',
    'controller' => 'Search',
    'parentPath' => '/',
];
``` 
Example: Up
``` 
$documentMigrationHelper = $this->getDocumentMigrationHelper();
$documentMigrationHelper->createPageByParentPath(
    self::PAGE['key'],
    self::PAGE['name'],
    self::PAGE['controller'],
    self::PAGE['parentPath']
);
```
Example: Down
```
$documentMigrationHelper = $this->getDocumentMigrationHelper();
$documentMigrationHelper->deleteByPath(
    self::PAGE['parentPath'].self::PAGE['key']
);
```

### Object (Folder)
Example: Up
``` 
$dataObjectMigrationHelper = $this->getDataObjectMigrationHelper();
$dataObjectMigrationHelper->createFolderByParentId('folder1', 1);
$dataObjectMigrationHelper->createFolderByPath('/folder2/subfolder');
```
Example: Down
```
$dataObjectMigrationHelper = $this->getDataObjectMigrationHelper();
$dataObjectMigrationHelper->deleteById(2);
$dataObjectMigrationHelper->deleteByPath('/folder2');
```

### Asset (Folder)
Example: Up
``` 
$assetMigrationHelper = $this->getAssetMigrationHelper();
$assetMigrationHelper->createFolderByParentId('name', 1);
$assetMigrationHelper->createFolderByPath('/asset1/subasset');
```
Example: Down
```
$assetMigrationHelper = $this->getAssetMigrationHelper();
$assetMigrationHelper->deleteById(2);
$assetMigrationHelper->deleteByPath('/asset1');
```

### Image Thumbnail
Example: Up
``` 
$name = 'thumbnail';
$imageThumbnailMigrationHelper = $this->getImageThumbnailMigrationHelper();
$imageThumbnailMigrationHelper->create($name, 'description');
$imageThumbnailMigrationHelper->addTransformationFrame($name, 40, 50, true);
$imageThumbnailMigrationHelper->removeTransformation($name, ImageThumbnailMigrationHelper::TRANSFORMATION_SET_BACKGROUND_COLOR);
$imageThumbnailMigrationHelper->addTransformationSetBackgroundColor($name, '#888888');
```
Example: Down
```
$name = 'thumbnail';
$imageThumbnailMigrationHelper = $this->getImageThumbnailMigrationHelper();
$imageThumbnailMigrationHelper->delete($name);
```


### QuantityValue Unit
Example: Up
``` 
$quantityValueUnitMigrationHelper = $this->getQuantityValueUnitMigrationHelper();
$quantityValueUnitMigrationHelper->createOrUpdate('abr', 'Long Abbreviation');
```
Example: Down
```
$quantityValueUnitMigrationHelper = $this->getQuantityValueUnitMigrationHelper();
$quantityValueUnitMigrationHelper->delete('abr');
```

## Commands
### Migrate in separate process
Executes the same migrations as the ```pimcore:migrations:migrate``` command,
but each one is run in a separate process, to prevent problems with PHP classes that changed during the runtime.
``` 
bin/console basilicom:migrations:migrate-in-separate-processes
```

## Ideas
* command: ```basilicom:migrations:generate <which type of migration>```
    * types e.g:
        * general migration for extended class only
        * class migration template with folders
        * ...
* enhance command: ```basilicom:migrations:migrate-in-separate-processes```
    * to also revert ```prev``` or ```<versionnumber>```
* Translations, how?
    * use csv file, which will get imported by command -> krombacher
    * use translation migration -> fleurop
* Video Thumbnail Migration Helper