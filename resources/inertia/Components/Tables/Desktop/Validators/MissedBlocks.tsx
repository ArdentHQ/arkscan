import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { IPaginatedResponse } from "@/types";
import { IForgingStats, SortDirection } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import Height from "@/Components/Block/Height";
import Age from "@/Components/Model/Age";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import useSharedData from "@/hooks/use-shared-data";
import Address from "@/Components/Wallet/Address";
import Number from "@/Components/General/Number";
import { networkCurrency } from "@/utils/number-formatter";
import TableSortingProvider from "@/Providers/TableSorting/TableSortingProvider";

export function Row({ row }: { row: IForgingStats }) {
    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <Height block={row} withoutLink />
            </TableCell>

            <TableCell breakpoint="md-lg" responsive>
                <Age timestamp={row.timestamp} />
            </TableCell>

            <TableCell>
                <Address wallet={row.validator} />
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50">
                <Number>{row.voterCount}</Number>
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50">
                {networkCurrency(row.votes ?? 0, 2)}
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50">
                {(row.votesPercentage ?? 0).toFixed(2)}%
            </TableCell>
        </tr>
    );
}

export function MissedBlocksTable({
    blocks,
    mobile,
}: {
    blocks: IPaginatedResponse<IForgingStats>;
    mobile?: React.ReactNode;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            resultSuffix={t("pages.validators.missed-blocks.results_suffix")}
            withHeader
            withFooter
            paginator={blocks}
            rowComponent={Row}
            mobile={mobile}
            noResultsMessage={blocks.noResultsMessage}
            columns={
                <TableSortingProvider initialSortBy="age" initialSortDirection={SortDirection.DESC} onChange={() => {}}>
                    <TableHeader sortId="height" type="string" className="width-[200px] whitespace-nowrap">
                        {t("tables.blocks.height")}
                    </TableHeader>

                    <TableHeader sortId="age" breakpoint="md-lg" responsive>
                        {t("tables.blocks.age")}
                    </TableHeader>

                    <TableHeader sortId="name">{t("tables.missed-blocks.validator")}</TableHeader>

                    <TableHeader sortId="no_of_voters" type="number">
                        {t("tables.missed-blocks.no_of_voters")}
                    </TableHeader>

                    <TableHeader sortId="votes" type="number">
                        {t("tables.missed-blocks.votes", {
                            currency: network?.currency,
                        })}
                    </TableHeader>

                    <TableHeader
                        sortId="percentage_votes"
                        type="number"
                        tooltip={t("tables.missed-blocks.info.percentage")}
                    >
                        {t("tables.missed-blocks.percentage")}
                    </TableHeader>
                </TableSortingProvider>
            }
        />
    );
}

export default function MissedBlocksTableWrapper({
    blocks,
    mobile,
    rowCount = 20,
}: {
    blocks?: IPaginatedResponse<IForgingStats>;
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!blocks || isLoading) {
        const columns: ILoadingTableColumn[] = [
            {
                name: t("tables.blocks.height"),
            },
            {
                name: t("tables.blocks.age"),
                breakpoint: "md-lg",
                responsive: true,
            },
            {
                name: t("tables.missed-blocks.validator"),
                type: "address",
            },
            {
                name: t("tables.missed-blocks.no_of_voters"),
                type: "number",
            },
            {
                name: t("tables.missed-blocks.votes", {
                    currency: network?.currency,
                }),
                type: "number",
            },
            {
                name: t("tables.missed-blocks.percentage"),
                type: "number",
                tooltip: t("tables.missed-blocks.info.percentage"),
            },
        ];

        return (
            <>
                <LoadingTable mobile={mobile} paginator={blocks} rowCount={rowCount} columns={columns} />
            </>
        );
    }

    return (
        <div>
            <MissedBlocksTable blocks={blocks} mobile={mobile} />
        </div>
    );
}
