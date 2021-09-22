<x-details.address
    :title="trans('general.transaction.multi_signature_address')"
    :transaction="$model"
    :model="$model->multiSignatureWallet()"
    title-icon="app.transactions-multi-signature"
/>
