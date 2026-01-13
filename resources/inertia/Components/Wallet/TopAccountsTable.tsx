import { router } from "@inertiajs/react";
import { useEffect } from "react";
import { TopAccountsProps } from "@/Pages/TopAccounts.contracts";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import TopAccountsTableWrapper from "@/Components/Tables/Desktop/Wallet/TopAccounts";
import TopAccountsMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/TopAccounts";

export default function TopAccountsTable({ wallets }: Pick<TopAccountsProps, "wallets">) {
    const { setRefreshPage } = usePageHandler();

    const updateTable = (callback?: CallableFunction) => {
        router.reload({
            only: ["wallets"],
            onSuccess: () => {
                if (callback) {
                    callback();
                }
            },
        });
    };

    useEffect(() => {
        updateTable();

        setRefreshPage((callback: CallableFunction) => {
            updateTable(callback);
        });
    }, []);

    return (
        <TopAccountsTableWrapper wallets={wallets} mobile={<TopAccountsMobileTableWrapper wallets={wallets} />} />
    );
}
