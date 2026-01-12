import HomeChartCard from "@/Components/Home/Chart/ChartCard";
import ArkVaultCTA from "@/Components/Home/ArkVaultCTA";
import HomeBlocksTableWrapper from "@/Components/Home/BlocksTable";
import { HomeProps } from "@/Pages/Home.contracts";
import HomeTransactionsTableWrapper from "@/Components/Home/TransactionsTable";
import Layout from "@/Layout";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { PageProps } from "@inertiajs/core";
import { PropsWithChildren } from "react";
import Statistics from "@/Components/Home/Statistics";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { router } from "@inertiajs/react";
import useSharedData from "@/hooks/use-shared-data";
import { useTabPolling } from "@/hooks/use-tab-polling";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useTranslation } from "react-i18next";

function HomeTabs({ blocks, transactions }: Pick<HomeProps, "blocks" | "transactions">) {
    const { currentTab } = useTabs();

    useTabPolling((tab: string, callback?: CallableFunction) => {
        let pollParameters: string[] = [];
        if (tab === "transactions") {
            pollParameters = ["transactions"];
        } else if (tab === "blocks") {
            pollParameters = ["blocks"];
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
        <div id="home:tabs:content" className="scroll-mt-13 sm:scroll-mt-16 md:scroll-mt-[123px]">
            {currentTab === "transactions" && <HomeTransactionsTableWrapper transactions={transactions} />}

            {currentTab === "blocks" && <HomeBlocksTableWrapper blocks={blocks} />}
        </div>
    );
}

function HomeTabsProvider({ children }: PropsWithChildren) {
    const { t } = useTranslation();
    const { baseUrl } = useSharedData<HomeProps>();

    return (
        <TabsProvider
            defaultSelected="transactions"
            queryStringDefaults={{
                transactions: {
                    page: 1,
                    "per-page": 25,
                },
                blocks: {
                    page: 1,
                    "per-page": 25,
                },
            }}
            tabs={[
                { text: t("pages.home.transactions"), value: "transactions" },
                { text: t("pages.home.blocks"), value: "blocks" },
            ]}
            baseUrl={baseUrl}
            useQueryParam
        >
            {children}
        </TabsProvider>
    );
}

export default function HomeIndex({ blocks, statistics, transactions }: PageProps<HomeProps>) {
    return (
        <Layout>
            <Statistics statistics={statistics} />

            <div className="mt-6 pb-8 md:pb-6">
                <HomeTabsProvider>
                    <PageHandlerProvider>
                        <HomeTabs blocks={blocks} transactions={transactions} />
                    </PageHandlerProvider>
                </HomeTabsProvider>

                <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">
                    <ArkVaultCTA />
                </div>
            </div>
        </Layout>
    );
}
