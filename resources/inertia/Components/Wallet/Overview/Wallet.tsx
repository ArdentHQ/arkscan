import { IWallet } from "@/types/generated";
import WalletOverviewItem from "./Item";
import { useTranslation } from "react-i18next";
import WalletOverviewItemEntry from "./ItemEntry";
import { useConfig } from "@/Providers/Config/ConfigContext";
import Address from "../Address";
import FiatValue from "@/Components/General/FiatValue";
import Tooltip from "@/Components/General/Tooltip";

export default function WalletOverviewWallet({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    const showTooltip = wallet.formattedBalanceTwoDecimals !== wallet.formattedBalanceFull;

    return (
        <WalletOverviewItem title={t('general.overview')}>
            <WalletOverviewItemEntry
                title={t('pages.wallet.name')}
                value={wallet.username}
            />

            <WalletOverviewItemEntry
                title={t('pages.wallet.balance')}
                value={(
                    <>
                        {showTooltip && (
                            <div className="sm:hidden">
                                <Tooltip content={wallet.formattedBalanceFull}>
                                    <span>{wallet.formattedBalanceTwoDecimals}</span>
                                </Tooltip>
                            </div>
                        )}

                        <span className="hidden sm:inline">
                            {wallet.formattedBalanceFull}
                        </span>
                    </>
                )}
            />

            <WalletOverviewItemEntry
                title={t('pages.wallet.value')}
                value={network!.canBeExchanged ? <FiatValue value={wallet.fiatValue} /> : null}
            />

            <WalletOverviewItemEntry
                title={t('pages.wallet.voting_for')}
                value={wallet.vote ? <Address wallet={wallet.vote} /> : null}
            />
        </WalletOverviewItem>
    )
}
