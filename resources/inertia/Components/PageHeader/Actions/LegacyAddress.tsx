import PageHeaderValuePopup from "./ValuePopup";
import { IWallet } from "@/types";
import { useTranslation } from "react-i18next";
import { useConfig } from "@/Providers/Config/ConfigContext";
import ClockIcon from "@ui/icons/arrows/clock.svg?react";
import ArrowExternalIcon from "@ui/icons/arrows/arrow-external.svg?react";

export default function PageHeaderLegacyAddressAction({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    return (
        <PageHeaderValuePopup
            value={wallet.legacyAddress!}
            button={<ClockIcon className="w-4 h-4" />}
            title={t('pages.wallet.legacy-address.title')}
            id="legacyAddress"
            additionalButtons={(
                <a
                    href={`${network.legacyExplorerUrl}/addresses/${wallet.legacyAddress}`}
                    target="_blank"
                    className="p-2 w-full focus-visible:ring-inset button button-secondary button-icon"
                >
                    <ArrowExternalIcon className="w-4 h-4" />
                </a>
            )}
        />
    )
}
