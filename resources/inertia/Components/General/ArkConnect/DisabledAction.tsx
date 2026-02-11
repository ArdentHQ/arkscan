import useSharedData from "@/hooks/use-shared-data";
import { useTranslation } from "react-i18next";
import Tooltip from "../Tooltip";

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
    const { network } = useSharedData();

    return (
        <>
            {isConnected && !isOnSameNetwork && (
                <Tooltip content={t(`general.arkconnect.wrong_network.${network!.alias}`)}>
                    <div>{children}</div>
                </Tooltip>
            )}

            {!isConnected && (
                <Tooltip content={t("general.arkconnect.connect_wallet_tooltip")}>
                    <div>{children}</div>
                </Tooltip>
            )}
        </>
    );
}
