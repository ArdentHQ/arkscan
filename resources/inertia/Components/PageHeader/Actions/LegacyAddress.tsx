import PageHeaderValuePopup from "./ValuePopup";
import { IWallet } from "@/types";
import { useTranslation } from "react-i18next";
import ArrowExternal from "@/Assets/Icons/Arrows/ArrowExternal";
import Clock from "@/Assets/Icons/Arrows/Clock";
import { useConfig } from "@/Providers/Config/ConfigContext";

export default function PageHeaderLegacyAddressAction({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    return (
        <PageHeaderValuePopup
            value={wallet.legacyAddress!}
            button={<Clock className="w-4 h-4" />}
            title={t('pages.wallet.legacy-address.title')}
            id="legacyAddress"
            additionalButtons={(
                <a
                    href={`${network.legacyExplorerUrl}/addresses/${wallet.legacyAddress}`}
                    target="_blank"
                    className="p-2 w-full focus-visible:ring-inset button button-secondary button-icon"
                >
                    <ArrowExternal className="w-4 h-4" />
                </a>
            )}
        />
    )
}
