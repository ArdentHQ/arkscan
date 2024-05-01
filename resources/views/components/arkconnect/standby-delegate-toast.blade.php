<div
    x-data="{ preventPopup: false }"
    x-show="showDelegateOnStandbyMessage"
    class="flex fixed bottom-4 right-7 z-20 max-w-[410px] arkconnect-toast"
    x-cloak
>
    <x-ark-toast
        class="mx-auto !max-w-7xl"
        type="info"
        alpine-close="if (preventPopup) {
            ignoreStandbyAddress();
        }"
        hide-spinner
        always-show-title
    >
        @lang('general.arkconnect.delegate_standby')

        <a
            class="link font-semibold"
            href="{{ route('delegates') }}"
        >
            @lang('general.arkconnect.view_delegates')
        </a>

        <div class="border-t border-dashed border-theme-primary-200 dark:border-theme-dark-500 my-3"></div>

        <x-ark-checkbox
            name="do_not_remind"
            :label="trans('forms.general.do_not_remind')"
            label-classes="text-black dark:text-theme-dark-50"
            class=""
            x-model="preventPopup"
            {{-- alpine-label-class="{
                'text-theme-primary-600 dark:text-theme-dark-blue-500 font-semibold': preventPopup === true,
                'text-theme-secondary-900 dark:text-theme-dark-50': preventPopup === false,
            }" --}}
        />
    </x-ark-toast>
</div>
