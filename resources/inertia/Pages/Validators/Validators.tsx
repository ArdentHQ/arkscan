import { Head } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { usePageMetadata } from "@/Components/General/Metadata";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";

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
                <p>General validators page</p>
                {/* <PageHeader
                    title={t("pages.validator-monitor.title")}
                    subtitle={t("pages.validator-monitor.subtitle")}
                />

                <HeaderStats height={height} statistics={validatorData?.statistics} />

                <ValidatorFavoritesProvider>
                    <MissedBlocksTrackerProvider
                        validators={[
                            ...(validatorData?.validators ?? []),
                            ...(validatorData?.overflowValidators ?? []),
                        ]}
                    >
                        <MonitorTableWrapper
                            validators={validatorData?.validators}
                            overflowValidators={validatorData?.overflowValidators}
                            rowCount={rowCount}
                        />

                        <MobileDivider />

                        <div className="px-6 pb-8 pt-6 md:mx-auto md:max-w-7xl md:px-10 md:pt-0">
                            <MonitorMobileTableWrapper validators={validatorData?.validators} rowCount={rowCount} />
                        </div>
                    </MissedBlocksTrackerProvider>
                </ValidatorFavoritesProvider> */}
            </Layout>
        </>
    );
}
