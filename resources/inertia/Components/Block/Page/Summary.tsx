import { useTranslation } from "react-i18next";
import { IBlockDetails } from "@/types/generated";
import PageSection from "./PageSection";
import SectionDetailRow from "./SectionDetailRow";
import { NetworkCurrency } from "@/Components/General/NetworkCurrency";

export default function BlockSummary({ block }: { block: IBlockDetails }) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("pages.block.block_summary")}>
            <SectionDetailRow title={t("pages.block.header.block_reward")}>
                <NetworkCurrency value={String(block.reward)} />
            </SectionDetailRow>

            <SectionDetailRow title={t("pages.block.header.total_fees")}>
                <NetworkCurrency value={String(block.fee)} />
            </SectionDetailRow>
        </PageSection>
    );
}
