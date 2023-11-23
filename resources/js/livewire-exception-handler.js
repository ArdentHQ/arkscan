class LivewireExceptionHandler {
    CONCURRENT_MAX = 10;

    failures = {};

    constructor() {
        Livewire.hook("message.failed", this.handleFailure.bind(this));

        Livewire.hook("message.processed", this.handleSuccess.bind(this));
    }

    handleFailure(message, component) {
        if (typeof this.failures[component.id] === 'undefined') {
            this.failures[component.id] = 0;
        }

        this.failures[component.id]++;

        if (this.failures[component.id] === this.CONCURRENT_MAX) {
            location.reload();
        }
    }

    handleSuccess(message, component) {
        if (typeof this.failures[component.id] === 'undefined') {
            return;
        }

        delete this.failures[component.id];
    }
}

export default new LivewireExceptionHandler();
