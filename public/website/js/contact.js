document.querySelectorAll(".faq-button").forEach((button) => {
    button.addEventListener("click", () => {
        const item = button.closest(".faq-item");
        const isOpen = item.classList.contains("is-open");

        document.querySelectorAll(".faq-item.is-open").forEach((openItem) => {
            if (openItem !== item) {
                openItem.classList.remove("is-open");

                const openButton = openItem.querySelector(".faq-button");
                const openIcon = openItem.querySelector(".faq-icon");

                openButton.setAttribute("aria-expanded", "false");
                openIcon.textContent = "+";
            }
        });

        const shouldOpen = !isOpen;

        item.classList.toggle("is-open", shouldOpen);
        button.setAttribute("aria-expanded", String(shouldOpen));

        const icon = button.querySelector(".faq-icon");

        if (icon) {
            icon.textContent = shouldOpen ? "−" : "+";
        }
    });
});
