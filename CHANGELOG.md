## 3.0.4

### Added
- New composer package added: `google/apiclient`.
- Added .php-version file for some packages until updated to php 8.
- PostgreSQL database support has been added.
- Comment Service added.
- jsonabort() for abort() alternative

### Changed
- PHP 8 Support.
- Docker Compose file updated.
- ReflectionMethod `getClass` deprecated. Change `getClass` to `getType`.
- ExceptionHandler deprecated for Symfony/ErrorHandler.
- Replaced `App::exception()->setHandler` with `App::exception()->setExceptionHandler` for _Symfony ErrorHandler Component_.

### Updated
- Composer packages updated: `composer/composer`, `doctrine/dbal`, `maximebf/debugbar`, `phpunit/phpunit`.
- Vue and UIkit last version updated.
- System/Scripts.php changed #265 for Symfony 5.1.
- Fixed the problem of dark mode suddenly disappearing. The data will now be stored in the user's data.

### Removed
- Gulp packages deprecated. 
- Eslint and deprecated for new pattern.
- Symfony/debug deprecated.
- Mobiledetect/mobiledetectlib deprecated.
