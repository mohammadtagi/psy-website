function setDuration(minutes, price, activeButton) {
    const priceElements = document.querySelectorAll('.price-value');
    const durationElements = document.querySelectorAll('.duration-label');
    const buttons = document.querySelectorAll('.duration-btn');

    // افکت هنگام تغییر قیمت
    [...priceElements, ...durationElements].forEach(element => {
        element.classList.add('fade-update');
    });

    setTimeout(() => {
        priceElements.forEach(element => {
            element.textContent = price;
        });

        durationElements.forEach(element => {
            element.textContent = `مدت جلسه: ${minutes} دقیقه`;
        });

        [...priceElements, ...durationElements].forEach(element => {
            element.classList.remove('fade-update');
        });
    }, 180);

    // بازنشانی استایل تمام دکمه‌ها
    buttons.forEach(button => {
        button.classList.remove(
            'bg-[#E4BB54]',
            'text-[#123A28]',
            'shadow-md'
        );

        button.classList.add(
            'text-[#B7C6B7]',
            'hover:bg-[#285B43]',
            'hover:text-[#F6F1E7]'
        );
    });

    // فعال‌کردن دکمه انتخاب‌شده
    activeButton.classList.remove(
        'text-[#B7C6B7]',
        'hover:bg-[#285B43]',
        'hover:text-[#F6F1E7]'
    );

    activeButton.classList.add(
        'bg-[#E4BB54]',
        'text-[#123A28]',
        'shadow-md'
    );
}
