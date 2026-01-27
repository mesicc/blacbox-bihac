// Dark/Light Mode Toggle
const themeToggle = document.getElementById('themeToggle');
const body = document.body;

// Load saved theme preference
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'light') {
    body.classList.remove('dark-mode');
} else {
    body.classList.add('dark-mode');
}

// Toggle theme
themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    
    const isDarkMode = body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
    
    // Animate icon rotation
    const icons = themeToggle.querySelectorAll('.theme-icon');
    icons.forEach(icon => {
        icon.style.transform = isDarkMode ? 'rotate(0deg)' : 'rotate(180deg)';
    });
});

// Mobile Menu Toggle
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const navMenu = document.getElementById('navMenu');

mobileMenuToggle.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    mobileMenuToggle.classList.toggle('active');
});

// Close mobile menu when clicking a link
const navLinks = document.querySelectorAll('.nav-link');
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        mobileMenuToggle.classList.remove('active');
    });
});

// Smooth Scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            const navbarHeight = document.getElementById('navbar').offsetHeight;
            const targetPosition = targetElement.offsetTop - navbarHeight;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// Navbar scroll effect
let lastScroll = 0;
const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 10) {
        navbar.style.boxShadow = body.classList.contains('dark-mode') 
            ? '0 4px 6px -1px rgba(0, 0, 0, 0.3)' 
            : '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.boxShadow = 'none';
    }
    
    lastScroll = currentScroll;
});

// Intersection Observer for scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe sections for animation
const sections = document.querySelectorAll('.section-header, .program-card, .contact-card, .about-content, .about-image, .stat-item');

sections.forEach(section => {
    section.style.opacity = '0';
    section.style.transform = 'translateY(30px)';
    section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(section);
});

// Add hover effect for logo
const navLogo = document.querySelector('.nav-logo');
const footerLogo = document.querySelector('.footer-logo');

[navLogo, footerLogo].forEach(logo => {
    if (logo) {
        logo.addEventListener('mouseenter', () => {
            const logoBox = logo.querySelector('.logo-box');
            if (logoBox) {
                logoBox.style.animation = 'pulse 1s ease infinite';
            }
        });
        
        logo.addEventListener('mouseleave', () => {
            const logoBox = logo.querySelector('.logo-box');
            if (logoBox) {
                logoBox.style.animation = '';
            }
        });
    }
});

// Program card hover effects
const programCards = document.querySelectorAll('.program-card');

programCards.forEach(card => {
    card.addEventListener('mouseenter', () => {
        const arrow = card.querySelector('.program-arrow');
        if (arrow) {
            arrow.style.transform = 'translateX(0)';
        }
    });
    
    card.addEventListener('mouseleave', () => {
        const arrow = card.querySelector('.program-arrow');
        if (arrow) {
            arrow.style.transform = 'translateX(-10px)';
        }
    });
});

// Stats counter animation
const stats = document.querySelectorAll('.stat-number');

const animateCounter = (element) => {
    const target = element.textContent;
    const isPlus = target.includes('+');
    const number = parseInt(target.replace('+', ''));
    const duration = 2000;
    const steps = 60;
    const increment = number / steps;
    let current = 0;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= number) {
            element.textContent = isPlus ? number + '+' : number;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current) + (isPlus ? '+' : '');
        }
    }, duration / steps);
};

// Observe stats for animation
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
            animateCounter(entry.target);
            entry.target.classList.add('animated');
        }
    });
}, { threshold: 0.5 });

stats.forEach(stat => {
    statsObserver.observe(stat);
});

// Parallax effect for hero section
const hero = document.querySelector('.hero');
const heroContent = document.querySelector('.hero-content');

window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroHeight = hero.offsetHeight;
    
    if (scrolled < heroHeight) {
        heroContent.style.transform = `translateY(${scrolled * 0.5}px)`;
        heroContent.style.opacity = 1 - (scrolled / heroHeight) * 1.5;
    }
});

// Active link highlighting
const updateActiveLink = () => {
    const sections = document.querySelectorAll('section[id]');
    const navbarHeight = navbar.offsetHeight;
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - navbarHeight - 100;
        const sectionBottom = sectionTop + section.offsetHeight;
        const scrollPosition = window.pageYOffset;
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${section.id}`) {
                    link.classList.add('active');
                }
            });
        }
    });
};

window.addEventListener('scroll', updateActiveLink);

// Add ripple effect to buttons
const buttons = document.querySelectorAll('.btn');

buttons.forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Add CSS for ripple effect dynamically
const style = document.createElement('style');
style.textContent = `
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Console log for demo
console.log('BlackBox Bihac - Website Loaded Successfully!');
console.log('Theme:', body.classList.contains('dark-mode') ? 'Dark Mode' : 'Light Mode');

// ================================================================
// LIGHTBOX GALLERY
// ================================================================
const lightbox = document.getElementById('lightbox');
const lightboxImage = document.getElementById('lightboxImage');
const lightboxClose = document.getElementById('lightboxClose');
const lightboxPrev = document.getElementById('lightboxPrev');
const lightboxNext = document.getElementById('lightboxNext');
const lightboxCounter = document.getElementById('lightboxCounter');
const galleryGrid = document.getElementById('galleryGrid');
const galleryToggleBtn = document.getElementById('galleryToggleBtn');
const galleryItems = document.querySelectorAll('.gallery-item');

let currentImageIndex = 0;
let galleryImages = [];
let isGalleryExpanded = false;

// Inicijalno prikupi slike i postavi klik handlere
function initGallery() {
    galleryImages = [];
    
    galleryItems.forEach((item, index) => {
        const img = item.querySelector('img');
        if (img) {
            galleryImages.push(img.src);
        }
        
        // Klik na sliku otvara lightbox
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // Ako galerija nije prosirena i slika je skrivena, ne radi nista
            if (!isGalleryExpanded && item.classList.contains('gallery-hidden')) return;
            
            currentImageIndex = index;
            openLightbox();
        });
    });
}

// Toggle gallery - prikazi/sakrij dodatne slike
if (galleryToggleBtn) {
    galleryToggleBtn.addEventListener('click', function() {
        isGalleryExpanded = !isGalleryExpanded;
        
        if (isGalleryExpanded) {
            galleryGrid.classList.add('expanded');
            this.querySelector('span').textContent = 'Vidi manje';
        } else {
            galleryGrid.classList.remove('expanded');
            this.querySelector('span').textContent = 'Vidi vise';
            
            // Scroll do galerije ako je korisnik otisao predaleko
            const gallerySection = document.getElementById('galerija');
            if (gallerySection) {
                const rect = gallerySection.getBoundingClientRect();
                if (rect.bottom < 0) {
                    gallerySection.scrollIntoView({ behavior: 'smooth' });
                }
            }
        }
    });
}

// Otvori lightbox
function openLightbox() {
    if (!lightbox || !lightboxImage) return;
    
    const totalImages = isGalleryExpanded ? galleryImages.length : Math.min(6, galleryImages.length);
    
    lightboxImage.src = galleryImages[currentImageIndex];
    updateCounter(totalImages);
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Zatvori lightbox
function closeLightbox() {
    if (!lightbox) return;
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
}

// Prethodna slika
function prevImage() {
    const totalImages = isGalleryExpanded ? galleryImages.length : Math.min(6, galleryImages.length);
    currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
    updateLightboxImage();
}

// Sljedeca slika
function nextImage() {
    const totalImages = isGalleryExpanded ? galleryImages.length : Math.min(6, galleryImages.length);
    currentImageIndex = (currentImageIndex + 1) % totalImages;
    updateLightboxImage();
}

// Azuriraj sliku u lightboxu
function updateLightboxImage() {
    if (!lightboxImage) return;
    const totalImages = isGalleryExpanded ? galleryImages.length : Math.min(6, galleryImages.length);
    lightboxImage.style.animation = 'none';
    lightboxImage.offsetHeight;
    lightboxImage.style.animation = 'lightbox-zoom 0.3s ease';
    lightboxImage.src = galleryImages[currentImageIndex];
    updateCounter(totalImages);
}

// Azuriraj brojac
function updateCounter(total) {
    if (!lightboxCounter) return;
    lightboxCounter.textContent = `${currentImageIndex + 1} / ${total}`;
}

// Inicijaliziraj galeriju
initGallery();

// Event listeneri za lightbox kontrole
if (lightboxClose) {
    lightboxClose.addEventListener('click', closeLightbox);
}

if (lightboxPrev) {
    lightboxPrev.addEventListener('click', prevImage);
}

if (lightboxNext) {
    lightboxNext.addEventListener('click', nextImage);
}

// Zatvori na klik izvan slike
if (lightbox) {
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
}

// Keyboard navigacija
document.addEventListener('keydown', (e) => {
    if (!lightbox || !lightbox.classList.contains('active')) return;
    
    switch (e.key) {
        case 'Escape':
            closeLightbox();
            break;
        case 'ArrowLeft':
            prevImage();
            break;
        case 'ArrowRight':
            nextImage();
            break;
    }
});

// Touch/Swipe podrska za mobilne uredaje
let touchStartX = 0;
let touchEndX = 0;

if (lightbox) {
    lightbox.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    lightbox.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
}

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            nextImage();
        } else {
            prevImage();
        }
    }
}