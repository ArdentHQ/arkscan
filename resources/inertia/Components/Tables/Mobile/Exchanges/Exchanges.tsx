import MobileTable from "@/Components/Tables/Mobile/Table";
import MobileTableRow from "@/Components/Tables/Mobile/Row";
import TableCell from "@/Components/Tables/Mobile/TableCell";
import { IExchange } from "@/types/generated";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import HeaderExternalLink from "./HeaderExternalLink";
import { ExchangesProps } from "@/Pages/Exchanges.contracts";
import ExchangePair from "@/Components/Exchanges/Pairs";
import ExchangeVolume from "@/Components/Exchanges/Volume";
import ExchangePrice from "@/Components/Exchanges/Price";
import MobileExchangesSkeletonTable from "../Skeleton/Exchanges/Exchanges";

export function ExchangesMobileTable() {
    const { t } = useTranslation();
    const { exchanges } = useSharedData<ExchangesProps>();

    return (
        <MobileTable noResultsMessage={exchanges.noResultsMessage} resultCount={exchanges.total ?? 0}>
            {exchanges.data.map((exchange: IExchange, index) => (
                <MobileTableRow
                    key={index}
                    header={
                        <>
                            <HeaderExternalLink exchange={exchange} />
                        </>
                    }
                >
                    <TableCell label={t("tables.exchanges.top_pairs")}>
                        <ExchangePair exchange={exchange} />
                    </TableCell>

                    <TableCell label={t("tables.exchanges.price")}>
                        <ExchangePrice exchange={exchange} />
                    </TableCell>

                    <TableCell label={t("tables.exchanges.volume")}>
                        <ExchangeVolume exchange={exchange} />
                    </TableCell>
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export default function ExchangesMobileTableWrapper({ rowCount = 10 }: { rowCount?: number }) {
    const { isLoading } = usePageHandler();
    const { exchanges } = useSharedData<ExchangesProps>();

    if (!exchanges || isLoading) {
        return (
            <div>
                <MobileExchangesSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <ExchangesMobileTable />
        </div>
    );
}
