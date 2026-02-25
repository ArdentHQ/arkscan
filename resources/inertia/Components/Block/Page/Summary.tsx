import { useTranslation } from "react-i18next";
import { IBlock } from "@/types/generated";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import { NetworkCurrency } from "@/Components/General/NetworkCurrency";

export default function BlockSummary({ block }: { block: IBlock }) {
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
