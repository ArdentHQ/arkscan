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
import ValidatorsTab from "./tabs/Validators";
import MissedBlocksTableWrapper from "@/Components/Tables/Desktop/Validators/MissedBlocks";
import MissedBlocksMobileTableWrapper from "@/Components/Tables/Mobile/Validators/MissedBlocks";

const ValidatorsTabsWrapper = ({
    missedBlocks,
    validators,
    filters,
    recentVotes,
}: Pick<ValidatorsProps, "missedBlocks" | "validators" | "filters" | "recentVotes">) => {
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
            <ValidatorsTabs
                missedBlocks={missedBlocks}
                validators={validators}
                filters={filters}
                recentVotes={recentVotes}
            />
        </TabsProvider>
    );
};

const ValidatorsTabs = ({
    missedBlocks,
    validators,
    filters,
    recentVotes,
}: Pick<ValidatorsProps, "missedBlocks" | "validators" | "filters" | "recentVotes">) => {
    const { currentTab } = useTabs();

    useTabPolling((tab: string, callback?: CallableFunction) => {
        let pollParameters: string[] = [];
        if (tab === "validators") {
            pollParameters = ["validators"];
        } else if (tab === "missed-blocks") {
            pollParameters = ["missedBlocks"];
        } else if (tab === "recent-votes") {
            pollParameters = ["recentVotes"];
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
            {currentTab === "validators" && <ValidatorsTab validators={validators} filters={filters} />}

            {currentTab === "missed-blocks" && (
                <MissedBlocksTableWrapper
                    blocks={missedBlocks}
                    mobile={<MissedBlocksMobileTableWrapper blocks={missedBlocks} />}
                />
            )}

            {currentTab === "recent-votes" && <>{/*  */}</>}
        </>
    );
};

export default function Validators({
    statistics,
    network,
    missedBlocks,
    validators,
    filters,
    recentVotes,
}: PageProps<ValidatorsProps>) {
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
                    <ValidatorsTabsWrapper
                        missedBlocks={missedBlocks}
                        validators={validators}
                        filters={filters}
                        recentVotes={recentVotes}
                    />
                </PageHandlerProvider>
            </Layout>
        </>
    );
}
