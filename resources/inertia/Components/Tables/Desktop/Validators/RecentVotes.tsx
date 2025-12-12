import TableCell from "../TableCell";
import LoadingTable from "../LoadingTable";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Filter from "@/Components/Tables/Filter";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Model/Age";
import Addressing from "@/Components/Transaction/Addressing";
import { Link } from "@inertiajs/react";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import Method from "@/Components/Transaction/Method";

export function Row({ row: vote }: { row: ITransaction }) {
    const votedFor = vote.votedFor;

    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <ID transaction={vote} />
            </TableCell>

            <TableCell responsive breakpoint="xl">
                <Age timestamp={vote.timestamp} />
            </TableCell>

            <TableCell>
                <Addressing transaction={vote} alwaysShowAddress withoutTruncate className="hidden lg:flex" isGeneric />

                <Addressing transaction={vote} alwaysShowAddress className="lg:hidden" />
            </TableCell>

            <TableCell>
                <Method transaction={vote} />
            </TableCell>

            <TableCell>
                {votedFor && (
                    <Link href={`/wallet/${votedFor}`} className="link text-sm font-semibold">
                        {vote.votedForUsername ? vote.votedForUsername : <TruncateMiddle>{votedFor}</TruncateMiddle>}
                    </Link>
                )}
            </TableCell>
        </tr>
    );
}

export function RecentVotesTable({
    recentVotes,
    mobile,
}: Pick<ValidatorsProps, "recentVotes"> & {
    mobile?: React.ReactNode;
}) {
    const { t } = useTranslation();

    return (
        <Table
            withHeader
            withFooter
            paginator={recentVotes}
            rowComponent={Row}
            mobile={mobile}
            headerActions={<RecentVotesHeaderActions />}
            noResultsMessage={recentVotes.noResultsMessage}
            columns={
                <>
                    <TableHeader width="200">{t("tables.recent-votes.id")}</TableHeader>
                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.recent-votes.age")}
                    </TableHeader>
                    <TableHeader>{t("tables.recent-votes.addressing")}</TableHeader>
                    <TableHeader>{t("tables.recent-votes.type")}</TableHeader>
                    <TableHeader>{t("tables.recent-votes.validator")}</TableHeader>
                </>
            }
        />
    );
}

export default function RecentVotesTableWrapper({
    recentVotes,
    mobile,
    rowCount = 20,
}: Pick<ValidatorsProps, "recentVotes"> & {
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();
    const { t } = useTranslation();

    if (!recentVotes || isLoading) {
        return (
            <LoadingTable
                mobile={mobile}
                paginator={recentVotes}
                rowCount={rowCount}
                header={<RecentVotesHeaderActions />}
                columns={[
                    {
                        name: t("tables.recent-votes.id"),
                        type: "string",
                        className: "w-[200px]",
                    },
                    {
                        name: t("tables.recent-votes.age"),
                        type: "string",
                        responsive: true,
                        breakpoint: "xl",
                    },
                    {
                        name: t("tables.recent-votes.addressing"),
                        type: "address",
                        className: "hidden lg:flex",
                    },
                    {
                        name: t("tables.recent-votes.type"),
                        type: "string",
                    },
                    {
                        name: t("tables.recent-votes.validator"),
                        type: "string",
                    },
                ]}
            />
        );
    }

    return (
        <div>
            <RecentVotesTable recentVotes={recentVotes} mobile={mobile} />
        </div>
    );
}

export function RecentVotesHeaderActions() {
    return (
        <div className="flex items-center justify-end space-x-3">
            <div className="flex-1">
                <Filter testId="recent-votes:filter" withSelectAll />
            </div>
        </div>
    );
}
