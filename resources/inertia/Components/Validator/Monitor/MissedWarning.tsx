import Tippy from "@tippyjs/react";
import AlertTriangle from '@ui/icons/alert-triangle.svg';

export default function MissedWarning({ validator }) {
    if (! validator.wallet.keepsMissing) {
        return null;
    }

    return (
        <Tippy content={
            `Validator last forged ${validator.wallet.blocksSinceLastForged} blocks ago (${validator.wallet.blocksSinceLastForged})`}
        >
            <div className="text-theme-warning-900">
                <AlertTriangle className="w-4 h-4" />
            </div>
        </Tippy>
    )
}
