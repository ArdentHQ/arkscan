import { IWallet } from "@/types/generated";
import { Wallet } from "@/models/Wallet";
import WalletOverviewItemEntry from "../ItemEntry";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import Info from "@/Components/General/Info";

export default function WalletOverviewValidatorRank({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const walletModel = Wallet.from(wallet, {
        validatorCount: network!.validatorCount,
        knownWallets: network!.knownWallets,
    });

    const rank = wallet.attributes?.validatorRank;

    return (
        <WalletOverviewItemEntry
            title={t("pages.wallet.validator.rank")}
            hasEmptyValue={!walletModel.isValidator}
            value={
                <>
                    {!walletModel.isResigned && !walletModel.isDormant && (
                        <>
                            <span>#{rank}</span>
                            <span> / </span>
                        </>
                    )}

                    {walletModel.isDormant && (
                        <div className="flex items-center space-x-2">
                            <span className="text-theme-secondary-700 dark:text-theme-dark-500">
                                {t("pages.validators.dormant")}
                            </span>

                            <Info tooltip={t("pages.validators.dormant_tooltip")} type="info" />
                        </div>
                    )}

                    {walletModel.isResigned && (
                        <span className="text-theme-danger-700 dark:text-theme-danger-400">
                            {t("pages.validators.resigned")}
                        </span>
                    )}

                    {rank > network!.validatorCount && !walletModel.isResigned && !walletModel.isDormant && (
                        <span className="text-theme-secondary-500 dark:text-theme-dark-500">
                            {t("pages.validators.standby")}
                        </span>
                    )}

                    {rank <= network!.validatorCount && !walletModel.isResigned && !walletModel.isDormant && (
                        <span className="text-theme-success-700 dark:text-theme-success-500">
                            {t("pages.validators.active")}
                        </span>
                    )}
                </>
            }
        />
    );
}
