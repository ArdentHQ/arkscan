import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { Table } from "@/Components/Tables/Desktop/Table";
import TableHeader from "@/Components/Tables/Desktop/TableHeader";
import LoadingTable, { ILoadingTableColumn } from "@/Components/Tables/Desktop/LoadingTable";
import { Row } from "@/Components/Tables/Desktop/Transactions/Transactions";
import { TransactionsMobileTable } from "@/Components/Tables/Mobile/Transactions/Transactions";
import { MobileTransactionsSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Transactions/Transactions";
import LoadingText from "@/Components/Loading/Text";
import { Link } from "@inertiajs/react";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";
import Number from "@/Components/General/Number";

function HomeTransactionsTable({
    transactions,
    mobile,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    mobile?: React.ReactNode;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            paginator={transactions}
            rowComponent={Row}
            mobile={mobile}
            noResultsMessage={transactions.noResultsMessage}
            withFooter
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>

                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.transactions.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.transactions.method")}</TableHeader>

                    <TableHeader>{t("tables.transactions.addressing")}</TableHeader>

                    <TableHeader className="last-until-lg text-right" lastOn="lg">
                        {t("tables.transactions.amount", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader className="text-right" responsive breakpoint="lg">
                        {t("tables.transactions.fee", {
                            currency: network!.currency,
                        })}
                    </TableHeader>
                </>
            }
        />
    );
}

function HomeTransactionsMobile({ transactions }: { transactions: IPaginatedResponse<ITransaction> }) {
    return (
        <div className="md:hidden">
            <TransactionsMobileTable transactions={transactions} />
        </div>
    );
}

function ViewAllFooter({ total, suffix, href }: { total: number; suffix: string; href: string }) {
    const { t } = useTranslation();

    return (
        <div className="-mx-6 mt-4 flex flex-col items-center space-y-3 rounded-b-xl border-t border-theme-secondary-300 px-6 pt-4 dark:border-theme-dark-700 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 md:mx-0 md:mt-0 md:border md:border-t-0 md:pb-4">
            <div className="font-semibold dark:text-theme-dark-200 sm:mr-8">
                <span>
                    <Number>{total}</Number>
                </span>
                <span>&nbsp;{suffix}</span>
            </div>

            <div className="flex w-full sm:w-auto">
                <Link href={href} className="button-secondary h-8 w-full py-1.5">
                    <div className="flex items-center justify-center space-x-2">
                        <span>{t("pagination.view_all")}</span>

                        <ChevronRightSmallIcon className="h-3 w-3" />
                    </div>
                </Link>
            </div>
        </div>
    );
}

export default function HomeTransactionsTableWrapper({
    transactions,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
}) {
    const { t } = useTranslation();
    const { network, pagination } = useSharedData();
    const rowCount = transactions?.per_page ?? pagination?.per_page ?? 25;

    if (!transactions) {
        const columns: ILoadingTableColumn[] = [
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
                render: () => (
                    <div className="flex flex-1 flex-col justify-between space-y-2 font-semibold leading-4.25 lg:flex-row lg:space-x-2">
                        <div className="flex flex-row space-x-2">
                            <LoadingText width="w-[39px]" />
                            <LoadingText />
                        </div>

                        <div className="flex flex-row space-x-2">
                            <LoadingText width="w-[39px]" />
                            <LoadingText />
                        </div>
                    </div>
                ),
            },
            {
                name: t("tables.transactions.amount", {
                    currency: network!.currency,
                }),
                className: "text-right w-[100px]",
                lastOn: "lg",
            },
            {
                name: t("tables.transactions.fee", {
                    currency: network!.currency,
                }),
                className: "text-right w-[100px]",
                responsive: true,
                breakpoint: "lg",
            },
        ];

        return (
            <LoadingTable
                mobile={<MobileTransactionsSkeletonTable rowCount={rowCount} />}
                rowCount={rowCount}
                columns={columns}
            />
        );
    }

    return (
        <>
            <HomeTransactionsTable
                transactions={transactions}
                mobile={<HomeTransactionsMobile transactions={transactions} />}
            />

            <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">
                <ViewAllFooter
                    total={transactions.total ?? 0}
                    suffix={t("tables.home.transactions")}
                    href={route("transactions", { page: 2 })}
                />
            </div>
        </>
    );
}
