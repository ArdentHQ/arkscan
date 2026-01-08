import Layout from "@/Layout";
import HomeTransactionsTableWrapper from "@/Components/Home/TransactionsTable";
import HomeChartCard from "@/Components/Home/Chart/ChartCard";
import { PageProps } from "@inertiajs/core";
import { HomeProps } from "@/Pages/Home.contracts";
import { router } from "@inertiajs/react";
import { PropsWithChildren } from "react";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { useTabPolling } from "@/hooks/use-tab-polling";
import HomeBlocksTableWrapper from "@/Components/Home/BlocksTable";

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

export default function HomeIndex({ blocks, transactions }: PageProps<HomeProps>) {
    return (
        <Layout>
            <div className="mt-6 pb-8 md:pb-6">
                <div className="mt-8 px-6 md:mx-auto md:max-w-7xl md:border-0 md:px-10">
                    <div className="flex flex-col space-y-3 lg:flex-row lg:space-x-3 lg:space-y-0">
                        <div className="flex-1">{/* Stats */}</div>

                        <HomeChartCard />
                    </div>
                </div>

                <HomeTabsProvider>
                    <PageHandlerProvider>
                        <HomeTabs blocks={blocks} transactions={transactions} />
                    </PageHandlerProvider>
                </HomeTabsProvider>
            </div>
        </Layout>
    );
}
