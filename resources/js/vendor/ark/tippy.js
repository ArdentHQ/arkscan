import tippy from "tippy.js";
import "tippy.js/dist/tippy.css";

/** Enable tooltips for components with this data attribute, and global config options */
tippy("[data-tippy-content]", {
    trigger: "mouseenter click",
});

window.initClipboard = () => {
    tippy(".clipboard", {
        trigger: "click",
        content: (reference) => reference.getAttribute("tooltip-content"),
        onShow(instance) {
            setTimeout(() => {
                instance.hide();
            }, 3000);
        },
    });
};
