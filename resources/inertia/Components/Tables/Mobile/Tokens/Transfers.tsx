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
import AddressingGeneric from "@/Components/Tokens/AddressingGeneric";
import Token from "@/Components/Tokens/Token";
import { TokenTransfer } from "@/models/TokenTransfer";
import useSharedData from "@/hooks/use-shared-data";

export function TransfersMobileTable({
    transfers,
    noAge,
}: {
    transfers: IPaginatedResponse<ITokenTransfer>;
    noAge?: boolean;
}) {
    const { t, i18n } = useTranslation();
    const { network } = useSharedData();

    return (
        <MobileTable noResultsMessage={transfers.noResultsMessage} resultCount={transfers.total ?? 0}>
            {transfers.data.map((row: ITokenTransfer, index) => {
                const transfer = TokenTransfer.make(row, network);

                return (
                    <MobileTableRow
                        key={index}
                        header={
                            <>
                                <ID transaction={transfer.transaction} />

                                {!noAge && (
                                    <Age
                                        className="text-theme-secondary-700 dark:text-theme-dark-200"
                                        timestamp={transfer.transaction.timestamp}
                                    />
                                )}
                            </>
                        }
                    >
                        <TableCell label={transfer.transaction.method.name({ t, i18n })} className="sm:flex-1">
                            <AddressingGeneric transfer={transfer} />
                        </TableCell>

                        <TableCell label={t("tables.tokens.amount_generic")} className="sm:flex-1">
                            <Amount
                                testId={`transaction:mobile:${transfer.transaction!.hash}:amount`}
                                tokenTransfer={transfer}
                            />
                        </TableCell>

                        <TableCell label={t("tables.tokens.token")}>
                            <Token token={transfer.token} className="w-full sm:w-[120px]" />
                        </TableCell>
                    </MobileTableRow>
                );
            })}
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
