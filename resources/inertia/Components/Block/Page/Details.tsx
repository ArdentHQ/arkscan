import { useTranslation } from "react-i18next";
import { IBlockDetails } from "@/types/generated";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import Number from "@/Components/General/Number";

export default function BlockDetails({ block }: { block: IBlockDetails }) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("pages.block.block_details")}>
            <SectionDetailRow title={t("pages.block.header.timestamp")} value={block.timestampFormatted} />

            <SectionDetailRow title={t("pages.block.header.height")}>
                <Number>{block.height}</Number>
            </SectionDetailRow>

            <SectionDetailRow title={t("pages.block.header.transactions")} value={block.transactionCount} />
        </PageSection>
    );
}
