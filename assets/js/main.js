/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Ana JavaScript dosyası
 */

// DOM yüklendiğinde çalışacak fonksiyonlar
document.addEventListener('DOMContentLoaded', function() {
    // Loading spinner'ı gizle
    hideLoadingSpinner();
    
    // AOS animasyonlarını başlat
    initAOS();
    
    // Header scroll efektini başlat
    initHeaderScroll();
    
    // Back to top butonunu başlat
    initBackToTop();
    
    // Smooth scroll'u başlat
    initSmoothScroll();
    
    // Portfolio filtrelerini başlat
    initPortfolioFilters();
    
    // Gallery lightbox'ını başlat
    initLightbox();
    
    // Form validasyonlarını başlat
    initFormValidation();
    
    // Counter animasyonlarını başlat
    initCounters();
    
    // Testimonial slider'ını başlat
    initTestimonialSlider();
    
    // Parallax efektlerini başlat
    initParallax();
    
    // Typing animasyonunu başlat
    initTypingAnimation();
});

// Loading spinner'ı gizle
function hideLoadingSpinner() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        setTimeout(() => {
            spinner.classList.add('hidden');
            setTimeout(() => {
                spinner.style.display = 'none';
            }, 300);
        }, 1000);
    }
}

// AOS animasyonlarını başlat
function initAOS() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    }
}

// Header scroll efekti
function initHeaderScroll() {
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }
}

// Back to top butonu
function initBackToTop() {
    const backToTopBtn = document.getElementById('back-to-top');
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Smooth scroll
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Portfolio filtreleri
function initPortfolioFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Aktif buton sınıfını güncelle
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Portfolio öğelerini filtrele
            portfolioItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 100);
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
}

// Lightbox başlat
function initLightbox() {
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': 'Resim %1 / %2'
        });
    }
}

// Form validasyonu
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
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

// Counter animasyonları
function initCounters() {
    const counters = document.querySelectorAll('.stat-number');
    const options = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current).toLocaleString();
                }, 16);
                
                observer.unobserve(counter);
            }
        });
    }, options);
    
    counters.forEach(counter => {
        observer.observe(counter);
    });
}

// Testimonial slider
function initTestimonialSlider() {
    const testimonials = document.querySelectorAll('.testimonial-card');
    if (testimonials.length > 1) {
        let currentIndex = 0;
        
        function showTestimonial(index) {
            testimonials.forEach((testimonial, i) => {
                testimonial.style.display = i === index ? 'block' : 'none';
            });
        }
        
        function nextTestimonial() {
            currentIndex = (currentIndex + 1) % testimonials.length;
            showTestimonial(currentIndex);
        }
        
        // İlk testimonial'ı göster
        showTestimonial(0);
        
        // Otomatik geçiş
        setInterval(nextTestimonial, 5000);
    }
}

// Parallax efektleri
function initParallax() {
    const parallaxElements = document.querySelectorAll('.parallax');
    
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        
        parallaxElements.forEach(element => {
            const speed = element.getAttribute('data-speed') || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    });
}

// Typing animasyonu
function initTypingAnimation() {
    const typingElement = document.querySelector('.typing-animation');
    if (typingElement) {
        const text = typingElement.getAttribute('data-text');
        let index = 0;
        
        function typeText() {
            if (index < text.length) {
                typingElement.textContent += text.charAt(index);
                index++;
                setTimeout(typeText, 100);
            }
        }
        
        // Intersection Observer ile görünür olduğunda başlat
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    typeText();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(typingElement);
    }
}

// Utility fonksiyonlar
const Utils = {
    // Debounce fonksiyonu
    debounce: function(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    },
    
    // Throttle fonksiyonu
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },
    
    // Element görünürlük kontrolü
    isElementInViewport: function(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    },
    
    // Smooth scroll to element
    scrollToElement: function(element, offset = 0) {
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    },
    
    // Format number
    formatNumber: function(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    
    // Generate random number
    randomNumber: function(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    
    // Check if mobile device
    isMobile: function() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    },
    
    // Add class with delay
    addClassWithDelay: function(element, className, delay) {
        setTimeout(() => {
            element.classList.add(className);
        }, delay);
    },
    
    // Remove class with delay
    removeClassWithDelay: function(element, className, delay) {
        setTimeout(() => {
            element.classList.remove(className);
        }, delay);
    }
};

// Event listeners
window.addEventListener('resize', Utils.debounce(function() {
    // Responsive işlemler
    if (typeof AOS !== 'undefined') {
        AOS.refresh();
    }
}, 250));

window.addEventListener('scroll', Utils.throttle(function() {
    // Scroll işlemleri
}, 16));

// Global fonksiyonlar
window.BonusBoss = {
    Utils: Utils,
    
    // Alert göster
    showAlert: function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.insertBefore(alertDiv, document.body.firstChild);
        
        // 5 saniye sonra otomatik kapat
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    },
    
    // Loading göster
    showLoading: function() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'loading-overlay';
        loadingDiv.innerHTML = `
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    },
    
    // Loading gizle
    hideLoading: function() {
        const loadingDiv = document.querySelector('.loading-overlay');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    },
    
    // Modal göster
    showModal: function(title, content) {
        const modalHtml = `
            <div class="modal fade" id="bonusBossModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${content}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('bonusBossModal'));
        modal.show();
        
        // Modal kapandığında DOM'dan kaldır
        document.getElementById('bonusBossModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }
};

// Console log
console.log('🎰 BonusBoss Casino Yayıncısı Portföy Sitesi yüklendi!');
console.log('👨‍💻 Yazılımcı: BERAT K');
console.log('🚀 Site başarıyla çalışıyor...');