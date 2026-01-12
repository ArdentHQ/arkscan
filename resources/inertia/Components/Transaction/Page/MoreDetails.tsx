import { useState } from "react";
import { useTranslation } from "react-i18next";
import { ITransaction } from "@/types/generated";
import { PageSection, SectionDetailRow } from "@/Components/PageSection";
import { TransactionDetails } from "@/Pages/Transaction.contracts";
import { formatUnits } from "@/utils/UnitConverter";
import Number from "@/Components/General/Number";
import Badge from "@/Components/General/Badge";
import MobileTableRow from "@/Components/Tables/Mobile/Row";
import TableCell from "@/Components/Tables/Mobile/TableCell";
import TransactionCodeBlock from "./CodeBlock";

export default function TransactionMoreDetails({
    transaction,
    details,
    headerWidthClass,
}: {
    transaction: ITransaction;
    details: TransactionDetails;
    headerWidthClass: string;
}) {
    const { t } = useTranslation();
    const [isMobilePayloadExpanded, setIsMobilePayloadExpanded] = useState(false);
    const [isDesktopPayloadExpanded, setIsDesktopPayloadExpanded] = useState(false);

    const gasLimit = formatUnits(transaction.gas, "wei");
    const gasUsed = formatUnits(transaction.gas_used, "wei");
    const payload = details.payload;

    return (
        <>
            <PageSection
                title={t("pages.transaction.more_details")}
                className="mt-6 sm:hidden"
                wrapperClass="flex flex-1 flex-col space-y-3 whitespace-nowrap"
            >
                <MobileTableRow
                    header={<span className="font-semibold">{t("pages.transaction.gas_information")}</span>}
                    contentClass="!space-y-3 divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-dark-800"
                >
                    <TableCell label={t("pages.transaction.header.gas_limit")}>
                        <Number>{gasLimit}</Number>
                    </TableCell>

                    <TableCell label={t("pages.transaction.header.usage_by_transaction")} className="pt-3">
                        <Number>{gasUsed}</Number>
                    </TableCell>
                </MobileTableRow>

                <MobileTableRow
                    header={<span className="font-semibold">{t("pages.transaction.other_attributes")}</span>}
                >
                    <TableCell label={t("pages.transaction.header.position_in_block")}>
                        <Number>{transaction.transaction_index}</Number>
                    </TableCell>
                </MobileTableRow>

                {payload && (
                    <div className="w-full space-y-3">
                        {isMobilePayloadExpanded && (
                            <div className="w-full">
                                <TransactionCodeBlock payload={payload} />
                            </div>
                        )}

                        <button
                            type="button"
                            className="link border-b border-dashed border-theme-primary-500 leading-5 hover:border-theme-primary-700 hover:no-underline"
                            onClick={() => setIsMobilePayloadExpanded(!isMobilePayloadExpanded)}
                        >
                            {isMobilePayloadExpanded ? t("actions.hide") : t("actions.view_all")}
                        </button>
                    </div>
                )}
            </PageSection>

            <div className="hidden px-6 sm:block md:mx-auto md:max-w-7xl md:px-10">
                <div className="font-semibold leading-5 text-theme-secondary-900 dark:text-theme-dark-50">
                    {t("pages.transaction.more_details")}
                </div>

                <PageSection
                    className="!px-0"
                    wrapperContainerClass="max-w-full leading-7"
                    wrapperClass="flex flex-1 flex-col space-y-3 whitespace-nowrap"
                >
                    <div className="inline-block">
                        <Badge className="inline-block text-sm">{t("pages.transaction.gas_information")}</Badge>
                    </div>

                    <SectionDetailRow
                        title={t("pages.transaction.header.gas_limit")}
                        value={<Number>{gasLimit}</Number>}
                        allowEmpty
                        headerWidthClass={headerWidthClass}
                    />

                    <SectionDetailRow
                        title={t("pages.transaction.header.usage_by_transaction")}
                        value={<Number>{gasUsed}</Number>}
                        allowEmpty
                        headerWidthClass={headerWidthClass}
                    />
                </PageSection>

                <PageSection
                    className="!px-0"
                    wrapperContainerClass="max-w-full leading-7"
                    wrapperClass="flex flex-1 flex-col space-y-3 whitespace-nowrap"
                >
                    <div className="inline-block">
                        <Badge className="inline-block text-sm">{t("pages.transaction.other_attributes")}</Badge>
                    </div>

                    <SectionDetailRow
                        title={t("pages.transaction.header.position_in_block")}
                        value={<Number>{transaction.transaction_index}</Number>}
                        allowEmpty
                        headerWidthClass={headerWidthClass}
                    />
                </PageSection>

                {payload && (
                    <div>
                        {isDesktopPayloadExpanded && (
                            <PageSection
                                className="!px-0 sm:mt-2"
                                wrapperContainerClass="w-full"
                                wrapperClass="flex flex-1 flex-col space-y-3 whitespace-nowrap"
                            >
                                <TransactionCodeBlock payload={payload} />
                            </PageSection>
                        )}

                        <PageSection className="!px-0" wrapperContainerClass="max-w-full" noBorder>
                            <button
                                type="button"
                                className="link border-b border-dashed border-theme-primary-500 leading-5 hover:border-theme-primary-700 hover:no-underline"
                                onClick={() => setIsDesktopPayloadExpanded(!isDesktopPayloadExpanded)}
                            >
                                {isDesktopPayloadExpanded ? t("actions.hide") : t("actions.view_all")}
                            </button>
                        </PageSection>
                    </div>
                )}
            </div>
        </>
    );
}
