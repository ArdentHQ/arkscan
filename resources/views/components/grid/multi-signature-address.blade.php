<x-details.address
    :title="trans('general.transaction.multi_signature_address')"
    :transaction="$model"
    :model="$model->multiSignatureWallet()"
    icon="app-volume" />
