import { PageProps } from "@inertiajs/core";
import { Head, router } from "@inertiajs/react";
import { IFilters, IPaginatedResponse, ITabbedData } from "@/types";
import { IBlock, ITransaction } from "@/types/generated";
import { usePageMetadata } from "@/Components/General/Metadata";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import Overview from "@/Components/Wallet/Overview/Overview";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import ValidatedBlocksTableWrapper from "@/Components/Tables/Desktop/Wallet/ValidatedBlocks";
import ValidatedBlocksMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/ValidatedBlocks";
import { WalletProps } from "@/Pages/Wallet.contracts";
import WalletTransactionsTab from "./tabs/Transactions";
import VotersTableWrapper from "@/Components/Tables/Desktop/Wallet/Voters";
import VotersMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Voters";
import { IWallet } from "@/types/generated";
import Layout from "@/Layout";
import { useTabPolling } from "@/hooks/use-tab-polling";

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
    const { currentTab } = useTabs();

    useTabPolling((tab: string, callback?: CallableFunction) => {
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
    });

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
                <Overview wallet={wallet} />

                <PageHandlerProvider>
                    <WalletTabsWrapper transactions={transactions} blocks={blocks} voters={voters} filters={filters} />
                </PageHandlerProvider>
            </Layout>
        </>
    );
}
