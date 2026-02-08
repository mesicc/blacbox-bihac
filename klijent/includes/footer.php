</div>
        </main>
    </div>
    
    <script>
        /* ------------------------------------------------------------------ */
        /* -------------------- KLIJENT MOBILNI MENI ------------------------ */
        /* -------------------------------------------Kemal Mesic------------ */
        
        // Elementi
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.querySelector('.klijent-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const navLinks = document.querySelectorAll('.nav-link');
        
        // Funkcija za otvaranje menija
        function openSidebar() {
            if (sidebar) {
                sidebar.classList.add('show');
            }
            if (overlay) {
                overlay.classList.add('active');
            }
            document.body.style.overflow = 'hidden';
        }
        
        // Funkcija za zatvaranje menija
        function closeSidebar() {
            if (sidebar) {
                sidebar.classList.remove('show');
            }
            if (overlay) {
                overlay.classList.remove('active');
            }
            document.body.style.overflow = '';
        }
        
        // Event listener za hamburger dugme
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar && sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }
        
        // Event listener za overlay - zatvori meni na klik
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
        
        // Zatvori meni kada se klikne na link (samo na mobilnim uređajima)
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
        
        // Zatvori meni na resize ako je prelazak na desktop
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            }, 250);
        });
        
        // Zatvori meni na ESC tipku
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
                closeSidebar();
            }
        });
        
        // Spriječi scroll na body kada je meni otvoren
        if (sidebar) {
            sidebar.addEventListener('touchmove', function(e) {
                e.stopPropagation();
            });
        }
        
        // Auto-hide alerts nakon 5 sekundi
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s ease';
                setTimeout(function() { 
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }, 5000);
        });
        
        // iOS viewport fix (spriječi "bounce" efekat)
        if (/iPhone|iPad|iPod/.test(navigator.userAgent)) {
            document.addEventListener('touchmove', function(e) {
                if (e.target.closest('.klijent-sidebar') || e.target.closest('.table-container')) {
                    return; // Dozvoli scroll samo unutar sidebara i tabela
                }
                if (e.touches.length > 1) {
                    e.preventDefault();
                }
            }, { passive: false });
        }
        
        // Smooth scroll za touch uređaje
        if ('ontouchstart' in window) {
            document.querySelectorAll('.table-container').forEach(function(container) {
                let isScrolling = false;
                let startX = 0;
                let scrollLeft = 0;
                
                container.addEventListener('touchstart', function(e) {
                    isScrolling = true;
                    startX = e.touches[0].pageX - container.offsetLeft;
                    scrollLeft = container.scrollLeft;
                });
                
                container.addEventListener('touchmove', function(e) {
                    if (!isScrolling) return;
                    e.preventDefault();
                    const x = e.touches[0].pageX - container.offsetLeft;
                    const walk = (x - startX) * 2;
                    container.scrollLeft = scrollLeft - walk;
                });
                
                container.addEventListener('touchend', function() {
                    isScrolling = false;
                });
            });
        }
    </script>
</body>
</html>