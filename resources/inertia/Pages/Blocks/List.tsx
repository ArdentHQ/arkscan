import { router, usePoll } from "@inertiajs/react";

import Layout from "@/Layout";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { BlocksListProps } from "../Blocks.contracts";
import { useTranslation } from "react-i18next";
import HeaderStats from "@/Components/Blocks/HeaderStats";
import useShareData from "@/hooks/use-shared-data";
import BlocksListTableWrapper from "@/Components/Tables/Desktop/Blocks/List";
import BlocksListMobileTableWrapper from "@/Components/Tables/Mobile/Blocks/List";
import MobileDivider from "@/Components/General/MobileDivider";
import { useEffect } from "react";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";

function TableWrapper() {
    const { setRefreshPage } = usePageHandler();

    const updateTable = (callback?: CallableFunction) => {
        router.reload({
            only: ["blocks"],
            onSuccess: () => {
                if (callback) {
                    callback();
                }
            },
        });
    };

    useEffect(() => {
        updateTable();

        setRefreshPage((callback: CallableFunction) => {
            updateTable(callback);
        });
    }, []);

    return <BlocksListTableWrapper mobile={<BlocksListMobileTableWrapper />} />;
}

export default function List({ statistics }: PageProps<BlocksListProps>) {
    const { t } = useTranslation();
    const { network } = useShareData();
    const { listen, enabled: usesBroadcasting } = useWebhooks();

    useEffect(() => {
        router.reload({
            only: ["blocks"],
        });

        return listen("blocks", "NewBlock", () => {
            router.reload({
                only: ["blocks"],
            });
        });
    }, []);

    // secs: 
    usePoll(30 * 1000, {
        only: ["blocks"],
    }, {
        autoStart: !usesBroadcasting,
    });

    return (
        <Layout>
            <PageHeader
                title={t("pages.blocks.title")}
                subtitle={t("pages.blocks.subtitle", { network: network.name })}
            />

            <HeaderStats statistics={statistics} />

            <MobileDivider className="mb-6" />

            <PageHandlerProvider>
                <TableWrapper />
            </PageHandlerProvider>
        </Layout>
    );
}
