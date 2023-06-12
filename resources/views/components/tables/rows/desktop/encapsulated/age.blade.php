@props([
    'model',
    'class' => 'text-theme-secondary-900 dark:text-theme-secondary-200',
])

<span
    x-data="{
        datetime: dayjs({{ $model->dateTime()->timestamp }} * 1000),
        tooltip: null,
        output: '',
    }"
    x-init="
        output = dayjs().to(datetime);
        tooltip = datetime.format('{{ DateFormat::TIME_JS }}');
        $nextTick(() => tippy($el, tooltipSettings));
        setInterval(() => output = dayjs().to(datetime), 1000);
    "
    @class(['text-sm font-semibold', $class])
    :data-tippy-content="tooltip"
    x-text="output"
></span>
