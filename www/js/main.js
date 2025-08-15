// introduced by Xenor in 2023: make the background page unscrollable while the nav overlay is open
// document.getElementById("nav-state").addEventListener('change', function() {
//     document.getElementsByTagName("body")[0].style = "overflow: " + (this.checked? "hidden" : "auto") + ";"
// })

// job deeplinking
document.querySelectorAll('[id^=ef-job-]').forEach(element => {
    // attach hash on open
    UIkit.util.on(element, 'beforeshow', () => {
        window.location.hash = element.id.substring(7);
    });
    // remove hash on close
    UIkit.util.on(element, 'beforehide', () => {
        window.location.hash = '';
    });
});
// open job on page load
const job = document.getElementById(`ef-job-${window.location.hash.substring(1)}`);
if (window.location.hash && job) {
    UIkit.modal(job).show();
}

/* Page Rating */
const pageRatingStars = document.querySelectorAll('.page-rating-stars > *');
pageRatingStars.forEach(star => {
    star.addEventListener('mouseenter', () => {
        const rating = parseInt(star.dataset.rating);
        pageRatingStars.forEach(s => {
            const current = parseInt(s.dataset.rating);
            s.classList.toggle('selected', current <= rating);
        });
    });

    star.addEventListener('mouseleave', () => {
        pageRatingStars.forEach(s => s.classList.remove('selected'));
    });
})