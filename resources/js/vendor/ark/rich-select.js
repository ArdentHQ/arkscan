const RichSelect = (
    extraData = {},
    options,
    initialValue = "",
    initialText = "",
    grouped = false,
    dispatchEvent = undefined
) => ({
    options,
    onInput($dispatch, $event) {
        if (dispatchEvent) {
            $dispatch(dispatchEvent, $event.target.value);
        }
    },
    init() {
        this.$nextTick(() => {
            if (grouped) {
                this.optionsCount = Object.keys(this.options)
                    .map((groupName) => Object.keys(this.options[groupName]))
                    .flat().length;
            } else {
                this.optionsCount = Object.keys(this.options).length;
            }
        });
    },
    optionsCount: null,
    open: false,
    selected: null,
    selectedGroup: null,
    value: initialValue,
    text: initialText,
    choose(value, groupName = null) {
        if (this.value === value) {
            return;
        }

        this.value = value;

        this.text =
            groupName !== null
                ? this.options[groupName][value]
                : this.options[value];

        this.open = false;

        this.setHiddenInputValue(value);
    },
    setHiddenInputValue(value, dispatchEvent = true) {
        const { input } = this.$refs;

        input.value = value;

        if (!dispatchEvent) {
            return;
        }

        const event = new Event("input", {
            bubbles: true,
            cancelable: true,
        });

        input.dispatchEvent(event);
    },
    onButtonClick() {
        const { listbox } = this.$refs;
        let selectedIndex;

        if (grouped) {
            selectedIndex = this.getAllValues().indexOf(this.value);
        } else {
            selectedIndex = Object.keys(this.options).indexOf(this.value);
        }

        this.selected = selectedIndex >= 0 ? selectedIndex : 0;
        this.open = true;
        this.$nextTick(() => {
            listbox.focus();
            this.scrollToSelectedOption();
        });
    },
    onOptionSelect() {
        if (grouped) {
            const allValues = this.getAllValues();
            this.choose(allValues[this.selected], this.selectedGroup);
        } else if (this.selected !== null) {
            this.choose(Object.keys(this.options)[this.selected]);
        }
        this.open = false;
        this.$refs.button.focus();
    },
    onEscape() {
        this.open = false;
        this.$refs.button.focus();
    },
    onArrowUp() {
        this.selected =
            this.selected - 1 < 0 ? this.optionsCount - 1 : this.selected - 1;
        this.scrollToSelectedOption();
    },
    onArrowDown() {
        this.selected =
            this.selected + 1 > this.optionsCount - 1 ? 1 : this.selected + 1;
        this.scrollToSelectedOption();
    },
    scrollToSelectedOption() {
        const { listbox } = this.$refs;
        const option = listbox.querySelectorAll("[data-option]")[this.selected];
        if (grouped) {
            this.selectedGroup = option.dataset.group;
        }
        option.scrollIntoView({
            block: "nearest",
        });
    },
    getAllValues() {
        return Object.keys(this.options)
            .map((groupName) => Object.keys(this.options[groupName]))
            .flat();
    },
    getOptionIndex(groupIndex, optionIndex) {
        let index = 0;
        for (let i = 0; i < groupIndex; i++) {
            const group = this.options[Object.keys(this.options)[i]];
            index += Object.keys(group).length;
        }
        return index + optionIndex;
    },
    ...extraData,
});

export default RichSelect;
