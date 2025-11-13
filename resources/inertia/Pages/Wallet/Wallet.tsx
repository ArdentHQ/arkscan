import { PageProps } from "@inertiajs/core";
import { Head, router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import { IFilters, IPaginatedResponse, ITabbedData } from "@/types";
import { IBlock, ITransaction } from "@/types/generated";
import { usePageMetadata } from "@/Components/General/Metadata";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Overview from "@/Components/Wallet/Overview/Overview";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import ValidatedBlocksTableWrapper from "@/Components/Tables/Desktop/Wallet/ValidatedBlocks";
import ValidatedBlocksMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/ValidatedBlocks";
import { ITab } from "@/Providers/Tabs/types";
import { WalletProps } from "@/Pages/Wallet.contracts";
import WalletTransactionsTab from "./tabs/Transactions";
import VotersTableWrapper from "@/Components/Tables/Desktop/Wallet/Voters";
import VotersMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Voters";
import { IWallet } from "../../types/generated";
import useWebhookListener from "@/Providers/Webhooks/useWebhookListener";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";

const WalletTabsWrapper = ({
    transactions,
    blocks,
    voters,
    filters,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    blocks?: IPaginatedResponse<IBlock>;
    voters?: IPaginatedResponse<IWallet>;
    filters: ITabbedData<IFilters>;
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
            <WalletTabs transactions={transactions} blocks={blocks} voters={voters} filters={filters} />
        </TabsProvider>
    );
};

const WalletTabs = ({
    transactions,
    blocks,
    voters,
    filters,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    blocks?: IPaginatedResponse<IBlock>;
    voters?: IPaginatedResponse<IWallet>;
    filters: ITabbedData<IFilters>;
}) => {
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const { setRefreshPage } = usePageHandler();
    const { currentTab, onTabChange } = useTabs();
    

    const pollCurrentTab = (tab: string, callback?: CallableFunction) => {
        let pollParameters: string[] = [];
        if (tab === "transactions") {
            pollParameters = ["transactions"];
        } else if (tab === "blocks") {
            pollParameters = ["blocks"];
        } else if (tab === "voters") {
            pollParameters = ["voters"];
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

    useEffect(() => {
        if (!currentTab) {
            return;
        }

        router.on("success", () => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(() => pollCurrentTab(currentTab), 8000);
        });

        if (!pollingTimerRef.current) {
            pollingTimerRef.current = setTimeout(() => pollCurrentTab(currentTab), 8000);

            pollCurrentTab(currentTab);
        }

        onTabChange((tab: ITab) => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(() => pollCurrentTab(tab.value), 8000);

            pollCurrentTab(tab.value);
        });

        setRefreshPage((callback?: CallableFunction) => {
            pollCurrentTab(currentTab, callback);
        });

        return () => {
            if (!pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        };
    }, [currentTab]);

    return (
        <>
            {currentTab === "transactions" && (
                <WalletTransactionsTab transactions={transactions} filters={filters.transactions} />
            )}

            {currentTab === "blocks" && (
                <>
                    <ValidatedBlocksTableWrapper
                        blocks={blocks}
                        mobile={<ValidatedBlocksMobileTableWrapper blocks={blocks} />}
                    />
                </>
            )}

            {currentTab === "voters" && (
                <>
                    <VotersTableWrapper voters={voters} mobile={<VotersMobileTableWrapper voters={voters} />} />
                </>
            )}
        </>
    );
};

export default function Wallet({ transactions, blocks, wallet, voters, network, filters }: PageProps<WalletProps>) {
    const { listen } = useWebhooks();
    
    const metadata = usePageMetadata({
        page: "wallet",
        detail: {
            name: network.name,
            address: wallet.address,
        },
    });

    const reloadTransactions = () => {
        router.reload({
            only: ["transactions"],
        });
    };

    useEffect(() => listen(`transactions.${wallet.address}`, "NewTransaction", reloadTransactions), [wallet.address]);
    
    useEffect(() => listen(`transactions.${wallet.public_key}`, "NewTransaction", reloadTransactions), [wallet.public_key]);

    
    return (
        <>
            <Head>{metadata}</Head>

            <Overview wallet={wallet} />

            {/* <button onClick={reloadTransactions}>Reload Transactions</button> */}

            <PageHandlerProvider>
                <WalletTabsWrapper transactions={transactions} blocks={blocks} voters={voters} filters={filters} />
            </PageHandlerProvider>
        </>
    );
}
