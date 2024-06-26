class LivewireExceptionHandler {
    CONCURRENT_MAX = 10;

    failures = {};

    constructor() {
        Livewire.hook("commit", ({ component, fail, succeed }) => {
            succeed(() => {
                this.handleSuccess(component);
            });

            fail(() => {
                this.handleFailure(component);
            });
        });
    }

    handleFailure(component) {
        if (typeof this.failures[component.id] === "undefined") {
            this.failures[component.id] = 0;
        }

        this.failures[component.id]++;

        if (this.failures[component.id] === this.CONCURRENT_MAX) {
            location.reload();
        }
    }

    handleSuccess(component) {
        if (typeof this.failures[component.id] === "undefined") {
            return;
        }

        delete this.failures[component.id];
    }
}

export default new LivewireExceptionHandler();
