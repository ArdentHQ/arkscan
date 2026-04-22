import { IPeer } from "@/types/generated";
import Layout from "@/Layout";
import MobileDivider from "@/Components/General/MobileDivider";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import WorldMap from "@/Components/NetworkMap/WorldMap";
import { useTranslation } from "react-i18next";

interface NetworkMapProps {
    peers: IPeer[];
}

export default function NetworkMapIndex({ peers }: PageProps<NetworkMapProps>) {
    const { t } = useTranslation();

    return (
        <Layout>
            <PageHeader title={t("pages.peers-map.title")} subtitle={t("pages.peers-map.subtitle")} />

            <MobileDivider className="mb-6" />

            <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">
                <div className="border-theme-secondary-300 dark:border-theme-dark-700 rounded border px-6 pt-4 pb-6 md:rounded-xl">
                    <div className="mb-3 flex items-center">
                        <span className="bg-theme-secondary-200 dark:border-theme-dark-900 dark:bg-theme-dark-950 flex flex-1 items-center gap-2 rounded-lg border-2 border-white px-3 py-2 text-sm sm:flex-none">
                            <div className="flex flex-1 items-center gap-2 sm:flex-none">
                                <span className="bg-theme-primary-600 dark:bg-theme-dark-blue-600 dark:outline-theme-dark-900 h-2 w-2 rounded-full outline outline-2 outline-white" />

                                <span className="text-theme-secondary-700 dark:text-theme-dark-200 text-sm font-semibold">
                                    {t("pages.peers-map.active_peers")}
                                </span>
                            </div>

                            <span className="text-theme-secondary-900 dark:text-theme-dark-50 text-sm font-semibold">
                                {peers.length}
                            </span>
                        </span>
                    </div>

                    <div className="border-theme-secondary-300 dark:border-theme-dark-700 overflow-hidden rounded-lg border">
                        <WorldMap peers={peers} />
                    </div>
                </div>
            </div>
        </Layout>
    );
}
