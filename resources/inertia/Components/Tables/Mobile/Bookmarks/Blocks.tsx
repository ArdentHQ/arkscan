import { useTranslation } from "react-i18next";
import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { IPaginatedResponse } from "@/types";
import { IBlock } from "@/types/generated";
import useSharedData from "@/hooks/use-shared-data";
import useSettings from "@/Providers/Settings/useSettings";
import { Block } from "@/models/Block";
import Age from "@/Components/Model/Age";
import Height from "@/Components/Block/Height";
import Reward from "@/Components/Block/Reward";
import Address from "@/Components/Wallet/Address";
import BookmarkButton from "@/Components/General/BookmarkButton";
import { currency } from "@/utils/number-formatter";

function Row({ row }: { row: IBlock }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const { currency: selectedCurrency } = useSettings();
    const block = Block.from(row);

    return (
        <MobileTableRow
            header={
                <>
                    <Height block={block} />

                    <div className="flex items-center space-x-2">
                        <Age
                            className="text-theme-secondary-700 dark:text-theme-dark-200"
                            timestamp={block.timestamp}
                        />
                        <BookmarkButton type="blocks" id={block.hash} variant="inline" />
                    </div>
                </>
            }
        >
            <TableCell label={t("tables.blocks.generated_by")} className="sm:flex-1">
                <Address wallet={block.proposer} truncate />
            </TableCell>

            <TableCell label={t("tables.blocks.transactions")}>{block.transactionCount}</TableCell>

            <TableCell
                label={t("tables.blocks.total_reward", {
                    currency: network?.currency,
                })}
            >
                <Reward block={block} withoutValue={!network?.canBeExchanged} />
            </TableCell>

            {network?.canBeExchanged && (
                <TableCell
                    label={t("tables.blocks.value", {
                        currency: network?.currency,
                    })}
                >
                    {currency(block.rewardFiat(selectedCurrency), selectedCurrency)}
                </TableCell>
            )}
        </MobileTableRow>
    );
}

export default function BookmarkBlocksMobileTable({ blocks }: { blocks: IPaginatedResponse<IBlock> }) {
    return (
        <div className="px-6 md:hidden">
            <MobileTable noResultsMessage={blocks.noResultsMessage} resultCount={blocks.total ?? 0}>
                {blocks.data.map((row: IBlock, index) => (
                    <Row key={index} row={row} />
                ))}
            </MobileTable>
        </div>
    );
}
