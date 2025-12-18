import { Head, router } from "@inertiajs/react";

import Layout from "@/Layout";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { PageProps } from "@inertiajs/core";
import { usePageMetadata } from "@/Components/General/Metadata";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { BlocksListProps } from "../Blocks.contracts";
import { useTranslation } from "react-i18next";
import HeaderStats from "@/Components/Blocks/HeaderStats";
import useShareData from "@/hooks/use-shared-data";
import BlocksListTableWrapper from "@/Components/Tables/Desktop/Blocks/List";
import BlocksListMobileTableWrapper from "@/Components/Tables/Mobile/Blocks/List";
import MobileDivider from "@/Components/General/MobileDivider";
import { useEffect } from "react";

export default function List({ statistics }: PageProps<BlocksListProps>) {
    const { t } = useTranslation();
    const { network } = useShareData();
    const metadata = usePageMetadata({
        page: "blocks",
        detail: {
            name: network.name,
        },
    });

    useEffect(() => {
        router.reload({
            only: ["blocks"],
        });
    }, []);

    return (
        <>
            <Head>{metadata}</Head>

            <Layout>
                <PageHeader
                    title={t("pages.blocks.title")}
                    subtitle={t("pages.blocks.subtitle", { network: network.name })}
                />

                <HeaderStats statistics={statistics} />

                <MobileDivider className="mb-6" />

                <PageHandlerProvider>
                    <BlocksListTableWrapper mobile={<BlocksListMobileTableWrapper />} />
                </PageHandlerProvider>
            </Layout>
        </>
    );
}
