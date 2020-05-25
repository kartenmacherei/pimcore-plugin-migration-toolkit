# Pimcore Plugin Migration Toolkit

## Why?
In every project we have migrations for the same things.
Like System Settings, Classes, etc.
This plugin provides you with the migration helpers and further tools.

## Versioning
| **Version** | **Function**  | **Pimcore Version** | **Implemented** |
| ----------- |:--------------|:--------------| ---------------:|
| 0.0.0 | initial Setup             |  | yes |
| 0.1.0 | System Settings Migration | `> 6.6.x` | yes |
| 0.2.0 | Language Settings Migration | `> 6.6.x` | yes |
| 0.3.0 | Website Settings Migration  | `> 6.6.x` | yes |
| 0.4.0 | User Role Migration         | `> 6.6.x` | yes |
| 0.x.0 | Class Migration           | `> 6.6.x` | no |
| 0.x.0 | Doktype Migration         | `> 6.6.x` | no |
| 0.x.0 | static routes             | `> 6.6.x` | no |
| 0.x.0 | ...fill list...           | `> 6.6.x` | no |

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
$languageSystemSettingsMigrationHelper = $this->getLanguageSystemSettingsMigrationHelper();
$languageSystemSettingsMigrationHelper->setDefaultLanguageInAdminInterface('de');
$languageSystemSettingsMigrationHelper->addLanguageWithFallback('de', 'en');
$languageSystemSettingsMigrationHelper->setDefaultLanguage('de');
```
Example: Down
```
$languageSystemSettingsMigrationHelper = $this->getLanguageSystemSettingsMigrationHelper();
$languageSystemSettingsMigrationHelper->setDefaultLanguageInAdminInterface('en');
$languageSystemSettingsMigrationHelper->removeLanguage('de');
$languageSystemSettingsMigrationHelper->setDefaultLanguage('en');
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

### Migration Data
If a migration needs data it needs to be located in the following folder:
```/project/app/Migrations/data/<classname-of-the-migration>```

## Ideas
* command: ```basilicom:migrations:migrate-in-separate-processes```
* command: ```basilicom:migrations:generate <which type of migration>```
    * types e.g:
        * general migration for extended class only
        * class migration template with folders
        * ...
* Translations, how?
    * use csv file, which will get imported by command -> krombacher
    * use translation migration -> fleurop