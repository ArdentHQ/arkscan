import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "@/Components/Tables/Mobile/TableCell";
import { MobileTransactionsSkeletonTable } from "../Skeleton/Wallet/Transactions";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { ValidatorsHeaderActions } from "@/Components/Tables/Desktop/Validators/Validators";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Model/Age";
import Addressing from "@/Components/Transaction/Addressing";
import { Link } from "@inertiajs/react";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import Tooltip from "@/Components/General/Tooltip";
import { Transaction } from "@/models/Transaction";
import useSharedData from "@/hooks/use-shared-data";

export function RecentVotesMobileTable({ recentVotes }: Pick<ValidatorsProps, "recentVotes">) {
    const { t, i18n } = useTranslation();
    const { network } = useSharedData();

    return (
        <MobileTable noResultsMessage={recentVotes.noResultsMessage} resultCount={recentVotes.data.length ?? 0}>
            {recentVotes.data.map((row: ITransaction, index) => {
                const vote = Transaction.make(row, network);

                return (
                    <MobileTableRow
                        key={index}
                        header={
                            <>
                                <div className="flex flex-1 justify-between">
                                    <ID transaction={vote} />

                                    <Age timestamp={vote.timestamp} className="leading-4.25" />
                                </div>
                            </>
                        }
                        expandable
                    >
                        <div>
                            <TableCell label={t("tables.recent-votes.addressing")}>
                                <Addressing transaction={vote} alwaysShowAddress className="sm:hidden" isGeneric />

                                <Addressing
                                    transaction={vote}
                                    alwaysShowAddress
                                    className="hidden sm:flex"
                                    withoutTruncate
                                    isGeneric
                                />
                            </TableCell>
                        </div>

                        <TableCell label={<span>{vote.method.name({ t, i18n })}</span>}>
                            {vote.votedFor && (
                                <Link href={route("wallet", vote.votedFor)} className="link text-sm font-semibold">
                                    {vote.votedForUsername ? (
                                        vote.votedForUsername
                                    ) : (
                                        <TruncateMiddle>{vote.votedFor}</TruncateMiddle>
                                    )}
                                </Link>
                            )}
                        </TableCell>
                    </MobileTableRow>
                );
            })}
        </MobileTable>
    );
}

export default function ValidatorsMobileTableWrapper({
    recentVotes,
    rowCount = 10,
}: Pick<ValidatorsProps, "recentVotes"> & {
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();

    if (!recentVotes || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0}>
                    <ValidatorsHeaderActions />
                </TableHeaderWrapper>

                <MobileTransactionsSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <RecentVotesMobileTable recentVotes={recentVotes} />
        </div>
    );
}
