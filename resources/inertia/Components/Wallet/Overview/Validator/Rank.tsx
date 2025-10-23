import { IWallet } from "@/types/generated";
import WalletOverviewItemEntry from "../ItemEntry";
import { useTranslation } from "react-i18next";
import { useConfig } from "@/Providers/Config/ConfigContext";
import Info from "@/Components/General/Info";

export default function WalletOverviewValidatorRank({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    const rank = wallet.attributes?.validatorRank;

    return (
        <WalletOverviewItemEntry
            title={t("pages.wallet.validator.rank")}
            hasEmptyValue={!wallet.isValidator}
            value={
                <>
                    {!wallet.isResigned && !wallet.isDormant && (
                        <>
                            <span>#{rank}</span>
                            <span> / </span>
                        </>
                    )}

                    {wallet.isDormant && (
                        <div className="flex items-center space-x-2">
                            <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                {t("pages.validators.dormant")}
                            </span>

                            <Info tooltip={t("pages.validators.dormant_tooltip")} type="info" />
                        </div>
                    )}

                    {wallet.isResigned && (
                        <span className="text-theme-danger-700 dark:text-theme-danger-400">
                            {t("pages.validators.resigned")}
                        </span>
                    )}

                    {rank > network!.validatorCount && !wallet.isResigned && !wallet.isDormant && (
                        <span className="text-theme-secondary-500 dark:text-theme-dark-500">
                            {t("pages.validators.standby")}
                        </span>
                    )}

                    {rank <= network!.validatorCount && !wallet.isResigned && !wallet.isDormant && (
                        <span className="text-theme-success-700 dark:text-theme-success-500">
                            {t("pages.validators.active")}
                        </span>
                    )}
                </>
            }
        />
    );
}
