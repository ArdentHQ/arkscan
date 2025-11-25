import PageHeaderValuePopup from "./ValuePopup";
import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import ClockIcon from "@ui/icons/arrows/clock.svg?react";
import ArrowExternalIcon from "@ui/icons/arrows/arrow-external.svg?react";

export default function PageHeaderLegacyAddressAction({ wallet, testId }: { wallet: IWallet; testId?: string }) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <PageHeaderValuePopup
            value={wallet.legacyAddress!}
            button={<ClockIcon className="h-4 w-4" />}
            title={t("pages.wallet.legacy-address.title")}
            id="legacy_address"
            copiedTooltip={t("pages.wallet.legacy_address_copied")}
            testId={testId}
            additionalButtons={
                <a
                    href={`${network!.legacyExplorerUrl}/addresses/${wallet.legacyAddress}`}
                    target="_blank"
                    className="button button-secondary button-icon w-full p-2 focus-visible:ring-inset"
                >
                    <ArrowExternalIcon className="h-4 w-4" />
                </a>
            }
        />
    );
}
