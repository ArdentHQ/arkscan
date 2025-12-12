import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import DropdownPopup from "@/Components/General/Dropdown/DropdownPopup";
import QRCode from "react-qr-code";
import { useRef, useState } from "react";
import ExternalLink from "@/Components/General/ExternalLink";
import { URLBuilder } from "@ardenthq/arkvault-url";
import Input from "@/Components/Input/Input";
import ArkConnectDisabledAction from "@/Components/General/ArkConnect/DisabledAction";
import QRCodeIcon from "@ui/icons/qr-code.svg?react";
import Tooltip from "@/Components/General/Tooltip";
import useSharedData from "@/hooks/use-shared-data";
import { useArkConnect } from "@/Providers/ArkConnect/ArkConnectContext";
import { WalletProps } from "@/Pages/Wallet.contracts";

function ArkVaultButton({
    amount,
    walletUri,
}: {
    amount: number | undefined;

    walletUri: string;
}) {
    const { t } = useTranslation();

    const { isOnSameNetwork, isConnected, performSend, isArkConnectEnabled } = useArkConnect();
    const { wallet } = useSharedData<WalletProps>();

    let arkconnectButton = null;

    if (isOnSameNetwork) {
        arkconnectButton = (
            <div>
                <button
                    type="button"
                    className="button-primary w-full"
                    onClick={() => {
                        void performSend(wallet.address, amount!);
                    }}
                    disabled={!amount}
                >
                    {t("brands.arkconnect")}
                </button>
            </div>
        );

        if (!amount) {
            arkconnectButton = (
                <Tooltip content={t("pages.wallet.qrcode.arkconnect_specify_amount_tooltip")}>
                    {arkconnectButton}
                </Tooltip>
            );
        }
    }

    return (
        <div className="mt-2 w-full">
            {isArkConnectEnabled && (
                <div className="flex w-full flex-col">
                    {!!arkconnectButton ? (
                        arkconnectButton
                    ) : (
                        <ArkConnectDisabledAction isConnected={isConnected} isOnSameNetwork={isOnSameNetwork ?? false}>
                            <button type="button" className="button-primary w-full" disabled>
                                {t("brands.arkconnect")}
                            </button>
                        </ArkConnectDisabledAction>
                    )}

                    <ExternalLink
                        url={walletUri}
                        className="button-secondary mt-2 w-full"
                        iconClass="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-primary-400 dim:text-theme-dim-blue-300 dark:text-theme-dark-500"
                    >
                        {t("brands.arkvault")}
                    </ExternalLink>
                </div>
            )}

            {!isArkConnectEnabled && (
                <div>
                    <ExternalLink
                        url={walletUri}
                        className="button-primary w-full"
                        iconClass="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-primary-400 dim:text-theme-dim-blue-300 dark:text-theme-dark-blue-300 w-3 h-3"
                    >
                        {t("brands.arkvault")}
                    </ExternalLink>
                </div>
            )}
        </div>
    );
}

function QRCodeContent({ wallet, testId }: { wallet: IWallet; testId?: string }) {
    const { t } = useTranslation();
    const [showOptions, setShowOptions] = useState(false);
    const { arkconnectConfig, network } = useSharedData();
    const [amount, setAmount] = useState<number | undefined>(undefined);

    const urlBuilder = new URLBuilder(arkconnectConfig.vaultUrl);
    urlBuilder.setNethash(network!.nethash);
    urlBuilder.setCoin(network!.coin);

    const walletUri = urlBuilder.generateTransfer(wallet.address, {
        amount: amount !== undefined && amount > 0 ? amount : undefined,
    });

    const hasTrackedOpen = useRef(false);

    return (
        <DropdownPopup
            title={t("pages.wallet.qrcode.title")}
            width="w-[calc(100vw)] sm:max-w-[320px]"
            zIndex={30}
            button={
                <div className="button button-secondary button-icon w-full p-2 focus-visible:ring-inset">
                    <QRCodeIcon className="h-4 w-4" />
                </div>
            }
            onClosed={() => setTimeout(() => setShowOptions(false))}
            onOpened={() => {
                if (!hasTrackedOpen.current) {
                    window.sa_event("qr_code_opened");

                    hasTrackedOpen.current = true;
                }
            }}
            testId={testId}
        >
            {showOptions && (
                <>
                    <div className="font-normal leading-5.25 text-theme-secondary-700 dark:text-theme-dark-200">
                        {t("pages.wallet.qrcode.description")}
                    </div>

                    <div className="py-4">
                        <Input
                            type="number"
                            id="amount"
                            name="amount"
                            maxLength={17}
                            className="font-normal"
                            inputClass="qr-code-amount"
                            label={t("pages.wallet.qrcode.currency_amount", { currency: network!.currency })}
                            value={amount}
                            onChange={(e) => setAmount(e.target.value ? parseFloat(e.target.value) : undefined)}
                            onWheel={(e) => {
                                const hasFocus = document.activeElement === e.target;
                                (e.target as HTMLInputElement).blur();
                                e.stopPropagation();

                                if (hasFocus) {
                                    setTimeout(() => {
                                        (e.target as HTMLInputElement).focus();
                                    }, 0);
                                }
                            }}
                            autoFocus
                        />
                    </div>
                </>
            )}

            <div className="flex justify-center">
                <div className="inline-block rounded-lg border border-theme-secondary-300 bg-white p-2 dark:border-theme-dark-300 sm:block">
                    <QRCode value={walletUri} size={224} />
                </div>
            </div>

            {!showOptions && (
                <button className="button-secondary mt-3 w-full" onClick={() => setShowOptions(true)}>
                    {t("pages.wallet.qrcode.specify_amount")}
                </button>
            )}

            {showOptions && (
                <div className="mt-4 font-normal leading-5.25 text-theme-secondary-700 dark:text-theme-dark-200">
                    {t("pages.wallet.qrcode.automatic_notice")}
                </div>
            )}

            <div className="mt-3 flex w-full items-center space-x-3">
                <div className="h-1px flex-1 border-t border-theme-secondary-300 dark:border-theme-dark-700"></div>

                <div className="font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                    {t("pages.wallet.qrcode.or_send_with")}
                </div>

                <div className="h-1px flex-1 border-t border-theme-secondary-300 dark:border-theme-dark-700"></div>
            </div>

            <ArkVaultButton amount={amount} walletUri={walletUri} />
        </DropdownPopup>
    );
}

export default function PageHeaderQRCodeModalAction({ wallet, testId }: { wallet: IWallet; testId?: string }) {
    return (
        <DropdownProvider>
            <QRCodeContent wallet={wallet} testId={testId} />
        </DropdownProvider>
    );
}
