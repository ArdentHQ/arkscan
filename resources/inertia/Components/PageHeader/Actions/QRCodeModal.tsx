import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import DropdownPopup from "@/Components/General/Dropdown/DropdownPopup";
import QRCode from "react-qr-code";
import { useState } from "react";
import ExternalLink from "@/Components/General/ExternalLink";
import { useConfig } from "@/Providers/Config/ConfigContext";
import Tippy from "@tippyjs/react";
import { URLBuilder } from "@ardenthq/arkvault-url";
import Input from "@/Components/Input/Input";
import ArkConnectDisabledAction from "@/Components/General/ArkConnect/DisabledAction";
import QRCodeIcon from "@ui/icons/qr-code.svg?react";

function ArkVaultButton({ hasAmount, isOnSameNetwork = true, walletUri }: { hasAmount: boolean; isOnSameNetwork: boolean; walletUri: string }) {
    const { t } = useTranslation();
    const { arkconnect } = useConfig();

    let arkconnectButton = null;
    if (isOnSameNetwork) {
        arkconnectButton = (
            <div>
                <button
                    type="button"
                    className="w-full button-primary"
                    onClick={() => {
                        // TODO: arkconnect `await performSend('{{ $this->address }}', '{{ $this->amount }}')` - https://app.clickup.com/t/86dxxbq8r
                    }}
                    disabled={! hasAmount}
                >
                    {t('brands.arkconnect')}
                </button>
            </div>
        );

        if (! hasAmount) {
            arkconnectButton = (
                <Tippy content={t('pages.wallet.qrcode.arkconnect_specify_amount_tooltip')}>
                    {arkconnectButton}
                </Tippy>
            );
        }
    }

    return (
        <div className="mt-2 w-full">
            {arkconnect!.enabled && (
                <div className="flex flex-col w-full">
                    {/* @TODO: handle arkconnect functionality - https://app.clickup.com/t/86dxxbq8r */}
                    {!! arkconnectButton ? arkconnectButton : (
                        <ArkConnectDisabledAction
                            isConnected={false}
                            isOnSameNetwork={isOnSameNetwork}
                        >
                            <button
                                type="button"
                                className="w-full button-primary"
                                disabled
                            >
                                {t('brands.arkconnect')}
                            </button>
                        </ArkConnectDisabledAction>
                    )}

                    <ExternalLink
                        url={walletUri}
                        className="mt-2 w-full button-secondary"
                        iconClass="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-primary-400 dim:text-theme-dim-blue-300 dark:text-theme-dark-500"
                    >
                        {t('brands.arkvault')}
                    </ExternalLink>
                </div>
            )}

            {! arkconnect!.enabled && (
                <div>
                    <ExternalLink
                        url={walletUri}
                        className="w-full button-primary"
                        iconClass="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-primary-400 dim:text-theme-dim-blue-300 dark:text-theme-dark-blue-300 w-3 h-3"
                    >
                        {t('brands.arkvault')}
                    </ExternalLink>
                </div>
            )}
        </div>
    )
}

function QRCodeContent({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const [showOptions, setShowOptions] = useState(false);
    const { arkconnect, network } = useConfig();
    const [amount, setAmount] = useState<number | undefined>(undefined);

    const urlBuilder = new URLBuilder(arkconnect!.vaultUrl);
    urlBuilder.setNethash(network!.nethash);
    urlBuilder.setCoin(network!.coin);

    const walletUri = urlBuilder.generateTransfer(wallet.address, {
        amount: amount !== undefined && amount > 0 ? amount : undefined,
    });

    return (
        <DropdownPopup
            title={t('pages.wallet.qrcode.title')}
            width="w-[calc(100vw-1rem)] sm:max-w-[320px]"
            button={
                <div className="p-2 w-full focus-visible:ring-inset button button-secondary button-icon">
                    <QRCodeIcon className="w-4 h-4" />
                </div>
            }
            onClosed={() => setTimeout(() => setShowOptions(false))}
        >
            {showOptions && (
                <>
                    <div className="font-normal text-theme-secondary-700 leading-5.25 dark:text-theme-dark-200">
                        {t('pages.wallet.qrcode.description')}
                    </div>

                    <div className="py-4">
                        <Input
                            type="number"
                            id="amount"
                            name="amount"
                            maxLength={17}
                            className="font-normal"
                            inputClass="qr-code-amount"
                            label={t('pages.wallet.qrcode.currency_amount', { currency: network!.currency })}
                            value={amount}
                            onChange={(e) => setAmount(e.target.value ? parseFloat(e.target.value) : undefined)}
                            onWheel={(e) => {
                                const hasFocus = document.activeElement === e.target;
                                (e.target as HTMLInputElement).blur();
                                e.stopPropagation();

                                if (hasFocus) {
                                    setTimeout(() => {
                                        (e.target as HTMLInputElement).focus()
                                    }, 0);
                                }
                            }}
                            autoFocus
                        />
                    </div>
                </>
            )}

            <div className="flex justify-center">
                <div className="inline-block p-2 bg-white rounded-lg border sm:block border-theme-secondary-300 dark:border-theme-dark-300">
                    <QRCode
                        value={walletUri}
                        size={224}
                    />
                </div>
            </div>

            {! showOptions && <button
                className="mt-3 w-full button-secondary"
                onClick={() => setShowOptions(true)}
            >
                {t('pages.wallet.qrcode.specify_amount')}
            </button>}

            {showOptions && (
                <div className="mt-4 font-normal text-theme-secondary-700 leading-5.25 dark:text-theme-dark-200">
                    {t('pages.wallet.qrcode.automatic_notice')}
                </div>
            )}

            <div className="flex items-center mt-3 space-x-3 w-full">
                <div className="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-dark-700"></div>

                <div className="font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                    {t('pages.wallet.qrcode.or_send_with')}
                </div>

                <div className="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-dark-700"></div>
            </div>

            {/* @TODO: handle arkconnect functionality - https://app.clickup.com/t/86dxxbq8r */}
            <ArkVaultButton
                hasAmount={false}
                isOnSameNetwork={true}
                walletUri={walletUri}
            />
        </DropdownPopup>
    );
}

export default function PageHeaderQRCodeModalAction({ wallet }: { wallet: IWallet }) {
    return (
        <DropdownProvider>
            <QRCodeContent wallet={wallet} />
        </DropdownProvider>
    )
}
