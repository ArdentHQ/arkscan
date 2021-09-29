<div>
    @lang('labels.timestamp')

    <div>
        <x-ark-local-time
            :datetime="$model->dateTime()"
            :format="DateFormat::TIME_JS"
            :placeholder="$model->timestamp()"
        />
    </div>
</div>
