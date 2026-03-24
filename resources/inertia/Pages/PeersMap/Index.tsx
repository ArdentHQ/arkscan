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
                <div className="overflow-hidden rounded-xl">
                    <WorldMap peers={peers} />
                </div>

                <div className="mt-4 text-right">
                    <span className="text-sm text-theme-secondary-500">
                        {peers.length} {peers.length === 1 ? "peer" : "peers"}
                    </span>
                </div>
            </div>
        </Layout>
    );
}
