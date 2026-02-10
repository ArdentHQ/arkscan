import TransfersTableWrapper from "@/Components/Tables/Desktop/Tokens/Transfers";
import TransfersMobileTableWrapper from "@/Components/Tables/Mobile/Tokens/Transfers";
import { router } from "@inertiajs/react";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { useEffect } from "react";
import useSharedData from "@/hooks/use-shared-data";
import { CancelToken } from "@inertiajs/core";
import { TokenTransfersProps } from "@/Pages/Tokens.contracts";

export default function TransfersTable() {
    const { setRefreshPage } = usePageHandler();
    const { transfers } = useSharedData<TokenTransfersProps>();

    const updateTable = (callback?: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => {
        router.reload({
            only: ["transfers"],
            onCancelToken,
            onSuccess: () => {
                if (callback) {
                    callback();
                }
            },
        });
    };

    useEffect(() => {
        updateTable();

        setRefreshPage((callback: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => {
            updateTable(callback, onCancelToken);
        });
    }, []);

    return (
        <TransfersTableWrapper transfers={transfers} mobile={<TransfersMobileTableWrapper transfers={transfers} />} />
    );
}
