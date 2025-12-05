import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "@/Components/Tables/Mobile/TableCell";
import { MobileTransactionsSkeletonTable } from "../Skeleton/Wallet/Transactions";
import { IValidator } from "@/types/generated";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { ValidatorsHeaderActions } from "@/Components/Tables/Desktop/Validators/Validators";
import Number from "@/Components/General/Number";
import Identity from "@/Components/General/Identity";
import VoteLink from "@/Components/Validator/VoteLink";
import ValidatorStatus from "@/Components/Validator/ValidatorStatus";
import Votes from "@/Components/Validator/Votes";
import Percentage from "@/Components/General/Percentage";
import MissedBlocks from "@/Components/Validator/MissedBlocks";
import { useArkConnect } from "@/Providers/ArkConnect/ArkConnectContext";
import CheckMarkBoxIcon from "@ui/icons/check-mark-box.svg?react";

export function ValidatorsMobileTable({ validators }: Pick<ValidatorsProps, "validators">) {
    const { t } = useTranslation();
    const { network, arkconnectConfig } = useSharedData();
    const { votingForAddress } = useArkConnect();

    console.log({ votingForAddress, address: validators.data[0].address });

    return (
        <MobileTable noResultsMessage={validators.noResultsMessage} resultCount={validators.data.length ?? 0}>
            {validators.data.map((validator: IValidator, index) => (
                <MobileTableRow
                    key={index}
                    header={
                        <>
                            <div className="flex min-w-0 flex-1 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                                <Number className="min-w-[32px]">{validator.rank ?? 0}</Number>

                                <div className="flex min-w-0 flex-1 items-center justify-between pl-3">
                                    <Identity
                                        model={validator}
                                        className="w-full min-w-0"
                                        contentClassName="min-w-0 w-full"
                                        linkClassName="pr-2 min-w-0"
                                    />

                                    <div className="flex items-center">
                                        <span className="hidden sm:block">
                                            <ValidatorStatus validator={validator} />
                                        </span>

                                        <div className="border-theme-secondary-300 dark:border-theme-dark-700 sm:ml-3 sm:border-l sm:pl-3">
                                            <VoteLink wallet={validator} />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </>
                    }
                    expandable
                    expandClass={
                        !validator.isResigned
                            ? "space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700"
                            : ""
                    }
                    contentClass={arkconnectConfig.enabled ? "!pb-0 sm:!pb-3" : ""}
                >
                    <TableCell label={t("tables.validators.status")}>
                        <div className="inline-block">
                            <ValidatorStatus validator={validator} />
                        </div>
                    </TableCell>

                    <TableCell label={t("tables.validators.no_of_voters")} className="sm:hidden">
                        <Number>{validator.voterCount}</Number>
                    </TableCell>

                    <TableCell label={t("tables.validators.votes", { currency: network?.currency })}>
                        <Votes validator={validator} />
                    </TableCell>

                    <TableCell label={t("tables.validators.percentage")}>
                        <Percentage>{validator.votesPercentage}</Percentage>
                    </TableCell>

                    <TableCell label={t("tables.validators.missed_blocks")}>
                        <div className="inline-block">
                            <MissedBlocks validator={validator} />
                        </div>
                    </TableCell>

                    {arkconnectConfig.enabled && (
                        <TableCell className="sm:hidden">
                            {votingForAddress === validator.address && (
                                <div className="-mx-3 mb-1 flex items-center space-x-2 bg-theme-secondary-200 p-3 dark:bg-theme-dark-800 dark:text-theme-dark-200">
                                    <div>
                                        <CheckMarkBoxIcon className="h-4 w-4" />
                                    </div>

                                    <div className="font-semibold">
                                        {t("pages.validators.arkconnect.voting_for_tooltip")}
                                    </div>
                                </div>
                            )}
                        </TableCell>
                    )}
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export default function ValidatorsMobileTableWrapper({
    validators,
    rowCount = 10,
}: Pick<ValidatorsProps, "validators"> & {
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();

    if (!validators || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0}>
                    <ValidatorsHeaderActions />
                </TableHeaderWrapper>

                <MobileTransactionsSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <ValidatorsMobileTable validators={validators} />
        </div>
    );
}
