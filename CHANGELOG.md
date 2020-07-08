# 1.0.19.9-dev 
## Fixed
- Debugging kaldırıldı.
- Storage upload ve new folder düzeltildi.
- Cache-Control denendi ve htaccess'e ekledi.

# 1.0.19.7-dev
## Added
- App::device() eklendi bu sayede giriş yapılan cihazı tespit edebiliriz.

# Changelog 1.0.19.6-dev

## Added
- Gravatar yerine kendi avatar kütüphanemizi ekledik. `$app['view']->avatar('isim' , ['size' => 100 , 'fontSize' => 1])`
- `User` kullanıcılarını çekmeden önce *Avatarları* için filtreleme yapabilirsiniz. Query çalıştırmadan önce `User::SetImageFilter(['size' , 'fontSize'])` parametlerini doldurmanız yeterli. Sonrasında ise `getAvatar()`'dan gelen tüm veriler verdiğiniz filtreye göre çevrilecektir.

## Fixed
- Eğer User'ın data sınıfında avatar yoksa kütüphane çalışır. `$app['user']->getAvatar()`
- Dashboard User Widget'daki resimler düzenlendi.
