# Pimcore Plugin Migration Toolkit

## Why?
In every project we have migrations for the same things.
Like System Settings, Classes, etc.
This plugin provides you with the migration helpers and further tools.

## Versioning
| **Version** | **Function**  | **Pimcore Version** | **Implemented** |
| ----------- |:--------------|:--------------| ---------------:|
| 0.0.0 | initial Setup                          |           | yes |
| 0.0.0 | System Settings Migration              | `> 6.6.x` | yes |
| 0.0.0 | Language Settings Migration            | `> 6.6.x` | yes |
| 0.0.0 | Website Settings Migration             | `> 6.6.x` | yes |
| 0.0.0 | User Role Migration                    | `> 6.6.x` | yes |
| 0.1.0 | Document Types Migration               | `> 6.6.x` | yes |
| 0.2.0 | Command: Migrate in separate process   | `> 6.6.x` | yes |
| 0.3.0 | Bundle Migration                       | `> 6.6.x` | yes |
| 0.x.0 | Object Class Migration                 | `> 6.6.x` | no |
| 0.x.0 | Fieldcollection Migration              | `> 6.6.x` | no |
| 0.x.0 | Object Brick Migration                 | `> 6.6.x` | no |
| 0.x.0 | Custom Layouts Migration               | `> 6.6.x` | no |
| 0.x.0 | QuantityValue Unit Migration           | `> 6.6.x` | no |
| 0.x.0 | Thumbnail Migration                    | `> 6.6.x` | no |
| 0.x.0 | Object (Folder) Migration              | `> 6.6.x` | no |
| 0.x.0 | Document (Folder) Migration            | `> 6.6.x` | no |
| 0.x.0 | Asset (Folder) Migration               | `> 6.6.x` | no |

## Commands
### Migrate in separate process
Executes the same migrations as the ```pimcore:migrations:migrate``` command,
but each one is run in a separate process, to prevent problems with PHP classes that changed during the runtime.
``` 
bin/console basilicom:migrations:migrate-in-separate-processes
```

## Usage Migration Helpers (WIP)

For all migrations extend them from the class ```AbstractAdvancedPimcoreMigration```.

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

### UserRoles
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
### Migration Data
If a migration needs data it needs to be located in the following folder:
```/project/app/Migrations/data/<classname-of-the-migration>```

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