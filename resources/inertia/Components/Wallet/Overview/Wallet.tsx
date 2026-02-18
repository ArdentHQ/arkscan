import { IWallet } from "@/types/generated";
import WalletOverviewItem from "./Item";
import { useTranslation } from "react-i18next";
import WalletOverviewItemEntry from "./ItemEntry";
import useSharedData from "@/hooks/use-shared-data";
import Address from "../Address";
import FiatValue from "@/Components/General/FiatValue";
import Tooltip from "@/Components/General/Tooltip";
import { useTabs } from "@/Providers/Tabs/TabsContext";

export default function WalletOverviewWallet({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const { select } = useTabs();

    const showTooltip = wallet.formattedBalanceTwoDecimals !== wallet.formattedBalanceFull;

    return (
        <WalletOverviewItem title={t("general.overview")}>
            <WalletOverviewItemEntry title={t("pages.wallet.name")} value={wallet.username} />

            <WalletOverviewItemEntry
                title={t("pages.wallet.balance")}
                value={
                    <>
                        {showTooltip && (
                            <div className="sm:hidden">
                                <Tooltip content={wallet.formattedBalanceFull}>
                                    <span>{wallet.formattedBalanceTwoDecimals}</span>
                                </Tooltip>
                            </div>
                        )}

                        {!showTooltip && <span className="sm:hidden">{wallet.formattedBalanceTwoDecimals}</span>}

                        <span className="hidden sm:inline">{wallet.formattedBalanceFull}</span>
                    </>
                }
            />

            <WalletOverviewItemEntry
                title={t("pages.wallet.value")}
                value={network!.canBeExchanged ? <FiatValue value={wallet.fiatValue} /> : null}
            />

            <WalletOverviewItemEntry
                title={t("pages.wallet.token_holdings")}
                value={
                    <span className="inline-flex items-center space-x-2">
                        <span>
                            {wallet.tokenHoldingsCount} {t("pages.wallet.tokens")}
                        </span>
                        <button type="button" className="link text-sm font-semibold" onClick={() => select("tokens")}>
                            {t("general.view")}
                        </button>
                    </span>
                }
            />

            <WalletOverviewItemEntry
                title={t("pages.wallet.voting_for")}
                value={wallet.vote ? <Address wallet={wallet.vote} /> : null}
            />
        </WalletOverviewItem>
    );
}
