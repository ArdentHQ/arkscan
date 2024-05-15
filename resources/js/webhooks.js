window.Webhook = {
    livewire(channel, event, emit) {
        Echo.channel(channel)
            .listen(event, () => {
                Livewire.emit(emit);
            });
    }
};
