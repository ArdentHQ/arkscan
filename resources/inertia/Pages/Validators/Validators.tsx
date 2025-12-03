import { Head } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { usePageMetadata } from "@/Components/General/Metadata";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";

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
            </Layout>
        </>
    );
}
