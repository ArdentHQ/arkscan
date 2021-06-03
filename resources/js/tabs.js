const Tabs = (defaultSelected = "", extraData = {}, onSelected = () => {}) => {
    return {
        selected: defaultSelected,
        select(name) {
            this.selected = name;
            this.onSelected(name);
        },
        selectPrevTab(e) {
            const el = e.target;
            const wrapper = el.closest("[role=tablist]");
            let prevTab = el;
            do {
                prevTab = prevTab.previousElementSibling;
                if (prevTab === null) {
                    prevTab = wrapper.querySelector("[role=tab]:last-child");
                }
            } while (!prevTab.matches("[role=tab]"));
            prevTab.focus();
        },
        selectNextTab(e) {
            const el = e.target;
            const wrapper = el.closest("[role=tablist]");
            let nextTab = el;

            do {
                nextTab = nextTab.nextElementSibling;
                if (nextTab === null) {
                    nextTab = wrapper.querySelector("[role=tab]");
                }
            } while (!nextTab.matches("[role=tab]"));

            nextTab.focus();
        },
        onSelected: onSelected,
        ...extraData,
    };
};

export default Tabs;
