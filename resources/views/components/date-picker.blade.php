<input
    x-data
    x-ref="input"
    x-init="new Pikaday({
        field: $refs.input,
        format: 'DD.MM.YYYY',
        minDate: new Date('{{ Network::epoch() }}'),
        maxDate: new Date(),
        toString(date, format) {
            return date.toLocaleDateString('fi-FI', { year: 'numeric', month: '2-digit', day: '2-digit' });
        },
    })"
    type="text"
    onchange="this.dispatchEvent(new InputEvent('input'))"
    {{ $attributes }}
>
