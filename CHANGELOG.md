All notable changes to this project will be documented in this file.
 
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## 3.0.0

### Added
- New composer package added: `google/apiclient`.
- Added .php-version file for some packages until updated to php 8.
- PostgreSQL database support has been added.
- Comment Service added.

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

### Removed
- Gulp packages deprecated. 
- Eslint and deprecated for new pattern.
- Symfony/debug deprecated.
- Mobiledetect/mobiledetectlib deprecated.


## 2.0.11.1-RC1 - 2.0.11-RC1 - 2020-10-25

### Removed
- app/console/src/Commands/SelfupdateCommand.php and app/console/src/Commands/InstallCommand.php command line on Greencheap CLI.

### Fixed
- Avatar stretch issue in admin panel has been resolved.
- Safari support.
- %site& to %site% on mail template.

### Updated
- Language file update for tr_TR.

## 2.0.10.6-RC1 - 2020-10-09

### Removed
- Symfony Yaml.

## 2.0.10.4-RC1 - 2020-09-29

### Added
- Symfony/Mime , Symfony/Yaml install.

## 2.0.10.3-RC1 - 2020-09-20

### Fixed
- Composer update.

### Removed
- Twig_Filter_Simply remove deprecated 2.12.

### Added
- PhpMatcherDumper.php for Symfony 5.1 depraceted 4.2.
- MobileDetect class for devices.

## 2.0.10-RC1 - 2020-09-13

### Edit
- Mobile menu.
- Symfony's packages version upgrade from 4.* to 5.1.

## 2.0.9.3-beta - 2020-08-24
Marketplace plugin has been developed. Users will be able to install new plugins and themes using the plugin store.

### Added
- Marketplace Node.

### Fixed
- Turkish translate.
- Commands/Translate.

## Removed
- Categories section has been removed and will be active again in future versions.

## 2.0.8.6-alpha - 2020-08-04
Son kullanıcıdan gelen bir kaç hata için hızlı güncelleme versiyonudur.

### Fixed
- Install tarafında, proje farklı bir klasöre yükleniyorsa. URL yapısı logoyu getirmiyordu. Bu durum admin panelde de yaşandı. Buna istinaden problemli olan bölgelere `$url()` ile düzeltilme yapıldı.
- GreenCheap'i ilk defa yüklediğinizde requirements'ı karşılayamadığı zamanlar olabiliyor (.htaccess dosyasının eksik olması, yazma izni vb.) bu gibi durumlarda requirements uyarı sayfasına yönlendirir. Ancak tasarımsal problem vardı, bu sorun çözüldü.
- Kategori sisteminde type olmamasına rağmen select box seçiliymiş gibi geliyor. Aynı zamanda eğer kategoriler sistemini kullanan bir eklenti yoksa. Bir kategori eklenemiyor. Bu işlem bilerek yapıldı. Kategori sistemi paketler için oluşturulmuştur. Type belirtilmediği sürece kategori açamazsınız.
- Safari tarayıcısında Ecmascript 8'den dolayı bir kaç hatalar alıyoruz (_Sadece admin panelde_). Bu yüzden safari kullanan kullanıcılar için admin panelde bir uyarı ekledik.

## 2.0.8.4-alpha - 2020-08-04
Beta sürümüne çıkabilmesi için gerekli bazı değişiklikler var. Bu sürümde bu paketlerin güncellemesi ile ilgili.

### Change
- Extensions listesinde `he` adında bir kelime hatası düzeltilmiştir.

### Added
- Varsayılan Theme-one ve Blog eklentisi paketlere import edildi.
- İlk yükleme esnasında widget, blog post ve nodelar varsayılan olarak eklendi.

### Removed
- `/storage/*` silindi `.gitignore` dosyasından
