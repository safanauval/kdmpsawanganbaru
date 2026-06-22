// Navbar change on scroll
const navbar = document.getElementById("mainHeader");
const hero = document.querySelector(".hero-fullscreen");

function handleNavbarScroll() {
    if (!hero) return;
    const heroBottom = hero.offsetTop + hero.offsetHeight;
    // Jika scroll melewati hero (atau lebih dari 50px), beri class scrolled
    if (
        window.scrollY > 50 ||
        window.scrollY + window.innerHeight > heroBottom
    ) {
        navbar.classList.add("scrolled");
    } else {
        navbar.classList.remove("scrolled");
    }
}

window.addEventListener("scroll", handleNavbarScroll);
window.addEventListener("resize", handleNavbarScroll);
handleNavbarScroll(); // panggil awal

// Intersection Observer untuk efek reveal
const revealElements = document.querySelectorAll(".reveal");
const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("revealed");
            }
        });
    },
    {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px",
    },
);
revealElements.forEach((el) => observer.observe(el));
if (!window.IntersectionObserver) {
    revealElements.forEach((el) => el.classList.add("revealed"));
}
