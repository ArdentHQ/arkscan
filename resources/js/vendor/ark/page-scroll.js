window.scrollToQuery = (selector) => {
    const navbar = document.querySelector("#navbar");

    window.scrollTo({
        top: document.querySelector(selector).offsetTop - navbar.clientHeight,
        behavior: "smooth",
    });
};
