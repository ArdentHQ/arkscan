import { IWallet } from "@/types/generated";
import WalletOverviewItemEntry from "../ItemEntry";
import { useTranslation } from "react-i18next";
import { NetworkCurrency } from "@/Components/General/NetworkCurrency";
import Tooltip from "@/Components/General/Tooltip";

export default function WalletOverviewValidatorVotes({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();

    return (
        <WalletOverviewItemEntry
            title={t("pages.wallet.validator.votes_title")}
            hasEmptyValue={!wallet.isValidator}
            value={
                <>
                    {wallet.isValidator && (
                        <div className="flex items-center space-x-1">
                            <div>
                                <Tooltip content={NetworkCurrency({ value: wallet.votes })}>
                                    <NetworkCurrency value={wallet.votes} decimals={0} />
                                </Tooltip>
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    // TODO: show voters tab & scroll
                                }}
                                className="link"
                            >
                                {t("general.view")}
                            </button>
                        </div>
                    )}
                </>
            }
        />
    );
}
