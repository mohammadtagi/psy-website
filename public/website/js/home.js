(() => {
    const section = document.getElementById('client-testimonials');
    const viewport = document.getElementById('testimonials-viewport');
    const track = document.getElementById('testimonials-track');
    const cards = [...track.querySelectorAll('.testimonial-card')];
    const previousButton = document.getElementById('testimonials-prev');
    const nextButton = document.getElementById('testimonials-next');

    let currentIndex = 0;
    let autoplayTimer = null;
    let hasEntered = false;
    let isPaused = false;

    const getStep = () => {
        if (!cards[0]) return 0;

        const cardWidth = cards[0].getBoundingClientRect().width;
        const styles = window.getComputedStyle(track);
        const gap = parseFloat(styles.columnGap || styles.gap || 0);

        return cardWidth + gap;
    };

    const getVisibleCards = () => {
        const step = getStep();
        if (!step) return 1;

        return Math.max(1, Math.floor((viewport.clientWidth + 32) / step));
    };

    const getMaxIndex = () => {
        return Math.max(0, cards.length - getVisibleCards());
    };

    const updateSlider = () => {
        const maxIndex = getMaxIndex();

        currentIndex = Math.min(currentIndex, maxIndex);
        // علامت منفی حذف شد تا جهت حرکت با RTL هماهنگ شود
        track.style.transform = `translateX(${currentIndex * getStep()}px)`;

        previousButton.disabled = currentIndex === 0;
        nextButton.disabled = currentIndex === maxIndex;
    };

    const goToNext = () => {
        const maxIndex = getMaxIndex();

        if (currentIndex >= maxIndex) {
            currentIndex = 0;
        } else {
            currentIndex += 1;
        }

        updateSlider();
    };

    const goToPrevious = () => {
        const maxIndex = getMaxIndex();

        if (currentIndex <= 0) {
            currentIndex = maxIndex;
        } else {
            currentIndex -= 1;
        }

        updateSlider();
    };

    const stopAutoplay = () => {
        window.clearInterval(autoplayTimer);
        autoplayTimer = null;
    };

    const startAutoplay = () => {
        stopAutoplay();

        autoplayTimer = window.setInterval(() => {
            if (!isPaused) {
                goToNext();
            }
        }, 7000);
    };

    previousButton.addEventListener('click', () => {
        goToPrevious();
        startAutoplay();
    });

    nextButton.addEventListener('click', () => {
        goToNext();
        startAutoplay();
    });

    viewport.addEventListener('mouseenter', () => {
        isPaused = true;
    });

    viewport.addEventListener('mouseleave', () => {
        isPaused = false;
    });

    window.addEventListener('resize', updateSlider);

    // یک بار ورق‌خوردن هنگام رسیدن سکشن به محدوده دید
    const observer = new IntersectionObserver(
        (entries) => {
            const entry = entries[0];

            if (entry.isIntersecting && !hasEntered) {
                hasEntered = true;

                window.setTimeout(() => {
                    goToNext();
                    startAutoplay();
                }, 600);

                observer.disconnect();
            }
        },
        {
            threshold: 0.35
        }
    );

    observer.observe(section);
    updateSlider();
})();

document.querySelectorAll(".faq-toggle").forEach((btn) => {
    btn.addEventListener("click", () => {
        const item = btn.closest(".faq-item");
        const willOpen = !item.classList.contains("is-open");

        document.querySelectorAll(".faq-item").forEach((el) => {
            el.classList.remove("is-open");
            el.querySelector(".faq-icon").textContent = "+";
            el.querySelector(".faq-toggle").setAttribute("aria-expanded", "false");
        });

        if (willOpen) {
            item.classList.add("is-open");
            item.querySelector(".faq-icon").textContent = "−";
            btn.setAttribute("aria-expanded", "true");
        }
    });
});

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
