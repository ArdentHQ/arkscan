import Layout from "@/Layout";
import HomeTransactionsTableWrapper from "@/Components/Home/TransactionsTable";
import HomeChartCard from "@/Components/Home/Chart/ChartCard";
import { PageProps } from "@inertiajs/core";
import { HomeProps } from "@/Pages/Home.contracts";
import { router } from "@inertiajs/react";
import { PropsWithChildren, useEffect, useRef } from "react";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";

function HomeTransactionsTab({ transactions }: Pick<HomeProps, "transactions">) {
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const { baseUrl } = useSharedData<HomeProps>();

    useEffect(() => {
        const pollTransactions = () => {
            router.get(
                baseUrl,
                {},
                {
                    only: ["transactions"],
                    preserveScroll: true,
                    preserveState: true,
                    replace: true,
                    showProgress: false,
                },
            );
        };

        const removeListener = router.on("success", () => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(pollTransactions, 10000);
        });

        pollTransactions();

        return () => {
            removeListener();

            if (!pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        };
    }, [baseUrl]);

    return <HomeTransactionsTableWrapper transactions={transactions} />;
}

function HomeTabs({ transactions }: Pick<HomeProps, "transactions">) {
    const { currentTab } = useTabs();

    return (
        <div id="home:tabs:content" className="scroll-mt-13 sm:scroll-mt-16 md:scroll-mt-[123px]">
            {currentTab === "transactions" && <HomeTransactionsTab transactions={transactions} />}

            {currentTab === "blocks" && <div />}
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

export default function HomeIndex({ transactions }: PageProps<HomeProps>) {
    return (
        <Layout>
            <div className="mt-6 pb-8 md:pb-6">
                <div className="mt-8 px-6 md:mx-auto md:max-w-7xl md:border-0 md:px-10">
                    <div className="flex flex-col space-y-3 lg:flex-row lg:space-x-3 lg:space-y-0">
                        <HomeChartCard />
                    </div>
                </div>

                <HomeTabsProvider>
                    <PageHandlerProvider>
                        <HomeTabs transactions={transactions} />
                    </PageHandlerProvider>
                </HomeTabsProvider>
            </div>
        </Layout>
    );
}
