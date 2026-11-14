// Inisialisasi AOS
AOS.init({ duration: 800, once: true });

// Universal ScrollSpy & Navigation Logic
document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.navbar');
    const sections = document.querySelectorAll('section[id], header[id]');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    // JALANKAN LOGIKA NAVIGASI HANYA JIKA NAVBAR ADA (Halaman Publik)
    if (navbar) {
        function highlightMenu() {
            let scrollY = window.pageYOffset;

            sections.forEach(section => {
                const sectionHeight = section.offsetHeight;
                const sectionTop = section.offsetTop - 170;
                const sectionId = section.getAttribute('id');
                
                const targetLink = document.querySelector(`.navbar-nav a[href*="#${sectionId}"]`);

                if (targetLink) {
                    if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        targetLink.classList.add('active');
                    }
                }
            });

            // Navbar Scroll Style
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        }

        window.addEventListener('scroll', highlightMenu);
        highlightMenu(); // Jalankan sekali saat load
    }
});
