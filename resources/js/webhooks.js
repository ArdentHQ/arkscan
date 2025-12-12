window.Webhook = {
    listeners: {},

    listen(channel, event, emit) {
        if (this.listeners[channel] === undefined) {
            this.listeners[channel] = {};
        }

        if (this.listeners[channel][event] === undefined) {
            this.listeners[channel][event] = {};
        }

        if (this.listeners[channel][event][emit] !== undefined) {
            return;
        }

        const emitter = () => {
            Livewire.emit(emit);
        };

        Echo.channel(channel).subscribe();
        Echo.channel(channel).listen(event, emitter);

        this.listeners[channel][event][emit] = emitter;
    },

    remove(channel, event, emit) {
        if (this.listeners[channel] === undefined) {
            return;
        }

        if (this.listeners[channel][event] === undefined) {
            return;
        }

        if (this.listeners[channel][event][emit] === undefined) {
            return;
        }

        Echo.channel(channel).stopListening(event, this.listeners[channel][event][emit]);

        // The best way I could find to see if there were any events remaining.
        // We don't need to be subscribed if we're not listening for anything.
        if (Object.keys(Echo.channel(channel).subscription.callbacks._callbacks).length === 0) {
            Echo.channel(channel).unsubscribe();
        }

        this.listeners[channel][event][emit] = undefined;
    },
};
