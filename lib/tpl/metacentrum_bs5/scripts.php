<?php
if (!defined('DOKU_INC')) die();
?>
<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Custom JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Navigation Toggle
        const mobileNavToggle = document.getElementById('mobileNavToggle');
        const mobileNavClose = document.getElementById('mobileNavClose');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');

        function openMobileNav() {
            mobileSidebar.classList.add('show');
            mobileSidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileNav() {
            mobileSidebar.classList.remove('show');
            mobileSidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (mobileNavToggle) {
            mobileNavToggle.addEventListener('click', openMobileNav);
        }

        if (mobileNavClose) {
            mobileNavClose.addEventListener('click', closeMobileNav);
        }

        if (mobileSidebarOverlay) {
            mobileSidebarOverlay.addEventListener('click', closeMobileNav);
        }

        // Close mobile nav on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                closeMobileNav();
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
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

        // Add animation classes on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.content-section, .hero-content').forEach(el => {
            observer.observe(el);
        });

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const query = this.value.trim();
                    if (query) {
                        // Implement search functionality here
                        console.log('Searching for:', query);
                        // You can integrate with DokuWiki's search or implement custom search
                    }
                }
            });
        }
    });
</script>