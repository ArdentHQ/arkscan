import { useTranslation } from "react-i18next";
import PageHeaderContainer from "@/Components/PageHeader/Container";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import Clipboard from "@/Components/General/Clipboard";
import { ITransaction } from "@/types/generated";
import BookmarkButton from "@/Components/General/BookmarkButton";

function HeaderActions({ hash }: { hash: string }) {
    const { t } = useTranslation();

    return (
        <>
            <Clipboard
                value={hash}
                className="button-secondary group flex h-8 w-full items-center p-2 focus-visible:ring-inset"
                wrapperClass="flex-1"
                tooltipContent={t("pages.transaction.transaction_id_copied")}
                withCheckmarks
                checkmarksClass="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
                testId="transaction:copy-id"
            >
                <div className="ml-2 sm:hidden">{t("actions.copy")}</div>
            </Clipboard>

            <BookmarkButton testId="transaction:bookmark" className="h-8 min-w-[62px] sm:min-w-0" />
        </>
    );
}

export default function TransactionHeader({ transaction }: { transaction: ITransaction }) {
    const { t } = useTranslation();

    return (
        <PageHeaderContainer
            label={t("pages.transaction.transaction_id")}
            breakpoint="sm"
            extra={<HeaderActions hash={transaction.hash} />}
        >
            <TruncateDynamic value={transaction.hash} />
        </PageHeaderContainer>
    );
}
