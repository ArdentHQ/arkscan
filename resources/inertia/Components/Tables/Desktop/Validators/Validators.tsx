import TableCell from "../TableCell";
import LoadingTable from "../LoadingTable";
import { ITransaction, IValidator, IWallet } from "@/types/generated";
import { IPaginatedResponse } from "@/types";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Model/Age";
import ID from "@/Components/Transaction/ID";
import Amount from "@/Components/Transaction/Amount";
import Fee from "@/Components/Transaction/Fee";
import { Table } from "../Table";
import Method from "@/Components/Transaction/Method";
import Addressing from "@/Components/Transaction/Addressing";
import UnderlineArrowDownIcon from "@ui/icons/arrows/underline-arrow-down.svg?react";
import TableHeader from "../TableHeader";
import { useMemo, useState } from "react";
import ExportTransactionsModal from "../Wallet/ExportTransactionsModal";
import { WalletProps } from "@/Pages/Wallet.contracts";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Filter from "@/Components/Tables/Filter";
import useSharedData from "@/hooks/use-shared-data";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { useArkConnect } from "@/Providers/ArkConnect/ArkConnectContext";
import Tooltip from "@/Components/General/Tooltip";
import CheckMarkBoxIcon from "@ui/icons/check-mark-box.svg?react";
import Identity from "@/Components/General/Identity";
import Badge from "@/Components/General/Badge";
import Number from "@/Components/General/Number";
import { currencyWithDecimals } from "@/utils/number-formatter";
import Percentage from "@/Components/General/Percentage";
import classNames from "classnames";
import VoteLink from "@/Components/Validator/VoteLink";
import ExternalLink from "@/Components/General/ExternalLink";

export function ValidatorStatus({ validator }: { validator: IValidator }) {
    const { t } = useTranslation();

    const statusLabel = useMemo(() => {
        if (validator.isActive) {
            return t("general.validators.forging-status.active");
        }

        if (validator.isResigned) {
            return t("general.validators.forging-status.resigned");
        }

        if (validator.isDormant) {
            return t("general.validators.forging-status.dormant");
        }

        return t("general.validators.forging-status.standby");
    }, [validator.isActive, validator.isResigned, validator.isDormant, t]);

    return <Badge className="encapsulated-badge">{statusLabel}</Badge>;
}

export function Row({ row: validator }: { row: IValidator }) {
    const { arkconnectConfig, network } = useSharedData();
    const { votingForAddress } = useArkConnect();
    const { t } = useTranslation();

    const votes = validator.votes;

    return (
        <tr className="text-sm font-semibold">
            <TableCell>{<Number>{validator.rank ?? 0}</Number>}</TableCell>

            <TableCell>
                <div className="flex items-center space-x-2">
                    <Identity model={validator} />

                    {arkconnectConfig.enabled && votingForAddress === validator.address && (
                        <div>
                            <Tooltip content={t("pages.validators.arkconnect.voting_for_tooltip")}>
                                <CheckMarkBoxIcon className="h-4 w-4" />
                            </Tooltip>
                        </div>
                    )}
                </div>
            </TableCell>

            <TableCell>
                <ValidatorStatus validator={validator} />
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50">
                <div>
                    <Number>{validator.voterCount}</Number>

                    <div className="divide mt-1 hidden space-x-2 divide-x divide-theme-secondary-300 text-xs leading-3.75 text-theme-secondary-700 dark:divide-theme-dark-700 dark:text-theme-dark-200 sm:flex lg:hidden">
                        <div>
                            <Number>{validator.votes}</Number>
                        </div>

                        <div className="pl-2">
                            <Percentage>{validator.votesPercentage}</Percentage>
                        </div>
                    </div>
                </div>
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50" responsive>
                {votes > 0 && votes < 0.01 ? (
                    <Tooltip
                        content={currencyWithDecimals({
                            value: votes,
                            currency: network!.currency,
                            hideCurrency: true,
                        })}
                    >
                        <span>&lt;0.01</span>
                    </Tooltip>
                ) : (
                    <Number>{votes}</Number>
                )}
            </TableCell>

            <TableCell
                className="text-right text-theme-secondary-900 dark:text-theme-dark-50"
                responsive
                breakpoint="lg"
            >
                <Percentage>{validator.votesPercentage}</Percentage>
            </TableCell>

            <TableCell className="text-right">
                <Badge
                    className={classNames("min-w-[30px] text-center", {
                        "border-theme-success-100 bg-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:text-theme-success-500":
                            validator.missedBlocksState === "success",
                        "border-theme-orange-light bg-theme-orange-light text-theme-orange-dark dim:text-theme-warning-400 dark:!border-theme-warning-600 dark:text-theme-warning-400":
                            validator.missedBlocksState === "warning",
                        "border-theme-danger-100 bg-theme-danger-100 text-theme-danger-700 dim:border-theme-failed-state-bg dim:text-theme-failed-state-text dark:border-theme-failed-state-bg dark:text-theme-failed-state-text":
                            validator.missedBlocksState === "danger",
                        "encapsulated-badge border-transparent bg-theme-secondary-200 dark:border-theme-dark-800 dark:text-theme-dark-500":
                            validator.missedBlocksState === "inactive",
                    })}
                >
                    {validator.missedBlocks}
                </Badge>
            </TableCell>

            <TableCell className="text-right">
                {arkconnectConfig.enabled ? (
                    <VoteLink wallet={validator} />
                ) : (
                    <ExternalLink url={validator.voteUrl ?? ""} innerClass="text-sm" noIcon>
                        {t("actions.vote")}
                    </ExternalLink>
                )}
            </TableCell>
        </tr>
    );
}

export function ValidatorsTable({
    validators,
    mobile,
}: Pick<ValidatorsProps, "validators"> & {
    mobile?: React.ReactNode;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            withHeader
            withFooter
            paginator={validators}
            rowComponent={Row}
            mobile={mobile}
            headerActions={<ValidatorsHeaderActions />}
            noResultsMessage={validators.noResultsMessage}
            columns={
                <>
                    <TableHeader width="70">{t("tables.validators.rank")}</TableHeader>

                    <TableHeader>{t("tables.validators.validator")}</TableHeader>

                    <TableHeader>{t("tables.validators.status")}</TableHeader>

                    <TableHeader type="number" className="whitespace-nowrap">
                        {t("tables.validators.no_of_voters")}
                    </TableHeader>

                    <TableHeader type="number" responsive className="whitespace-nowrap">
                        {t("tables.validators.votes", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader type="number" responsive breakpoint="lg" className="!py-2.5">
                        {t("tables.validators.percentage")}
                    </TableHeader>

                    <TableHeader type="number" className="!py-2.5">
                        {t("tables.validators.missed_blocks")}
                    </TableHeader>

                    <TableHeader width="70">{""}</TableHeader>
                </>
            }
        />
    );
}

export default function ValidatorsTableWrapper({
    validators,
    mobile,
    rowCount = 20,
}: Pick<ValidatorsProps, "validators"> & {
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!validators || isLoading) {
        return (
            <LoadingTable
                mobile={mobile}
                paginator={validators}
                rowCount={rowCount}
                header={<ValidatorsHeaderActions />}
                columns={[
                    {
                        name: t("tables.validators.rank"),
                        type: "string",
                        className: "w-[70px]",
                    },
                    {
                        name: t("tables.validators.validator"),
                        type: "address",
                    },
                    {
                        name: t("tables.validators.status"),
                    },
                    {
                        name: t("tables.validators.no_of_voters"),
                        type: "number",
                        className: "whitespace-nowrap text-right",
                    },
                    {
                        name: t("tables.validators.votes", {
                            currency: network!.currency,
                        }),
                        type: "number",
                        className: "whitespace-nowrap text-right",
                        responsive: true,
                    },
                    {
                        name: t("tables.validators.percentage"),
                        type: "number",
                        className: "text-right",
                        responsive: true,
                        breakpoint: "lg",
                    },
                    {
                        name: t("tables.validators.missed_blocks"),
                        type: "number",
                        className: "whitespace-nowrap text-right",
                    },
                    {
                        name: "",
                        type: "string",
                        className: "w-[70px] text-right",
                    },
                ]}
            />
        );
    }

    return (
        <div>
            <ValidatorsTable validators={validators} mobile={mobile} />
        </div>
    );
}

export function ValidatorsHeaderActions() {
    return (
        <div className="flex items-center justify-end space-x-3">
            <div className="flex-1">
                <Filter testId="validators:filter" withSelectAll />
            </div>
        </div>
    );
}
