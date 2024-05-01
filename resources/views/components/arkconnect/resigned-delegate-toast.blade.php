<x-arkconnect.delegate-toast
    x-show="showDelegateResignedMessage"
    :text="trans('general.arkconnect.delegate_resigned')"
    :link-text="trans('general.arkconnect.delegate_resigned_switch_vote')"
    divider-class="border-theme-warning-200 dark:border-theme-dark-500"
    on-close="ignoreResignedAddress();"
    type="warning"
/>
