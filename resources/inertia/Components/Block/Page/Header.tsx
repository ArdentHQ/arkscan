import { useTranslation } from "react-i18next";
import { IBlockDetails } from "@/types/generated";
import PageHeaderContainer from "@/Components/PageHeader/Container";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import Clipboard from "@/Components/General/Clipboard";

function HeaderActions({ block }: { block: IBlockDetails }) {
    const { t } = useTranslation();

    return (
        <Clipboard
            value={block.hash}
            className="button-secondary group flex h-auto w-full items-center p-2 focus-visible:ring-inset"
            wrapperClass="flex-1"
            tooltipContent={t("pages.block.block_id_copied")}
            withCheckmarks
            checkmarksClass="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
            testId="block:copy-id"
        />
    );
}

export default function BlockHeader({ block }: { block: IBlockDetails }) {
    const { t } = useTranslation();

    return (
        <PageHeaderContainer label={t("pages.block.block_id")} extra={<HeaderActions block={block} />}>
            <TruncateDynamic value={block.hash} />
        </PageHeaderContainer>
    );
}
