import { disableBodyScroll, enableBodyScroll } from "body-scroll-lock";

const Modal = (extraData = {}) => {
    return {
        init() {
            const el = this.$el;

            disableBodyScroll(el);

            // Since alpine doesn't have any method to track the destruction of
            // the element, this interval will ensure to enable the body
            // scrolling once the modal is no longer visible
            const interval = setInterval(() => {
                if (
                    ["", "none"].includes(window.getComputedStyle(el).display)
                ) {
                    enableBodyScroll(el);
                    clearInterval(interval);
                }
            }, 500);
        },
        ...extraData,
    };
};

export default Modal;
