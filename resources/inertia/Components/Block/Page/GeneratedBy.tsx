import { useTranslation } from "react-i18next";
import { IBlockDetails } from "@/types/generated";
import { Link } from "@inertiajs/react";
import PageSection from "./PageSection";
import SectionDetailRow from "./SectionDetailRow";
import TruncateMiddle from "@/Components/General/TruncateMiddle";

export default function GeneratedBy({ block }: { block: IBlockDetails }) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("pages.block.generated_by")}>
            <SectionDetailRow title={t("pages.block.header.validator")}>
                <Link href={route("wallet", block.validatorAddress)} className="link font-semibold">
                    {block.validatorHasUsername ? (
                        <span>{block.validatorUsername}</span>
                    ) : (
                        <>
                            <span className="hidden md:inline">{block.validatorAddress}</span>
                            <span className="md:hidden">
                                <TruncateMiddle>{block.validatorAddress}</TruncateMiddle>
                            </span>
                        </>
                    )}
                </Link>
            </SectionDetailRow>
        </PageSection>
    );
}
