<div class="flex">
    <div class="-mr-2 circled-icon text-theme-secondary-400 border-theme-danger-400">
        @svg($icon, 'w-5 h-5')
    </div>

    <div class="circled-icon text-theme-secondary-400 border-theme-secondary-700">
        <div class="w-6 h-6 overflow-hidden rounded-full md:w-8 md:h-8">
            <div class="object-cover w-full h-full">
                {!! Avatar::make($model->address()) !!}
            </div>
        </div>
    </div>
</div>
