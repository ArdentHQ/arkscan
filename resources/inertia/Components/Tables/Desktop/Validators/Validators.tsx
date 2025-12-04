import TableCell from "../TableCell";
import LoadingTable from "../LoadingTable";
import { ITransaction } from "@/types/generated";
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
import { useState } from "react";
import ExportTransactionsModal from "../Wallet/ExportTransactionsModal";
import { WalletProps } from "@/Pages/Wallet.contracts";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Filter from "@/Components/Tables/Filter";
import useSharedData from "@/hooks/use-shared-data";
import { ValidatorsProps } from "@/Pages/Validators.contracts";

export function Row({ row }: { row: ITransaction }) {
    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">{/* <ID transaction={row} /> */}</TableCell>

            <TableCell>{/* <Age timestamp={row.timestamp} /> */}</TableCell>

            <TableCell>{/* <Method transaction={row} /> */}</TableCell>

            <TableCell>{/* <Addressing transaction={row} withoutLink={row.isSentToSelf} /> */}</TableCell>

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

    // {
    //     name: t("tables.validators.rank"),
    //     type: "string",
    //     className: "w-[70px]",
    // },
    // {
    //     name: t("tables.validators.validator"),
    //     type: "address",
    // },
    // {
    //     name: t("tables.validators.status"),
    //     // type? @TODO
    // },
    // {
    //     name: t("tables.validators.no_of_voters"),
    //     type: "number",
    //     className: "whitespace-nowrap",
    // },
    // {
    //     name: t("tables.validators.votes", {
    //         currency: network!.currency,
    //     }),
    //     type: "number",
    //     className: "whitespace-nowrap",
    // },
    // {
    //     name: t("tables.validators.percentage"),
    //     type: "number",
    //     // className: "!py-2.5", ??
    // },
    // {
    //     name: t("tables.validators.missed_blocks"),
    //     type: "number",
    //     className: "whitespace-nowrap",
    // },
    // {
    //     name: "",
    //     type: "string",
    //     className: "w-[70px]",
    // },
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
