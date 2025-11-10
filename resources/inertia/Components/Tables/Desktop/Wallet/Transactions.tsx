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
import ExportTransactionsModal from "./ExportTransactionsModal";
import { WalletProps } from "@/Pages/Wallet.contracts";
import Filter from "@/Components/Tables/Filter";
import useConfig from "@/hooks/use-config";

export function Row({ row }: { row: ITransaction }) {
    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">
                <ID transaction={row} />
            </TableCell>

            <TableCell breakpoint="xl" responsive>
                <Age timestamp={row.timestamp} />
            </TableCell>

            <TableCell>
                <Method transaction={row} />
            </TableCell>

            <TableCell>
                <Addressing transaction={row} withoutLink={row.isSentToSelf} />
            </TableCell>

            <TableCell className="text-right" lastOn="md-lg">
                <Amount transaction={row} />
            </TableCell>

            <TableCell className="text-right" breakpoint="md-lg" responsive>
                <Fee transaction={row} />
            </TableCell>
        </tr>
    );
}

export function TransactionsTable({
    transactions,
    mobile,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    mobile?: React.ReactNode;
}) {
    const { t } = useTranslation();
    const { network } = useConfig();

    return (
        <Table
            withHeader
            withFooter
            paginator={transactions}
            rowComponent={Row}
            resultCount={transactions.total ?? 0}
            mobile={mobile}
            headerActions={<TransactionsHeaderActions hasTransactions={transactions.total > 0} />}
            noResultsMessage={transactions.noResultsMessage}
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>

                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.transactions.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.transactions.method")}</TableHeader>

                    <TableHeader>{t("tables.transactions.addressing")}</TableHeader>

                    <TableHeader className="last-until-md-lg text-right" last-on="md-lg">
                        {t("tables.transactions.amount", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader className="text-right" responsive breakpoint="md-lg">
                        {t("tables.transactions.fee", {
                            currency: network!.currency,
                        })}
                    </TableHeader>
                </>
            }
        />
    );
}

export default function TransactionsTableWrapper({
    transactions,
    mobile,
    rowCount = 20,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    if (!transactions) {
        const { t } = useTranslation();
        const { network } = useConfig();

        return (
            <>
                <div className="hidden md:block">
                    <LoadingTable
                        rowCount={rowCount}
                        header={<TransactionsHeaderActions hasTransactions={false} />}
                        columns={[
                            {
                                name: t("tables.transactions.id"),
                                type: "string",
                                className: "w-[60px]",
                            },
                            {
                                name: t("tables.transactions.age"),
                                type: "string",
                                className: "w-[60px]",
                                responsive: true,
                                breakpoint: "xl",
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
                            },
                            {
                                name: t("tables.transactions.amount", {
                                    currency: network!.currency,
                                }),
                                className: "text-right w-[100px]",
                                lastOn: "md-lg",
                            },
                            {
                                name: t("tables.transactions.fee", {
                                    currency: network!.currency,
                                }),
                                className: "text-right w-[100px]",
                                responsive: true,
                                breakpoint: "md-lg",
                            },
                        ]}
                    />
                </div>

                {!!mobile && <div className="px-6 md:hidden md:px-10">{mobile}</div>}
            </>
        );
    }

    return (
        <div>
            <TransactionsTable transactions={transactions} mobile={mobile} />
        </div>
    );
}

export function TransactionsHeaderActions({ hasTransactions }: { hasTransactions: boolean }) {
    const { t } = useTranslation();
    const { wallet, rates, network, settings } = useConfig<WalletProps>();

    const [isTransactionsExportModalOpen, setIsTransactionsExportModalOpen] = useState(false);

    return (
        <div className="flex items-center justify-end space-x-3">
            <div className="flex-1">
                <button
                    type="button"
                    data-testid="wallet:transactions:export-button"
                    className="button-secondary flex w-full items-center justify-center space-x-2 py-1.5 sm:px-4"
                    disabled={!hasTransactions}
                    onClick={() => setIsTransactionsExportModalOpen(true)}
                >
                    <UnderlineArrowDownIcon className="h-4 w-4" />

                    <span>{t("actions.export")}</span>
                </button>

                <ExportTransactionsModal
                    isOpen={isTransactionsExportModalOpen}
                    onClose={() => setIsTransactionsExportModalOpen(false)}
                    address={wallet.address}
                    network={network}
                    userCurrency={settings?.currency || ""}
                    rates={rates}
                    canBeExchanged={network?.canBeExchanged || false}
                />
            </div>

            <div className="flex-1">
                <Filter testId="wallet:transactions:filter" withSelectAll />
            </div>
        </div>
    );
}
