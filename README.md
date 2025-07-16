# BERAT K - R10 Portfolio Website

🚀 **Profesyonel Yazılımcı Portföy Sitesi**

Modern, responsive ve tamamen yönetilebilir bir portföy web sitesi. BERAT K - R10 tarafından tasarlanan bu sistem, yazılımcılar için özel olarak geliştirilmiştir.

## ✨ Özellikler

### 🎨 Frontend Özellikleri
- **Modern Koyu Tema** - Profesyonel görünüm
- **Responsive Tasarım** - Tüm cihazlarda mükemmel görünüm
- **Gradient Animasyonlar** - Etkileyici görsel efektler
- **Smooth Scroll** - Akıcı sayfa geçişleri
- **Interactive Elements** - Kullanıcı dostu arayüz
- **SEO Optimize** - Arama motorları için optimize

### ⚙️ Backend Özellikleri
- **Admin Paneli** - Tam yönetim kontrolü
- **İçerik Yönetimi** - Tüm içerikleri yönetebilme
- **Dosya Yükleme** - Resim ve döküman yükleme
- **Contact Form** - Mesaj alma sistemi
- **Güvenlik** - Güvenli giriş sistemi
- **Veritabanı** - MySQL destekli

### 📱 Sayfa Yapısı
- **Ana Sayfa** - Hero section, istatistikler, son projeler
- **Hakkımda** - Kişisel bilgiler, yetenekler, deneyim
- **Hizmetlerim** - Sunulan hizmetler
- **Çalışmalarım** - Projeler ve portföy
- **Ürünler** - Hazır ürünler showcase
- **İletişim** - Contact form ve bilgiler

## 🛠 Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache/Nginx web sunucusu
- cPanel hosting desteği (opsiyonel)

### Otomatik Kurulum
1. Tüm dosyaları web sunucunuza yükleyin
2. Web tarayıcınızda `yoursite.com/install.php` adresine gidin
3. Kurulum sihirbazını takip edin:
   - Veritabanı bilgilerini girin
   - Admin hesabı oluşturun
   - Site ayarlarını yapın
4. Kurulum tamamlandıktan sonra `install.php` dosyasını silin

### Manuel Kurulum
1. `config/database.php` dosyasını düzenleyin
2. Veritabanını manuel olarak oluşturun (SQL dosyası install.php içinde)
3. Admin kullanıcısını manuel olarak ekleyin

## 🎯 Kullanım

### Admin Paneli Erişimi
- URL: `yoursite.com/admin/login.php`
- Varsayılan kullanıcı: Kurulum sırasında belirlenir

### Ana Özellikler

#### 📊 Dashboard
- Site istatistikleri
- Son projeler
- Yeni mesajlar
- Hızlı erişim linkleri

#### 🎨 İçerik Yönetimi
- **Projeler**: Portfolyo projelerini ekle/düzenle
- **Hizmetler**: Sunulan hizmetleri yönet
- **Ürünler**: Demo linkli ürünleri yönet
- **Mesajlar**: Gelen mesajları görüntüle

#### ⚙️ Site Ayarları
- Genel site bilgileri
- SEO ayarları
- İletişim bilgileri
- Sosyal medya linkleri
- Logo ve favicon yükleme

## 📁 Dosya Yapısı

```
├── admin/                  # Admin paneli
│   ├── includes/          # Admin header/footer
│   ├── index.php         # Dashboard
│   ├── login.php         # Giriş sayfası
│   ├── projects.php      # Proje yönetimi
│   ├── services.php      # Hizmet yönetimi
│   ├── products.php      # Ürün yönetimi
│   ├── messages.php      # Mesaj yönetimi
│   ├── settings.php      # Site ayarları
│   └── logout.php        # Çıkış
├── assets/                # Statik dosyalar
│   ├── css/              # Stil dosyaları
│   ├── js/               # JavaScript dosyaları
│   └── img/              # Resim dosyaları
├── config/                # Yapılandırma
│   └── database.php      # Veritabanı ayarları
├── includes/              # Ortak dosyalar
│   ├── header.php        # Site header
│   ├── footer.php        # Site footer
│   └── functions.php     # Yardımcı fonksiyonlar
├── uploads/               # Yüklenen dosyalar
├── index.php             # Ana sayfa
├── about.php             # Hakkımda sayfası
├── services.php          # Hizmetler sayfası
├── portfolio.php         # Çalışmalar sayfası
├── products.php          # Ürünler sayfası
├── contact.php           # İletişim sayfası
└── install.php           # Kurulum scripti
```

## 🎨 Özelleştirme

### Tema Renkleri
CSS değişkenlerini düzenleyerek renkleri özelleştirebilirsiniz:

```css
:root {
    --primary-color: #6c5ce7;
    --secondary-color: #fd79a8;
    --dark-bg: #0d1117;
    --dark-card: #161b22;
    --dark-border: #21262d;
}
```

### Logo ve Favicon
Admin panelinden `Ayarlar` > `Site Bilgileri` bölümünden yükleyebilirsiniz.

### İçerik Düzenleme
Tüm içerikler admin panelinden düzenlenebilir:
- Site başlığı ve açıklaması
- Ana sayfa hero metni
- İstatistik sayıları
- Hakkımda metni
- İletişim bilgileri

## 🔧 Teknik Detaylar

### Teknolojiler
- **Backend**: PHP 7.4+, MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6.4
- **Fonts**: Google Fonts (Poppins)

### Güvenlik
- SQL Injection koruması
- XSS koruması
- CSRF koruması
- Password hashing
- Session güvenliği

### SEO
- Meta tags yönetimi
- Structured data
- Sitemap desteği
- Mobile-first tasarım
- Page speed optimization

## 📱 Mobil Uyumluluk

Site tamamen responsive tasarıma sahiptir:
- Mobile-first approach
- Touch-friendly interface
- Optimized images
- Fast loading times

## 🚀 Performans

### Optimizasyonlar
- Minified CSS/JS
- Image optimization
- Lazy loading
- CDN kullanımı
- Caching support

## 🤝 Destek

### Frequently Asked Questions

**S: Admin şifremi unuttum, nasıl sıfırlayabilirim?**
C: Veritabanındaki `admin_users` tablosundan şifrenizi `password_hash()` fonksiyonu ile değiştirebilirsiniz.

**S: Yeni sayfa nasıl eklerim?**
C: Mevcut sayfa yapısını takip ederek yeni PHP dosyaları oluşturabilir ve menüye ekleyebilirsiniz.

**S: Veritabanı ayarlarını nasıl değiştiririm?**
C: `config/database.php` dosyasını düzenleyerek veritabanı bağlantı bilgilerini güncelleyebilirsiniz.

### Hata Giderme

1. **Blank Page**: PHP error logs kontrol edin
2. **Database Error**: Bağlantı bilgilerini kontrol edin
3. **Permission Error**: Dosya izinlerini 755/644 yapın
4. **Upload Error**: `uploads/` klasörü izinlerini kontrol edin

## 📄 Lisans

Bu proje BERAT K - R10 tarafından geliştirilmiştir. Kişisel ve ticari kullanım için uygundur.

## 👨‍💻 Geliştirici

**BERAT K - R10**
- Profesyonel Full Stack Developer
- Modern web teknolojileri uzmanı
- 5+ yıllık deneyim

---

## 🎉 Tebrikler!

BERAT K - R10 Portfolio sisteminizi başarıyla kurdunuz! Şimdi admin paneline giriş yaparak içeriklerinizi yönetmeye başlayabilirsiniz.

**Admin Panel**: `yoursite.com/admin/login.php`

İyi kullanımlar! 🚀