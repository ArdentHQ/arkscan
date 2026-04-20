import { useTranslation } from "react-i18next";
import { IBlock } from "@/types/generated";
import { Link } from "@inertiajs/react";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import TruncateMiddle from "@/Components/General/TruncateMiddle";

export default function GeneratedBy({ block }: { block: IBlock }) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("pages.block.generated_by")}>
            <SectionDetailRow title={t("pages.block.header.validator")}>
                <Link href={route("wallet", block.proposer.address)} className="link font-semibold">
                    {block.proposer.hasUsername ? (
                        <span>{block.proposer.username}</span>
                    ) : (
                        <>
                            <span className="hidden md:inline">{block.proposer.address}</span>
                            <span className="md:hidden">
                                <TruncateMiddle>{block.proposer.address}</TruncateMiddle>
                            </span>
                        </>
                    )}
                </Link>
            </SectionDetailRow>
        </PageSection>
    );
}
