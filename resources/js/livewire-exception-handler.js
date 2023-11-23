class LivewireExceptionHandler {
    CONCURRENT_MAX = 10;

    concurrentFailures = {};

    constructor() {
        Livewire.hook("message.failed", this.handleFailure.bind(this));

        Livewire.hook("message.processed", this.handleSuccess.bind(this));
    }

    handleFailure(message, component) {
        if (typeof this.concurrentFailures[component.id] === 'undefined') {
            this.concurrentFailures[component.id] = 0;
        }

        this.concurrentFailures[component.id]++;

        if (this.concurrentFailures[component.id] === this.CONCURRENT_MAX) {
            location.reload();
        }
    }

    handleSuccess(message, component) {
        if (typeof this.concurrentFailures[component.id] === 'undefined') {
            return;
        }

        delete this.concurrentFailures[component.id];
    }
}

export default new LivewireExceptionHandler();
