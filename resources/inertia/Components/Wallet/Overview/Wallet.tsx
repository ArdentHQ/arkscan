import { IWallet } from "@/types/generated";
import WalletOverviewItem from "./Item";
import { useTranslation } from "react-i18next";
import WalletOverviewItemEntry from "./ItemEntry";
import useSharedData from "@/hooks/use-shared-data";
import Address from "../Address";
import FiatValue from "@/Components/General/FiatValue";
import Tooltip from "@/Components/General/Tooltip";
import { WalletProps } from "@/Pages/Wallet.contracts";
import { Link } from "@inertiajs/react";

export default function WalletOverviewWallet({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network, tokenHoldingsCount } = useSharedData<WalletProps>();

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
                        <span className="hidden min-[280px]:inline-block">
                            {tokenHoldingsCount} {t("pages.wallet.tokens")}
                        </span>

                        <Link
                            className="link"
                            href={route("wallet", { wallet: wallet.address, view: "tokens" }) + "#wallet:tabs:content"}
                        >
                            {t("general.view")}
                        </Link>
                    </span>
                }
            />

            <WalletOverviewItemEntry
                title={t("pages.wallet.voting_for")}
                value={wallet.vote ? <Address wallet={wallet.vote} truncate="dynamic" /> : null}
                valueClassName="min-w-0"
            />
        </WalletOverviewItem>
    );
}
