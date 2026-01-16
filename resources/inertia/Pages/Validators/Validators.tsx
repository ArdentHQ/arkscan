import { router } from "@inertiajs/react";
import Layout from "@/Layout";
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
import RecentVotesTab from "./tabs/RecentVotes";
import useSharedData from "@/hooks/use-shared-data";
import { PropsWithChildren } from "react";
import { useTranslation } from "react-i18next";

const ValidatorsTabs = () => {
    const { currentTab } = useTabs();

    const { missedBlocks, validators, filters, recentVotes } = useSharedData<ValidatorsProps>();

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

export default function Validators() {
    const { t } = useTranslation();

    return (
        <Layout>
            <PageHeader title={t("pages.validators.title")} subtitle={t("pages.validators.subtitle")} />

            <ValidatorsPageHandlerProvider>
                <ValidatorsTabs />
            </ValidatorsPageHandlerProvider>
        </Layout>
    );
}
