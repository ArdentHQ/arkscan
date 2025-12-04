import { Head, router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { usePageMetadata } from "@/Components/General/Metadata";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import HeaderStats from "@/Components/Validator/HeaderStats";
import { ValidatorsProps } from "../Validators.contracts";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useTabPolling } from "@/hooks/use-tab-polling";

const ValidatorsTabsWrapper = () => {
    return (
        <TabsProvider
            defaultSelected="validators"
            queryStringDefaults={{
                validators: {
                    page: 1,
                    "per-page": 25,
                },
                "missed-blocks": {
                    page: 1,
                    "per-page": 25,
                },
                "recent-votes": {
                    page: 1,
                    "per-page": 25,
                },
            }}
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

export default function Validators({ statistics, network }: PageProps<ValidatorsProps>) {
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

                <HeaderStats statistics={statistics} />

                <PageHandlerProvider>
                    <ValidatorsTabsWrapper />
                </PageHandlerProvider>
            </Layout>
        </>
    );
}
