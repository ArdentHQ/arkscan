import { useConfig } from "@/Providers/Config/ConfigContext";
import Tippy from "@tippyjs/react";
import { useTranslation } from "react-i18next";

export default function ArkConnectDisabledAction({
    isConnected,
    isOnSameNetwork,
    children,
}: {
    isConnected: boolean;
    isOnSameNetwork: boolean;
    children: React.ReactNode;
}) {
    const { t } = useTranslation();
    const { network } = useConfig();

    return (
        <>
            {isConnected && ! isOnSameNetwork && (
                <Tippy content={t(`general.arkconnect.wrong_network.${network!.alias}`)}>
                    <div>{children}</div>
                </Tippy>
            )}

            {! isConnected && (
                <Tippy content={t('general.arkconnect.connect_wallet_tooltip')}>
                    <div>{children}</div>
                </Tippy>
            )}
        </>
    );
}
