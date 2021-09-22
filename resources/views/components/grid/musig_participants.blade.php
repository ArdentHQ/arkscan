<x-grid.generic :title="trans('pages.transaction.musig_participants')" icon="edit">
    @lang('pages.transaction.musig_participants_text', [
        $model->multiSignatureMinimum(),
        $model->multiSignatureParticipantCount()
    ])
</x-grid.generic>
