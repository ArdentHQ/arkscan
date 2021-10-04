@if($shortened ?? false)
    <x-ark-local-time
        :datetime="$model->dateTime()"
        :format="DateFormat::TIME_SHORT_JS"
        :placeholder="$model->timestamp(true)"
        :tooltip-format="DateFormat::TIME_JS"
    />
@else
    <x-ark-local-time
        :datetime="$model->dateTime()"
        :format="DateFormat::TIME_JS"
        :placeholder="$model->timestamp()"
    />
@endif
