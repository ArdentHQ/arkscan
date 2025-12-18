import { Head, router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { usePageMetadata } from "@/Components/General/Metadata";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import HeaderStats from "@/Components/Validator/HeaderStats";
import { IValidatorsStatistics, ValidatorsProps } from "../Validators.contracts";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useTabPolling } from "@/hooks/use-tab-polling";
import ValidatorsTab from "./tabs/Validators";
import MissedBlocksTableWrapper from "@/Components/Tables/Desktop/Validators/MissedBlocks";
import MissedBlocksMobileTableWrapper from "@/Components/Tables/Mobile/Validators/MissedBlocks";
import RecentVotesTab from "./tabs/RecentVotes";
import useSharedData from "@/hooks/use-shared-data";
import { PropsWithChildren } from "react";

const ValidatorsTabsWrapper = ({
    missedBlocks,
    validators,
    filters,
    recentVotes,
}: Pick<ValidatorsProps, "missedBlocks" | "validators" | "filters" | "recentVotes">) => {
    return (
        <ValidatorsTabs
            missedBlocks={missedBlocks}
            validators={validators}
            filters={filters}
            recentVotes={recentVotes}
        />
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
        <div id="validators:tabs:content" className="scroll-mt-13 sm:scroll-mt-16 md:scroll-mt-[123px]">
            {currentTab === "validators" && <ValidatorsTab validators={validators} filters={filters} />}

            {currentTab === "missed-blocks" && (
                <MissedBlocksTableWrapper
                    blocks={missedBlocks}
                    mobile={<MissedBlocksMobileTableWrapper blocks={missedBlocks} />}
                />
            )}

            {currentTab === "recent-votes" && <RecentVotesTab recentVotes={recentVotes} filters={filters} />}
        </div>
    );
};

function ValidatorsPageHandlerProvider({ children }: PropsWithChildren) {
    const { baseUrl, statistics } = useSharedData<ValidatorsProps>();

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
            header={<HeaderStats statistics={statistics} />}
            baseUrl={baseUrl}
        >
            <PageHandlerProvider>{children}</PageHandlerProvider>
        </TabsProvider>
    );
}

export default function Validators({
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

                <ValidatorsPageHandlerProvider>
                    <ValidatorsTabsWrapper
                        missedBlocks={missedBlocks}
                        validators={validators}
                        filters={filters}
                        recentVotes={recentVotes}
                    />
                </ValidatorsPageHandlerProvider>
            </Layout>
        </>
    );
}
