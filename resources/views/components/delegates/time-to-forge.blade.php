@props(['model'])

<span
    x-data="{
        datetime: dayjs({{ $model->forgingAt()->timestamp }} * 1000),
        tooltip: null,
        output: '{{ $model->forgingAt() }}',
    }"
    x-init="
        updateOutput = () => {
            seconds = datetime.diff(dayjs(), 'second');
            if (seconds < 60) {
                output = `${seconds} seconds`;
            } else {
                output = dayjs().to(datetime);
            }
        };
        tooltip = datetime.format('{{ DateFormat::TIME_JS }}');
        $nextTick(() => tippy($el, tooltipSettings));
        updateOutput();
        setInterval(updateOutput, 1000);
    "
    {{ $attributes }}
    :data-tippy-content="tooltip"
    x-text="output"
></span>
