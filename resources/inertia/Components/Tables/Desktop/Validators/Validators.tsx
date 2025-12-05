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

{
    /* <x-ark-tables.row wire:key="validator-{{ $validator->address() }}">
                
                
                
                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.number-of-voters :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.validators.votes :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                    breakpoint="lg"
                >
                    <x-tables.rows.desktop.encapsulated.validators.votes-percentage :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.missed-blocks :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.vote-link :model="$validator" />
                </x-ark-tables.cell>
            </x-ark-tables.row> */
}
export function Row({ row: validator }: { row: IValidator }) {
    const { arkconnectConfig } = useSharedData();
    const { votingForAddress } = useArkConnect();
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

    return (
        <tr className="text-sm font-semibold">
            <TableCell>{validator.rank}</TableCell>

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
                <Badge className="encapsulated-badge">{statusLabel}</Badge>
            </TableCell>

            <TableCell className="text-right">
                <Number className="text-theme-secondary-900 dark:text-theme-dark-50">{validator.voterCount}</Number>
            </TableCell>

            <TableCell>{/* <Amount transaction={row} hideCurrency /> */}</TableCell>

            <TableCell>{/* <Fee transaction={row} /> */}</TableCell>
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
            <>
                {/* @see resources/views/components/tables/desktop/validators/list-table.blade.php */}
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
                            // type? @TODO
                        },
                        {
                            name: t("tables.validators.no_of_voters"),
                            type: "number",
                            className: "whitespace-nowrap",
                        },
                        {
                            name: t("tables.validators.votes", {
                                currency: network!.currency,
                            }),
                            type: "number",
                            className: "whitespace-nowrap",
                        },
                        {
                            name: t("tables.validators.percentage"),
                            type: "number",
                            // className: "!py-2.5", ??
                        },
                        {
                            name: t("tables.validators.missed_blocks"),
                            type: "number",
                            className: "whitespace-nowrap",
                        },
                        {
                            name: "",
                            type: "string",
                            className: "w-[70px]",
                        },
                    ]}
                />
            </>
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
