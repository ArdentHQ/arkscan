import Layout from "@/Layout";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { useTranslation } from "react-i18next";
import InformationCard from "@/Components/Contact/InformationCard";
import useSharedData from "@/hooks/use-shared-data";
import { SupportProps } from "../Support.contracts";
import EmailContact from "@/Components/Contact/EmailContact";
import MobileDivider from "@/Components/General/MobileDivider";

export default function SupportIndex() {
    const { t } = useTranslation();
    const { socialNetworkUrls, contactEmail } = useSharedData<SupportProps>();

    return (
        <Layout>
            <PageHeader title={t("pages.support.title")} subtitle={t("pages.support.description")} />

            <MobileDivider />

            <div className="dark:text-theme-dark-200 mx-auto flex max-w-7xl flex-col md:px-10 lg:flex-row">
                <InformationCard socialNetworkUrls={socialNetworkUrls} />

                <MobileDivider />

                <EmailContact email={contactEmail} />
            </div>
        </Layout>
    );
}
