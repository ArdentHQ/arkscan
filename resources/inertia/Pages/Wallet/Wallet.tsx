import { PageProps } from "@inertiajs/core";
import { Head, router } from "@inertiajs/react";
import { PropsWithChildren, useEffect, useRef } from "react";
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
import { ITab, ITabsQueryString } from "@/Providers/Tabs/types";
import { WalletProps } from "@/Pages/Wallet.contracts";
import WalletTransactionsTab from "./tabs/Transactions";
import VotersTableWrapper from "@/Components/Tables/Desktop/Wallet/Voters";
import VotersMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Voters";
import { IWallet } from "@/types/generated";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";
import useSharedData from "@/hooks/use-shared-data";
import Layout from "@/Layout";

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
    return <WalletTabs transactions={transactions} blocks={blocks} voters={voters} filters={filters} />;
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

    const { listen } = useWebhooks();
    const { wallet } = useSharedData<WalletProps>();

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

    const reloadTransactions = () => {
        router.reload({
            only: ["transactions"],
        });
    };

    useEffect(() => {
        if (currentTab !== "transactions") {
            return;
        }

        return listen(`transactions.${wallet.address}`, "NewTransaction", reloadTransactions);
    }, [wallet.address, currentTab]);

    useEffect(() => {
        if (currentTab !== "transactions") {
            return;
        }

        return listen(`transactions.${wallet.public_key}`, "NewTransaction", reloadTransactions);
    }, [wallet.public_key, currentTab]);

    useEffect(() => {
        if (currentTab !== "blocks") {
            return;
        }

        return listen(`blocks.${wallet.public_key}`, "NewBlock", () => {
            router.reload({
                only: ["blocks"],
            });
        });
    }, [wallet.public_key, currentTab]);

    useEffect(() => {
        if (currentTab !== "voters") {
            return;
        }

        return listen(`wallet-vote.${wallet.public_key}`, "WalletVote", () => {
            router.reload({
                only: ["voters"],
            });
        });
    }, [wallet.public_key, currentTab]);

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

        onTabChange((tab: ITab, isFirstLoad: boolean) => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(() => pollCurrentTab(tab.value), 8000);

            if (isFirstLoad) {
                pollCurrentTab(tab.value);
            }
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
        <div id="wallet:tabs:content">
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
        </div>
    );
};

function WalletPageHandlerProvider({ children, wallet }: PropsWithChildren<{ wallet: IWallet }>) {
    const tabs = [{ text: "Transactions", value: "transactions" }];
    const queryStringDefaults: ITabsQueryString = {
        transactions: {
            page: 1,
            "per-page": 25,
            outgoing: true,
            incoming: true,
            transfers: true,
            multipayments: true,
            votes: true,
            validator: true,
            username: true,
            contract_deployment: true,
            others: true,
        },
    };

    if (wallet.isValidator) {
        tabs.push({ text: "Validated Blocks", value: "blocks" });
        tabs.push({ text: "Voters", value: "voters" });

        queryStringDefaults["blocks"] = {
            page: 1,
            "per-page": 25,
        };

        queryStringDefaults["voters"] = {
            page: 1,
            "per-page": 25,
        };
    }

    return (
        <TabsProvider
            defaultSelected="transactions"
            queryStringDefaults={queryStringDefaults}
            tabs={tabs}
            header={<Overview wallet={wallet} />}
        >
            <PageHandlerProvider>{children}</PageHandlerProvider>
        </TabsProvider>
    );
}

export default function Wallet({ transactions, blocks, wallet, voters, network, filters }: PageProps<WalletProps>) {
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

            <Layout>
                <WalletPageHandlerProvider wallet={wallet}>
                    <WalletTabsWrapper transactions={transactions} blocks={blocks} voters={voters} filters={filters} />
                </WalletPageHandlerProvider>
            </Layout>
        </>
    );
}
