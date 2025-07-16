/**
 * Main JavaScript - BERAT K - R10 Portfolio
 * Sayaç animasyonları ve diğer interaktif özellikler
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 BERAT K - R10 Portfolio loaded!');
    
    // Theme toggle - İLK ÖNCE
    initThemeToggle();
    
    // Sayaç animasyonunu başlat
    initCounterAnimation();
    
    // Scroll animasyonları
    initScrollAnimations();
    
    // Diğer interaktif özellikler
    initOtherFeatures();
});

/**
 * THEME TOGGLE SİSTEMİ
 */
function initThemeToggle() {
    console.log('🌙 Theme toggle initializing...');
    
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const body = document.body;
    
    if (!themeToggle || !themeIcon) {
        console.error('❌ Theme toggle elements not found!');
        return;
    }
    
    console.log('✅ Theme toggle elements found');
    
    // Kullanıcının tercihini localStorage'dan al
    const defaultTheme = body.getAttribute('data-default-theme') || 'dark';
    const savedTheme = localStorage.getItem('theme') || defaultTheme;
    
    console.log('🎯 Saved theme:', savedTheme);
    
    // Başlangıç temasını uygula
    if (savedTheme === 'light') {
        body.classList.remove('dark-theme');
        body.classList.add('light-theme');
        themeIcon.className = 'fas fa-sun';
        themeToggle.title = 'Karanlık Tema';
    } else {
        body.classList.remove('light-theme');
        body.classList.add('dark-theme');
        themeIcon.className = 'fas fa-moon';
        themeToggle.title = 'Aydınlık Tema';
    }
    
    // Toggle butonuna tıklama eventi
    themeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('🔄 Theme toggle clicked!');
        toggleTheme();
    });
    
    console.log('✅ Theme toggle initialized successfully');
}

function toggleTheme() {
    console.log('🔄 Toggling theme...');
    
    const body = document.body;
    const themeIcon = document.getElementById('themeIcon');
    const themeToggle = document.getElementById('themeToggle');
    
    if (!themeIcon || !themeToggle) {
        console.error('❌ Theme elements not found in toggle function');
        return;
    }
    
    if (body.classList.contains('dark-theme')) {
        // Dark'tan Light'a geç
        console.log('🌞 Switching to light theme');
        body.classList.remove('dark-theme');
        body.classList.add('light-theme');
        themeIcon.className = 'fas fa-sun';
        themeToggle.title = 'Karanlık Tema';
        localStorage.setItem('theme', 'light');
        
        // Smooth transition ekle
        themeToggle.style.transform = 'rotate(180deg)';
        setTimeout(() => {
            themeToggle.style.transform = 'rotate(0deg)';
        }, 300);
        
    } else {
        // Light'tan Dark'a geç
        console.log('🌙 Switching to dark theme');
        body.classList.remove('light-theme');
        body.classList.add('dark-theme');
        themeIcon.className = 'fas fa-moon';
        themeToggle.title = 'Aydınlık Tema';
        localStorage.setItem('theme', 'dark');
        
        // Smooth transition ekle
        themeToggle.style.transform = 'rotate(-180deg)';
        setTimeout(() => {
            themeToggle.style.transform = 'rotate(0deg)';
        }, 300);
    }
    
    // Navbar background'unu güncelle
    updateNavbarTheme();
    
    console.log('✅ Theme toggled successfully');
}

function updateNavbarTheme() {
    const navbar = document.querySelector('.navbar');
    const isScrolled = window.pageYOffset > 50;
    
    if (isScrolled) {
        if (document.body.classList.contains('light-theme')) {
            navbar.style.background = 'rgba(248, 249, 250, 0.95)';
        } else {
            navbar.style.background = 'rgba(22, 27, 34, 0.95)';
        }
        navbar.style.backdropFilter = 'blur(10px)';
    }
}

/**
 * SAYAÇ ANİMASYONU - Ana fonksiyon
 */
function initCounterAnimation() {
    const counters = document.querySelectorAll('.stat-number');
    
    if (counters.length === 0) {
        console.log('⚠️ Sayaç elemanları bulunamadı');
        return;
    }

    console.log(`📊 ${counters.length} adet sayaç bulundu`);

    // Intersection Observer ile sayaçları gözlemle
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target); // Bir kez çalıştır
            }
        });
    }, observerOptions);

    // Her sayacı gözlemle
    counters.forEach(counter => {
        observer.observe(counter);
    });
}

/**
 * TEK SAYAÇ ANİMASYONU
 */
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-count'));
    
    if (isNaN(target) || target === 0) {
        console.log('⚠️ Geçersiz sayaç değeri:', element.getAttribute('data-count'));
        element.textContent = element.getAttribute('data-count') + '+';
        return;
    }

    console.log(`🎯 Sayaç animasyonu başlatılıyor: ${target}`);
    
    let current = 0;
    const increment = target / 100; // 100 adımda tamamla
    const duration = 2000; // 2 saniye
    const stepTime = duration / 100;

    const timer = setInterval(() => {
        current += increment;
        
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        element.textContent = Math.floor(current) + '+';
    }, stepTime);

    // Element'e animasyon sınıfı ekle
    element.classList.add('counter-animated');
}

/**
 * SCROLL ANİMASYONLARI
 */
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    if (animatedElements.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    });

    animatedElements.forEach(el => observer.observe(el));
}

/**
 * DİĞER İNTERAKTİF ÖZELLİKLER
 */
function initOtherFeatures() {
    // Scroll to top butonu
    initScrollToTop();
    
    // Navbar scroll efekti
    initNavbarScrollEffect();
    
    // Form validasyonları
    initFormValidations();
}

/**
 * SCROLL TO TOP
 */
function initScrollToTop() {
    const scrollBtn = document.querySelector('.scroll-to-top');
    
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollBtn.style.display = 'block';
            } else {
                scrollBtn.style.display = 'none';
            }
        });
    }
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

/**
 * NAVBAR SCROLL EFEKTİ
 */
function initNavbarScrollEffect() {
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
                updateNavbarTheme(); // Theme'e göre güncelle
            } else {
                navbar.classList.remove('scrolled');
                navbar.style.background = '';
                navbar.style.backdropFilter = '';
            }
        });
    }
}

/**
 * FORM VALİDASYONLARI
 */
function initFormValidations() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

/**
 * SAYAÇLARI MANUEL BAŞLAT (Debug için)
 */
function forceStartCounters() {
    console.log('🔧 Sayaçlar manuel olarak başlatılıyor...');
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        animateCounter(counter);
    });
}

/**
 * DEBUG FONKSİYONLARI
 */
function debugCounters() {
    const counters = document.querySelectorAll('.stat-number');
    console.log('🔍 Sayaç Debug Bilgileri:');
    
    counters.forEach((counter, index) => {
        const dataCount = counter.getAttribute('data-count');
        const text = counter.textContent;
        console.log(`Sayaç ${index + 1}: data-count="${dataCount}", text="${text}"`);
    });
}

// Global fonksiyonlar (konsol erişimi için)
window.forceStartCounters = forceStartCounters;
window.debugCounters = debugCounters;

console.log('✅ Main.js yüklendi - Sayaçlar ve tema toggle hazır!');