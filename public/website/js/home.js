document.addEventListener("DOMContentLoaded", function () {
    const sections = document.querySelectorAll(
        "#home, " +
        "#services, " +
        'section[aria-label="آمار تجربه و جلسات"], ' +
        'section[aria-labelledby="process-title"]'
    );

    if (!sections.length) {
        return;
    }

    const showSection = (section) => {
        section.classList.add("is-visible");
    };

    const showVisibleSections = () => {
        const viewportHeight = window.innerHeight;

        sections.forEach((section) => {
            const rect = section.getBoundingClientRect();

            const isVisible =
                rect.top < viewportHeight * 0.85 &&
                rect.bottom > viewportHeight * 0.15;

            if (isVisible) {
                showSection(section);
            }
        });
    };

    if (!("IntersectionObserver" in window)) {
        sections.forEach(showSection);
        return;
    }

    const observer = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    showSection(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.15,
            rootMargin: "0px 0px -80px 0px"
        }
    );

    sections.forEach((section) => {
        observer.observe(section);
    });

    /*
     * در Full Screen ارتفاع viewport تغییر می‌کند.
     * این بررسی فقط کلاس را اضافه می‌کند و هیچ‌وقت آن را حذف نمی‌کند.
     */
    let resizeTimer;

    window.addEventListener("resize", () => {
        window.clearTimeout(resizeTimer);

        resizeTimer = window.setTimeout(() => {
            showVisibleSections();
        }, 100);
    });

    document.addEventListener("fullscreenchange", () => {
        window.setTimeout(showVisibleSections, 150);
    });

    showVisibleSections();
});
