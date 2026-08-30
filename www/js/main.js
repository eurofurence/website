// introduced by Xenor in 2023: make the background page unscrollable while the nav overlay is open
// document.getElementById("nav-state").addEventListener('change', function() {
//     document.getElementsByTagName("body")[0].style = "overflow: " + (this.checked? "hidden" : "auto") + ";"
// })

/* Consent Required - Click to Allow External Contents */
document.querySelectorAll('.consent-cover').forEach(container => {
    container.addEventListener('click', () => {
        const elem = document.createElement(container.dataset.elementType);
        for (attr in container.dataset) {
            if (attr === 'elementType')
                continue;
            // console.info(`[consent cover] attr: setting ${attr.replace(/[A-Z]/g, m => "-" + m.toLowerCase())}="${container.dataset[attr]}"`);
            elem.setAttribute(attr.replace(/[A-Z]/g, m => "-" + m.toLowerCase()), container.dataset[attr]);
        };
        container.replaceWith(elem);
    });
});


/* Page Rating */
const rating = document.getElementById('rating-rating');
const stars = document.querySelectorAll('.page-rating-stars > *');
stars.forEach(star => {
    star.addEventListener('mouseenter', () => {
        stars.forEach(s => {
            s.classList.toggle('glowing', s.dataset.rating <= star.dataset.rating);
        });
    });

    star.addEventListener('click', () => {
        rating.value = star.dataset.rating;
    });

    star.addEventListener('mouseleave', () => {
        stars.forEach(s => {s.classList.toggle('glowing', s.dataset.rating <= rating.value)});
    });
});

/* Page Rating Success or Failure Message */
if (document.location.hash.substring(1) === 'rate-success') {
    UIkit.notification('Thank you! We will forward your rating to the appropriate department.', 'success');
}
if (document.location.hash.substring(1) === 'rate-failure') {
    UIkit.notification('Uhoh, something went wrong on our side. Please tell @draconigen on Telegram.', 'danger');
}

/* Back To Top (UIkit Totop) */
const toTopButton = document.getElementById('ef-to-top');
if (toTopButton) {
    toTopButton.addEventListener('click', (event) => {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    const configuredThreshold = Number.parseInt(toTopButton.dataset.threshold || '100', 10);
    const threshold = Number.isFinite(configuredThreshold) && configuredThreshold > 0 ? configuredThreshold : 100;
    const fadeRange = 48;
    let toTopTicking = false;

    const updateToTopVisibility = () => {
        toTopTicking = false;

        const scrollY = window.scrollY || document.documentElement.scrollTop || 0;
        const fadeStart = Math.max(0, threshold - fadeRange);
        const fadeEnd = threshold + fadeRange;
        const raw = (scrollY - fadeStart) / (fadeEnd - fadeStart);
        const opacity = Math.max(0, Math.min(1, raw));

        toTopButton.style.opacity = String(opacity);
        if (opacity > 0.01) {
            toTopButton.classList.add('ef-visible');
        } else {
            toTopButton.classList.remove('ef-visible');
        }
    };

    const scheduleToTopUpdate = () => {
        if (toTopTicking) {
            return;
        }
        toTopTicking = true;
        window.requestAnimationFrame(updateToTopVisibility);
    };

    window.addEventListener('scroll', scheduleToTopUpdate, { passive: true });
    window.addEventListener('resize', scheduleToTopUpdate);
    scheduleToTopUpdate();
}

