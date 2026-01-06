import Layout from "@/Layout";
import HomeTransactionsTableWrapper from "@/Components/Home/TransactionsTable";
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

    useEffect(() => {
        const pollTransactions = () => {
            router.reload({
                only: ["transactions"],
            });
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
    }, []);

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
                <HomeTabsProvider>
                    <PageHandlerProvider>
                        <HomeTabs transactions={transactions} />
                    </PageHandlerProvider>
                </HomeTabsProvider>
            </div>
        </Layout>
    );
}
