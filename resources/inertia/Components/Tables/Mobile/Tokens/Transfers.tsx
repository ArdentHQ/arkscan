import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTransactionsSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Transactions/Transactions";
import { IPaginatedResponse } from "@/types";
import { ITokenTransfer } from "@/types/generated";
import { useTranslation } from "react-i18next";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Model/Age";
import Amount from "@/Components/Tokens/Amount";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import Addressing from "@/Components/Transaction/Addressing";

export function TransfersMobileTable({
    transfers,
    noAge,
}: {
    transfers: IPaginatedResponse<ITokenTransfer>;
    noAge?: boolean;
}) {
    const { t } = useTranslation();

    return (
        <MobileTable noResultsMessage={transfers.noResultsMessage} resultCount={transfers.total ?? 0}>
            {transfers.data.map((transfer: ITokenTransfer, index) => (
                <MobileTableRow
                    key={index}
                    header={
                        <>
                            <ID transaction={transfer.transaction!} />

                            {!noAge && (
                                <Age
                                    className="text-theme-secondary-700 dark:text-theme-dark-200"
                                    timestamp={transfer.transaction!.timestamp}
                                />
                            )}
                        </>
                    }
                >
                    <TableCell label={transfer.transaction!.type} className="sm:flex-1">
                        <Addressing transaction={transfer.transaction!} isReceived={true} />
                    </TableCell>

                    <TableCell label={t("tables.tokens.amount_generic")}>
                        <Amount
                            testId={`transaction:mobile:${transfer.transaction!.hash}:amount`}
                            tokenTransfer={transfer}
                        />
                    </TableCell>
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export default function TransfersMobileTableWrapper({
    transfers,
    rowCount = 10,
}: {
    transfers?: IPaginatedResponse<ITokenTransfer>;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();

    if (!transfers || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0} />

                <MobileTransactionsSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <TransfersMobileTable transfers={transfers} />
        </div>
    );
}
