import { IPeer } from "@/types/generated";
import Layout from "@/Layout";
import MobileDivider from "@/Components/General/MobileDivider";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import WorldMap from "./WorldMap";
import { useTranslation } from "react-i18next";

interface PeersMapProps {
    peers: IPeer[];
}

export default function PeersMapIndex({ peers }: PageProps<PeersMapProps>) {
    const { t } = useTranslation();

    return (
        <Layout>
            <PageHeader title={t("pages.peers-map.title")} subtitle={t("pages.peers-map.subtitle")} />

            <MobileDivider className="mb-6" />

            <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">
                <div className="rounded border border-theme-secondary-300 p-6 dark:border-theme-dark-700 md:rounded-xl">
                    <div className="mb-4 flex items-center">
                        <span className="inline-flex items-center gap-2 rounded-full border border-theme-secondary-300 px-3 py-1.5 text-sm dark:border-theme-dark-700">
                            <span className="h-2.5 w-2.5 rounded-full bg-theme-primary-600 dark:bg-theme-dark-blue-400" />

                            <span className="text-theme-secondary-700 dark:text-theme-dark-200">
                                {t("pages.peers-map.active_peers")}
                            </span>

                            <span className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                                {peers.length}
                            </span>
                        </span>
                    </div>

                    <div className="overflow-hidden rounded-lg">
                        <WorldMap peers={peers} />
                    </div>
                </div>
            </div>
        </Layout>
    );
}
