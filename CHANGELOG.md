
# Change Log
All notable changes to this project will be documented in this file.
 
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.0.9.4-beta - 2020-09-03

### Fixed
- A description tag has been added next to og:description.

### Edit
- .gitignore added vscode

### Removed
- Categories System Deleted

## 2.0.9.3-beta - 2020-08-24
Marketplace plugin has been developed. Users will be able to install new plugins and themes using the plugin store.

### Added
- Marketplace Node

### Fixed
- Turkish translate
- Commands/Translate

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
 
## [Unreleased] - yyyy-mm-dd
 
Here we write upgrading notes for brands. It's a team effort to make them as
straightforward as possible.
 
### Added
- [PROJECTNAME-XXXX](http://tickets.projectname.com/browse/PROJECTNAME-XXXX)
  MINOR Ticket title goes here.
- [PROJECTNAME-YYYY](http://tickets.projectname.com/browse/PROJECTNAME-YYYY)
  PATCH Ticket title goes here.
 
### Changed
 
### Fixed
