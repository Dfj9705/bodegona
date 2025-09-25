import './bootstrap';

const setActive = () => {
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll(".dropdown-item");
    navLinks.forEach((link) => {
        const linkUrl = link.getAttribute("href");
        if (currentUrl === linkUrl) {
            link.classList.add("active");
            const dropdownToggle = link.closest(".dropdown").querySelector(".dropdown-toggle");
            if (dropdownToggle) {
                dropdownToggle.classList.add("active");
            }
        }
    });

}

setActive();