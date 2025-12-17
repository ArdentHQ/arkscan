import MobileTable from "@/Components/Tables/Mobile/Table";
import MobileTableRow from "@/Components/Tables/Mobile/Row";
import { MobileMissedBlocksSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Validators/MissedBlocks";
import { IPaginatedResponse } from "@/types";
import { IForgingStats } from "@/types/generated";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Model/Age";
import useSharedData from "@/hooks/use-shared-data";
import Height from "@/Components/Block/Height";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import TableCell from "../TableCell";
import Address from "@/Components/Wallet/Address";
import { networkCurrency } from "@/utils/number-formatter";
import Number from "@/Components/General/Number";

export function MissedBlocksMobileTable({ blocks }: { blocks: IPaginatedResponse<IForgingStats> }) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <MobileTable noResultsMessage={blocks.noResultsMessage} resultCount={blocks.total ?? 0}>
            {blocks.data.map((block: IForgingStats, index) => (
                <MobileTableRow
                    key={index}
                    expandable
                    header={
                        <div className="flex w-full items-center justify-between space-x-4">
                            <div className="sm:flex sm:flex-1">
                                <Height block={block} />
                            </div>

                            <div className="justify-end sm:flex sm:flex-1">
                                <Age
                                    timestamp={block.timestamp}
                                    className="text-theme-secondary-700 dark:text-theme-dark-200"
                                />
                            </div>
                        </div>
                    }
                >
                    <TableCell label={t("tables.missed-blocks.validator")} className="sm:hidden">
                        <Address wallet={block.validator} />
                    </TableCell>

                    <TableCell label={t("tables.missed-blocks.no_of_voters")}>
                        <Number>{block.voterCount}</Number>
                    </TableCell>

                    <TableCell
                        label={t("tables.missed-blocks.votes", {
                            currency: network?.currency,
                        })}
                    >
                        {networkCurrency(block.votes ?? 0, 2)}
                    </TableCell>

                    <TableCell label={t("tables.missed-blocks.percentage")}>
                        {(block.votesPercentage ?? 0).toFixed(2)}%
                    </TableCell>
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export default function MissedBlocksMobileTableWrapper({
    blocks,
    rowCount = 10,
}: {
    blocks?: IPaginatedResponse<IForgingStats>;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();

    if (!blocks || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0} />

                <MobileMissedBlocksSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <MissedBlocksMobileTable blocks={blocks} />
        </div>
    );
}
