<x-arkconnect.delegate-toast
    id="standby"
    show-property="showDelegateOnStandbyMessage"
    :text="trans('general.arkconnect.delegate_standby')"
    :link-text="trans('general.arkconnect.view_delegates')"
    divider-class="border-theme-primary-200 dark:border-theme-dark-500"
    on-close="ignoreStandbyAddress();"
    type="info"
/>
