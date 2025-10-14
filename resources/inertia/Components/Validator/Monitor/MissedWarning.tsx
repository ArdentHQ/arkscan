import { IValidator } from "@/types";
import Tippy from "@tippyjs/react";
import { useTranslation } from "react-i18next";
import AlertTriangleIcon from "@ui/icons/alert-triangle.svg?react";

export default function MissedWarning({ validator }: { validator: IValidator}) {
    const { t } = useTranslation();

    if (validator.wallet.keepsMissing === false) {
        return null;
    }

    return (
        <div data-testid={`missed-warning-${validator.wallet.address}`}>
            <Tippy content={
                t('pages.validator-monitor.missed_blocks_tooltip', {
                    blocks: validator.wallet.blocksSinceLastForged,
                    time: validator.wallet.durationSinceLastForged,
                })
            }>
                <div className="text-theme-warning-900">
                    <AlertTriangleIcon className="w-4 h-4" />
                </div>
            </Tippy>
        </div>
    )
}
