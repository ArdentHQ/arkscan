import { PageProps } from "@inertiajs/core";
import { Head, router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import { IPaginatedResponse } from "@/types";
import { IBlock, IWallet, ITransaction } from "@/types/generated";
import { usePageMetadata } from "@/Components/General/Metadata";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import TransactionsTableWrapper from "@/Components/Tables/Desktop/Wallet/Transactions";
import ConfigProvider from "@/Providers/Config/ConfigProvider";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Overview from "@/Components/Wallet/Overview/Overview";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import TransactionsMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Transactions";
import ValidatedBlocksTableWrapper from "@/Components/Tables/Desktop/Wallet/ValidatedBlocks";
import ValidatedBlocksMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/ValidatedBlocks";
import { ITab } from "@/Providers/Tabs/types";

const WalletTabsWrapper = ({
    transactions,
    blocks,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    blocks?: IPaginatedResponse<IBlock>;
}) => {
    return (
        <TabsProvider
            defaultSelected="transactions"
            tabs={[
                { text: "Transactions", value: "transactions" },
                { text: "Validated Blocks", value: "blocks" },
                { text: "Voters", value: "voters" },
            ]}
        >
            <WalletTabs transactions={transactions} blocks={blocks} />
        </TabsProvider>
    );
};

const WalletTabs = ({
    transactions,
    blocks,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    blocks?: IPaginatedResponse<IBlock>;
}) => {
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const { setRefreshPage } = usePageHandler();

    const { currentTab } = useTabs();

    useEffect(() => {
        router.on("success", () => {
            pollingTimerRef.current = setTimeout(pollCurrentTab, 8000);
        });

        const pollCurrentTab = (callback?: CallableFunction) => {
            let pollParameters: string[] = [];
            if (currentTab === "transactions") {
                pollParameters = ["transactions"];
            } else if (currentTab === "blocks") {
                pollParameters = ["blocks"];
            } else if (currentTab === "voting") {
                pollParameters = ["votes"];
            }

            router.reload({
                only: pollParameters,
                onSuccess: () => {
                    if (callback) {
                        callback();
                    }
                },
            });
        };

        pollCurrentTab();

        setRefreshPage((callback?: CallableFunction) => {
            pollCurrentTab(callback);
        });

        return () => {
            if (!pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        };
    }, []);

    return (
        <>
            {currentTab === "transactions" && (
                <>
                    <TransactionsTableWrapper
                        transactions={transactions}
                        mobile={<TransactionsMobileTableWrapper transactions={transactions} />}
                    />
                </>
            )}

            {currentTab === "blocks" && (
                <>
                    <ValidatedBlocksTableWrapper
                        blocks={blocks}
                        mobile={<ValidatedBlocksMobileTableWrapper blocks={blocks} />}
                    />
                </>
            )}
        </>
    );
};

export default function Wallet({
    transactions,
    blocks,
    wallet,
    network,
    ...props
}: PageProps<{
    transactions: IPaginatedResponse<ITransaction>;
    blocks?: IPaginatedResponse<IBlock>;
    wallet: IWallet;
}>) {
    const metadata = usePageMetadata({
        page: "wallet",
        detail: {
            name: network.name,
            address: wallet.address,
        },
    });

    return (
        <>
            <Head>{metadata}</Head>

            <ConfigProvider network={network} {...props}>
                <Overview wallet={wallet} />

                <PageHandlerProvider>
                    <WalletTabsWrapper transactions={transactions} blocks={blocks} />
                </PageHandlerProvider>
            </ConfigProvider>
        </>
    );
}
