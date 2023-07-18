document.querySelectorAll('[data-link-scroll-to]').forEach((element) => {
    element.addEventListener('click', function () {
        if (! element.dataset['linkScrollTo']) {
            return;
        }

        if (! document.querySelector(element.dataset['linkScrollTo'])) {
            return;
        }

        scrollToQuery(element.dataset['linkScrollTo']);
    });
});
