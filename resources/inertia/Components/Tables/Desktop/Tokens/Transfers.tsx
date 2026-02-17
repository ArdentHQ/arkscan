import TableCell from "../TableCell";
import LoadingTable from "../LoadingTable";
import { ITokenTransfer } from "@/types/generated";
import { IPaginatedResponse } from "@/types";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Model/Age";
import ID from "@/Components/Transaction/ID";
import { Table } from "../Table";
import Method from "@/Components/Transaction/Method";
import TableHeader from "../TableHeader";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import LoadingText from "@/Components/Loading/Text";
import Amount from "@/Components/Tokens/Amount";
import AddressingGeneric from "@/Components/Tokens/AddressingGeneric";
import Token from "@/Components/Tokens/Token";
import { TokenTransfer } from "@/models/TokenTransfer";

export function Row({ row }: { row: ITokenTransfer }) {
    const transfer = TokenTransfer.from(row);

    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">
                <ID transaction={transfer.transaction} />
            </TableCell>

            <TableCell breakpoint="xl" responsive>
                <Age timestamp={transfer.transaction.timestamp} />
            </TableCell>

            <TableCell>
                <Method transaction={transfer.transaction} />
            </TableCell>

            <TableCell>
                <AddressingGeneric transfer={transfer} />
            </TableCell>

            <TableCell className="text-right" lastOn="lg">
                <Amount
                    testId={`transaction:${transfer.transaction_hash}:amount`}
                    tokenTransfer={transfer}
                    breakpoint="lg"
                />
            </TableCell>

            <TableCell breakpoint="xl" responsive>
                <Token token={transfer.token} className="w-[120px]" />
            </TableCell>
        </tr>
    );
}

export function TransfersTable({
    transfers,
    mobile,
}: {
    transfers: IPaginatedResponse<ITokenTransfer>;
    mobile?: React.ReactNode;
}) {
    const { t } = useTranslation();

    return (
        <Table
            withHeader
            withFooter
            paginator={transfers}
            rowComponent={Row}
            mobile={mobile}
            noResultsMessage={transfers.noResultsMessage}
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>

                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.transactions.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.transactions.method")}</TableHeader>

                    <TableHeader>{t("tables.transactions.addressing")}</TableHeader>

                    <TableHeader className="last-until-lg text-right" last-on="lg">
                        {t("tables.tokens.amount_generic")}
                    </TableHeader>

                    <TableHeader className="w-[120px]" breakpoint="xl" responsive>
                        {t("tables.tokens.token")}
                    </TableHeader>
                </>
            }
        />
    );
}

export default function TransfersTableWrapper({
    transfers,
    mobile,
    rowCount = 20,
}: {
    transfers?: IPaginatedResponse<ITokenTransfer>;
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { t } = useTranslation();
    const { isLoading } = usePageHandler();

    if (!transfers || isLoading) {
        return (
            <LoadingTable
                mobile={mobile}
                paginator={transfers}
                rowCount={rowCount}
                header={true}
                columns={[
                    {
                        name: t("tables.transactions.id"),
                        type: "string",
                        className: "w-[60px]",
                    },
                    {
                        name: t("tables.transactions.age"),
                        type: "string",
                        className: "w-[60px] lg:hidden",
                        responsive: true,
                        breakpoint: "xl",
                        lastOn: "lg",
                    },
                    {
                        name: t("tables.transactions.method"),
                        indicatorHeight: "h-[21px]",
                        className: "text-left",
                    },
                    {
                        name: t("tables.transactions.addressing"),
                        type: "address",
                        indicatorHeight: "h-[21px]",
                        className: "text-left",
                        render: () => (
                            <div className="flex flex-row space-x-2">
                                <LoadingText width="w-[39px]" />
                                <LoadingText />
                            </div>
                        ),
                    },
                    {
                        name: t("tables.tokens.amount_generic"),
                        className: "text-right w-[100px]",
                        lastOn: "lg",
                    },
                    {
                        name: t("tables.tokens.token"),
                        className: "w-[120px]",
                        breakpoint: "xl",
                        responsive: true,
                    },
                ]}
            />
        );
    }

    return (
        <div>
            <TransfersTable transfers={transfers} mobile={mobile} />
        </div>
    );
}
