import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { IExchange, SortDirection } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import useSharedData from "@/hooks/use-shared-data";
import { ExchangesProps } from "@/Pages/Exchanges.contracts";
import ExchangePair from "@/Components/Exchanges/Pairs";
import ExternalLink from "@/Components/General/ExternalLink";
import TableSortingProvider from "@/Providers/TableSorting/TableSortingProvider";
import ExchangePrice from "@/Components/Exchanges/Price";
import ExchangeVolume from "@/Components/Exchanges/Volume";

export function Row({ row: exchange }: { row: IExchange }) {
    const { t } = useTranslation();

    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <div className="flex items-center space-x-3">
                    <div className="flex h-5 w-5 items-center justify-center">
                        <img className="max-h-full max-w-full" src={exchange.iconUrl} alt={`${exchange.name} icon`} />
                    </div>

                    <ExternalLink
                        url={exchange.url}
                        className="link flex items-center space-x-2 break-words font-semibold leading-4"
                        iconClass="inline relative flex-shrink-0 ml-0.5 text-theme-secondary-500 dark:text-theme-dark-500 w-3 h-3"
                    >
                        {exchange.name}
                    </ExternalLink>
                </div>
            </TableCell>

            <TableCell>
                <ExchangePair exchange={exchange} />
            </TableCell>

            <TableCell className="text-right">
                <ExchangePrice exchange={exchange} />
            </TableCell>

            <TableCell
                breakpoint="md-lg"
                responsive
                className="text-right text-theme-secondary-900 dark:text-theme-dark-50"
            >
                <ExchangeVolume exchange={exchange} />
            </TableCell>
        </tr>
    );
}

export function ExchangesTable({ mobile }: { mobile?: React.ReactNode }) {
    const { t } = useTranslation();
    const { exchanges, settings } = useSharedData<ExchangesProps>();

    return (
        <Table
            paginator={exchanges}
            rowComponent={Row}
            mobile={mobile}
            noResultsMessage={exchanges.noResultsMessage}
            columns={
                <TableSortingProvider initialSortBy="volume" initialSortDirection={SortDirection.DESC}>
                    <TableHeader type="string" sortId="name">
                        {t("tables.exchanges.name")}
                    </TableHeader>

                    <TableHeader sortId="top_pairs">{t("tables.exchanges.top_pairs")}</TableHeader>

                    <TableHeader type="number" sortId="price">
                        {t("tables.exchanges.price_currency", { currency: settings!.currency })}
                    </TableHeader>

                    <TableHeader breakpoint="md-lg" responsive type="number" sortId="volume">
                        {t("tables.exchanges.volume_currency", { currency: settings!.currency })}
                    </TableHeader>
                </TableSortingProvider>
            }
        />
    );
}

export function ExchangesLoadingState({ mobile, rowCount = 20 }: { mobile?: React.ReactNode; rowCount?: number }) {
    const { t } = useTranslation();
    const { settings, exchanges } = useSharedData<ExchangesProps>();

    const columns: ILoadingTableColumn[] = [
        {
            name: t("tables.exchanges.name"),
        },
        {
            name: t("tables.exchanges.top_pairs"),
        },
        {
            name: t("tables.exchanges.price_currency", {
                currency: settings!.currency,
            }),
            type: "number",
        },
        {
            name: t("tables.exchanges.volume_currency", {
                currency: settings!.currency,
            }),
            type: "number",
            breakpoint: "md-lg",
            responsive: true,
        },
    ];

    return (
        <>
            <LoadingTable mobile={mobile} paginator={exchanges} rowCount={rowCount} columns={columns} />
        </>
    );
}

export default function ExchangesTableWrapper({
    mobile,
    rowCount = 20,
}: {
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();
    const { exchanges } = useSharedData<ExchangesProps>();

    if (!exchanges || isLoading) {
        return <ExchangesLoadingState mobile={mobile} rowCount={rowCount} />;
    }

    return (
        <div>
            <ExchangesTable mobile={mobile} />
        </div>
    );
}
