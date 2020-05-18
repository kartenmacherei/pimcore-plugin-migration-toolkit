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
| 0.3.0 | Class Migration           | `> 6.6.x` | no |
| 0.x.0 | Doktype Migration         | `> 6.6.x` | no |
| 0.x.0 | static routes             | `> 6.6.x` | no |
| 0.x.0 | ...fill list...           | `> 6.6.x` | no |

## Usage Migration Helpers (WIP)

For all migrations extend them from the class ```AbstractAdvancedPimcoreMigration```.

### System Settings
Example:
```
$systemSettingsMigrationHelper = $this->getSystemSettingsMigrationHelper();
$systemSettingsMigrationHelper->setAdminColor('#000000');
```
### Language Settings
Example:
``` 
$languageSystemSettingsMigrationHelper = $this->getLanguageSystemSettingsMigrationHelper();
$languageSystemSettingsMigrationHelper->setDefaultLanguageInAdminInterface('de');
$languageSystemSettingsMigrationHelper->addLanguageWithFallback('de', 'en');
$languageSystemSettingsMigrationHelper->setDefaultLanguage('de');
```

### Migration Data
If a migration needs data it needs to be located in the following folder:
```/project/app/Migrations/data/<classname-of-the-migration>```