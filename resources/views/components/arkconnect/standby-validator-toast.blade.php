<x-arkconnect.validator-toast
    id="standby"
    show-property="showValidatorOnStandbyMessage"
    :text="trans('general.arkconnect.validator_standby')"
    :link-text="trans('general.arkconnect.view_validators')"
    divider-class="border-theme-primary-200 dark:border-theme-dark-500"
    on-close="ignoreStandbyAddress();"
    type="info"
/>
