import { useTranslation } from "react-i18next";
import PageHeaderContainer from "../../PageHeader/Container";
import { IWallet } from "@/types/generated";
import TruncateDynamic from "../../General/TruncateDynamic";
import Clipboard from "../../General/Clipboard";
import PageHeaderPublicKeyAction from "../../PageHeader/Actions/PublicKey";
import PageHeaderLegacyAddressAction from "../../PageHeader/Actions/LegacyAddress";
import WalletOverviewWallet from "./Wallet";
import WalletOverviewValidator from "./Validator/Validator";
import PageHeaderQRCodeModalAction from "@/Components/PageHeader/Actions/QRCodeModal";

function OverviewActions({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();

    return (
        <>
            <Clipboard
                value={wallet.address}
                className="button-secondary group flex h-auto w-full items-center p-2 focus-visible:ring-inset"
                wrapperClass="flex-1"
                tooltipContent={t("pages.wallet.address_copied")}
                withCheckmarks
                checkmarksClass="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
            />

            {!wallet.isCold && (
                <>
                    <PageHeaderPublicKeyAction wallet={wallet} />

                    {wallet.isLegacy && wallet.legacyAddress !== null && (
                        <PageHeaderLegacyAddressAction wallet={wallet} />
                    )}
                </>
            )}

            <PageHeaderQRCodeModalAction wallet={wallet} />
        </>
    );
}

export default function Overview({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();

    return (
        <>
            <PageHeaderContainer label={t("general.address")} extra={<OverviewActions wallet={wallet} />}>
                <TruncateDynamic value={wallet.address} />
            </PageHeaderContainer>

            <div className="md:mx-auto md:max-w-7xl md:px-10 md:pb-6">
                <div className="flex flex-col md:space-y-3 md-lg:flex-row md-lg:space-x-3 md-lg:space-y-0">
                    <WalletOverviewWallet wallet={wallet} />

                    <WalletOverviewValidator wallet={wallet} />
                </div>
            </div>
        </>
    );
}
