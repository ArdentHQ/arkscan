import { Head, router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { usePageMetadata } from "@/Components/General/Metadata";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useTabPolling } from "@/hooks/use-tab-polling";

const ValidatorsTabsWrapper = () => {
    return (
        <TabsProvider
            defaultSelected="validators"
            tabs={[
                { text: "Validators", value: "validators" },
                { text: "Missed Blocks", value: "missed-blocks" },
                { text: "Recent Votes", value: "recent-votes" },
            ]}
        >
            <ValidatorsTabs />
        </TabsProvider>
    );
};

const ValidatorsTabs = () => {
    const { currentTab } = useTabs();

    useTabPolling((tab: string, callback?: CallableFunction) => {
        let pollParameters: string[] = [];
        if (tab === "validators") {
            pollParameters = ["validators"];
        } else if (tab === "missed-blocks") {
            pollParameters = ["missed-blocks"];
        } else if (tab === "recent-votes") {
            pollParameters = ["recent-votes"];
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
            {currentTab === "validators" && <>{/*  */}</>}

            {currentTab === "missed-blocks" && <>{/*  */}</>}

            {currentTab === "recent-votes" && <>{/*  */}</>}
        </>
    );
};

export default function Validators({ network }: PageProps) {
    const { t } = useTranslation();
    const metadata = usePageMetadata({
        page: "validators",
        detail: {
            name: network.name,
        },
    });

    return (
        <>
            <Head>{metadata}</Head>

            <Layout>
                <PageHeader title={t("pages.validators.title")} subtitle={t("pages.validators.subtitle")} />

                <PageHandlerProvider>
                    <ValidatorsTabsWrapper />
                </PageHandlerProvider>
            </Layout>
        </>
    );
}
