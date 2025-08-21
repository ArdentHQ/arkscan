import { IValidator } from "@/types";
import Tippy from "@tippyjs/react";
import AlertTriangleIcon from '@/Assets/Icons/AlertTriangle';

export default function MissedWarning({ validator }: { validator: IValidator}) {
    if (! validator.wallet.keepsMissing) {
        return null;
    }

    return (
        <Tippy content={
            `Validator last forged ${validator.wallet.blocksSinceLastForged} blocks ago (${validator.wallet.blocksSinceLastForged})`}
        >
            <div className="text-theme-warning-900">
                <AlertTriangleIcon className="w-4 h-4" />
            </div>
        </Tippy>
    )
}
