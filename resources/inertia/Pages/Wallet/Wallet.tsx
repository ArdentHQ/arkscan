import { router } from "@inertiajs/react";
import { IBlock, ITransaction } from "@/types/generated";
import { IFilters, IPaginatedResponse, ITabbedData } from "@/types";
import { PropsWithChildren, useEffect } from "react";
import { useTranslation } from "react-i18next";

import { ITabsQueryString } from "@/Providers/Tabs/types";
import { IWallet } from "@/types/generated";
import { Wallet as WalletModel } from "@/models/Wallet";
import Layout from "@/Layout";
import Overview from "@/Components/Wallet/Overview/Overview";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { CancelToken, PageProps } from "@inertiajs/core";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import ValidatedBlocksMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/ValidatedBlocks";
import ValidatedBlocksTableWrapper from "@/Components/Tables/Desktop/Wallet/ValidatedBlocks";
import VotersMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Voters";
import VotersTableWrapper from "@/Components/Tables/Desktop/Wallet/Voters";
import { WalletProps } from "@/Pages/Wallet.contracts";
import WalletTransactionsTab from "./tabs/Transactions";
import useSharedData from "@/hooks/use-shared-data";
import { useTabPolling } from "@/hooks/use-tab-polling";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";
import TokenTransfersTableWrapper from "@/Components/Tables/Desktop/Wallet/TokenTransfers";
import TokenTransfersMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/TokenTransfers";
import TokensTableWrapper from "@/Components/Tables/Desktop/Wallet/Tokens";
import TokensMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Tokens";

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
    const { listen } = useWebhooks();
    const { wallet } = useSharedData<WalletProps>();

    const { currentTab } = useTabs();

    useTabPolling((tab: string, callback?: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => {
        let pollParameters: string[] = [];
        if (tab === "transactions") {
            pollParameters = ["transactions"];
        } else if (tab === "token-transfers") {
            pollParameters = ["tokenTransfers"];
        } else if (tab === "tokens") {
            pollParameters = ["tokens"];
        } else if (tab === "blocks") {
            pollParameters = ["blocks"];
        } else if (tab === "voters") {
            pollParameters = ["voters"];
        }

        router.reload({
            only: pollParameters,
            onCancelToken,
            onSuccess: () => {
                if (callback) {
                    callback();
                }
            },
        });
    });

    const reloadData = (only: string) => {
        router.reload({
            only: [only],
        });
    };

    useEffect(() => {
        let callback: (() => void) | null = null;
        if (currentTab === "transactions") {
            callback = () => reloadData("transactions");
        } else if (currentTab === "tokens") {
            callback = () => reloadData("tokens");
        } else if (currentTab === "token-transfers") {
            callback = () => reloadData("tokenTransfers");
        }

        if (!callback) {
            return;
        }

        return listen(`transactions.${wallet.address}`, "NewTransaction", callback);
    }, [wallet.address, currentTab]);

    useEffect(() => {
        let callback: (() => void) | null = null;
        if (currentTab === "transactions") {
            callback = () => reloadData("transactions");
        } else if (currentTab === "tokens") {
            callback = () => reloadData("tokens");
        } else if (currentTab === "token-transfers") {
            callback = () => reloadData("tokenTransfers");
        }

        if (!callback) {
            return;
        }

        return listen(`transactions.${wallet.public_key}`, "NewTransaction", callback);
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

    return (
        <div id="wallet:tabs:content" className="scroll-mt-13 sm:scroll-mt-16 md:scroll-mt-[123px]">
            {currentTab === "transactions" && (
                <WalletTransactionsTab transactions={transactions} filters={filters.transactions} />
            )}

            {currentTab === "token-transfers" && (
                <>
                    <TokenTransfersTableWrapper mobile={<TokenTransfersMobileTableWrapper />} />
                </>
            )}

            {currentTab === "tokens" && (
                <>
                    <TokensTableWrapper mobile={<TokensMobileTableWrapper />} />
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

            {currentTab === "voters" && (
                <>
                    <VotersTableWrapper voters={voters} mobile={<VotersMobileTableWrapper voters={voters} />} />
                </>
            )}
        </div>
    );
};

function WalletPageHandlerProvider({ children }: PropsWithChildren) {
    const { t } = useTranslation();
    const { baseUrl, wallet } = useSharedData<WalletProps>();
    const walletModel = WalletModel.from(wallet);
    const tabs = [
        { text: "Transactions", value: "transactions" },
        { text: "Token Transfers", value: "token-transfers" },
        { text: "Tokens", value: "tokens" },
    ];
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

        "token-transfers": {
            page: 1,
            "per-page": 25,
        },

        tokens: {
            page: 1,
            "per-page": 25,
        },
    };

    if (walletModel.isValidator) {
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
            baseUrl={baseUrl}
            ariaLabel={t("pages.wallet.title")}
        >
            <PageHandlerProvider>{children}</PageHandlerProvider>
        </TabsProvider>
    );
}

export default function Wallet({ transactions, blocks, voters, filters }: PageProps<WalletProps>) {
    return (
        <Layout>
            <WalletPageHandlerProvider>
                <WalletTabs transactions={transactions} blocks={blocks} voters={voters} filters={filters} />
            </WalletPageHandlerProvider>
        </Layout>
    );
}
