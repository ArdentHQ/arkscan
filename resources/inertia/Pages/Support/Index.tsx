import Layout from "@/Layout";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { useTranslation } from "react-i18next";
import InformationCard from "@/Components/Contact/InformationCard";
import useShareData from "@/hooks/use-shared-data";
import { SupportProps } from "../Support.contracts";
import SupportForm from "@/Components/Contact/SupportForm";
import MobileDivider from "@/Components/General/MobileDivider";

export default function SupportIndex() {
    const { t } = useTranslation();
    const { socialNetworkUrls, subjects } = useShareData<SupportProps>();

    return (
        <Layout>
            <PageHeader title={t("pages.support.title")} subtitle={t("pages.support.description")} />

            <MobileDivider />

            <div className="mx-auto flex max-w-7xl flex-col dark:text-theme-dark-200 md:px-10 lg:flex-row">
                <InformationCard socialNetworkUrls={socialNetworkUrls} />

                <MobileDivider />

                <SupportForm subjects={subjects} />
            </div>
        </Layout>
    );
}
