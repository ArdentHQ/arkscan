window.addEventListener("DOMContentLoaded", () => {
    const dropdownButtons = document.querySelectorAll(".dropdown-button");
    for (const button of dropdownButtons) {
        if (
            !button ||
            (button.getBoundingClientRect().height === 0 &&
                button.getBoundingClientRect().width === 0)
        ) {
            continue;
        }

        const bounds = button.getBoundingClientRect();
        const dropdown = button.parentElement.parentElement.querySelector(
            ".dropdown"
        );
        if (!dropdown || !dropdown.dataset.height) {
            continue;
        }

        const dropdownBottom =
            bounds.top +
            bounds.height +
            document.documentElement.scrollTop +
            parseInt(dropdown.dataset.height);
        if (dropdownBottom > document.body.clientHeight) {
            dropdown.style =
                "display: none; margin-top: -" +
                (dropdownBottom - document.body.clientHeight + 20) +
                "px";
        }
    }
});
