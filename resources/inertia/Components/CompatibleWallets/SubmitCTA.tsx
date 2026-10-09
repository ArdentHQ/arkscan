import SubmitCTA from "../Resources/SubmitCTA";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";

export default function CompatibleWalletsSubmitCTA() {
    const { t } = useTranslation();
    const { contactEmail } = useSharedData();

    const subject = encodeURIComponent(t("pages.compatible-wallets.submit_email_subject"));

    return (
        <SubmitCTA
            title={t("pages.compatible-wallets.dont_see_a_wallet")}
            subtitle={t("pages.compatible-wallets.let_us_know")}
            button={t("actions.submit_wallet")}
            href={`mailto:${contactEmail}?subject=${subject}`}
        />
    );
}
