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
                <div className="rounded border border-theme-secondary-300 px-6 pb-6 pt-4 dark:border-theme-dark-700 md:rounded-xl">
                    <div className="mb-3 flex items-center">
                        <span className="flex flex-1 items-center gap-2 rounded-lg border-2 border-white bg-theme-secondary-200 px-3 py-2 text-sm dark:border-theme-dark-900 dark:bg-theme-dark-950 sm:flex-none">
                            <div className="flex flex-1 items-center gap-2 sm:flex-none">
                                <span className="h-2 w-2 rounded-full bg-theme-primary-600 outline outline-2 outline-white dark:bg-theme-dark-blue-600 dark:outline-theme-dark-900" />

                                <span className="text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                                    {t("pages.peers-map.active_peers")}
                                </span>
                            </div>

                            <span className="text-sm font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                                {peers.length}
                            </span>
                        </span>
                    </div>

                    <div className="overflow-hidden rounded-lg border border-theme-secondary-300 dark:border-theme-dark-700">
                        <WorldMap peers={peers} />
                    </div>
                </div>
            </div>
        </Layout>
    );
}
