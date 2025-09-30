import { IWallet } from "@/types";
import WalletOverviewItemEntry from "../ItemEntry";
import { useTranslation } from "react-i18next";
import classNames from "@/utils/class-names";
import { useConfig } from "@/Providers/Config/ConfigContext";
import percentage from "@/utils/percentage";

export default function WalletOverviewValidatorProductivity({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { productivity } = useConfig();

    const walletProductivity = wallet.isActive ? wallet.productivity : 0;
    const isLow = wallet.isActive && walletProductivity < productivity!.danger;
    const isMedium = ! isLow && wallet.isActive && walletProductivity >= productivity!.danger && walletProductivity < productivity!.warning;
    const isHigh = ! isMedium && wallet.isActive && walletProductivity >= productivity!.warning;

    return (
        <WalletOverviewItemEntry
            title={t('pages.wallet.validator.productivity_title')}
            hasEmptyValue={! wallet.isValidator || wallet.isResigned || wallet.isDormant}
            value={(
                <>
                    {! wallet.isResigned && ! wallet.isDormant && (
                        <div className={classNames({
                            'flex items-center space-x-2': true,
                            'text-theme-secondary-500 dark:text-theme-dark-700': ! wallet.isActive,
                            'text-theme-danger-700 dark:text-theme-danger-400': isLow,
                            'text-theme-warning-700': isMedium,
                            'text-theme-success-700 dark:text-theme-success-500': isHigh,
                        })}>
                            <div>
                                {percentage(walletProductivity)}
                            </div>

                            <div className={classNames({
                                'w-4 h-4 rounded-full border': true,
                                'border-theme-secondary-200 bg-theme-secondary-400 dark:border-theme-dark-800 dark:bg-theme-dark-700': ! wallet.isActive,
                                'border-theme-danger-200 bg-theme-danger-700 dark:border-theme-danger-700 dark:bg-theme-danger-400': isLow,
                                'border-theme-warning-200 bg-theme-warning-700 dark:border-theme-warning-800 dark:bg-theme-warning-700': isMedium,
                                'border-theme-success-200 bg-theme-success-700 dark:border-theme-success-600 dark:bg-theme-success-500': isHigh,
                            })}></div>
                        </div>
                    )}
                </>
            )}
        />
    );
}
