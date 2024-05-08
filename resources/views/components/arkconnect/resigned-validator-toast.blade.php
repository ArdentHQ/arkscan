<x-arkconnect.validator-toast
    id="resigned"
    show-property="showValidatorResignedMessage"
    :text="trans('general.arkconnect.validator_resigned')"
    :link-text="trans('general.arkconnect.validator_resigned_switch_vote')"
    divider-class="border-theme-warning-200 dark:border-theme-dark-500"
    on-close="ignoreResignedAddress();"
    type="warning"
/>
