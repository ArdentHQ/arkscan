import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { IBlock } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import useSharedData from "@/hooks/use-shared-data";
import { BlocksListProps } from "@/Pages/Blocks.contracts";
import classNames from "classnames";
import Age from "@/Components/Model/Age";
import Height from "@/Components/Block/Height";
import Reward from "@/Components/Block/Reward";
import { Block } from "@/models/Block";
import Address from "@/Components/Wallet/Address";
import { IPaginatedResponse } from "@/types";

export function Row({ row }: { row: IBlock }) {
    const block = Block.from(row);
    const { network } = useSharedData();
    const { t } = useTranslation();

    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <Height block={block} />
            </TableCell>

            <TableCell breakpoint="md-lg" responsive>
                <Age timestamp={block.timestamp} />
            </TableCell>

            <TableCell>
                <div className="flex flex-col whitespace-nowrap text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50 md:space-y-1 xl:space-y-0">
                    <div className="xl:hidden">
                        <Address wallet={block.proposer} truncate />
                    </div>

                    <div className="hidden xl:block">
                        <Address wallet={block.proposer} />
                    </div>

                    <div className="mt-1 text-xs font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200 md-lg:hidden">
                        <span>{block.transactionCount}</span>
                        &nbsp;
                        <span>{t("tables.blocks.transactions")}</span>
                    </div>
                </div>
            </TableCell>

            <TableCell
                breakpoint="md-lg"
                responsive
                className="text-right text-theme-secondary-900 dark:text-theme-dark-50"
            >
                {block.transactionCount}
            </TableCell>

            <TableCell className="text-right" last-on={network?.canBeExchanged ? "lg" : undefined}>
                <Reward block={block} withoutValue={!network?.canBeExchanged} />
            </TableCell>

            {network?.canBeExchanged && (
                <TableCell className="text-right" breakpoint="lg" responsive>
                    {block.rewardFiat}
                </TableCell>
            )}
        </tr>
    );
}

export function BlocksListTable({
    blocks,
    mobile,
    withHeader = true,
    hidePagination = false,
}: Pick<BlocksListProps, "blocks"> & {
    mobile?: React.ReactNode;
    withHeader?: boolean;
    hidePagination?: boolean;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            withHeader={withHeader}
            withFooter
            paginator={blocks}
            rowComponent={Row}
            mobile={mobile}
            hidePagination={hidePagination}
            noResultsMessage={blocks.noResultsMessage}
            columns={
                <>
                    <TableHeader type="id" className="whitespace-nowrap">
                        {t("tables.blocks.height")}
                    </TableHeader>

                    <TableHeader breakpoint="md-lg" responsive>
                        {t("tables.blocks.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.blocks.generated_by")}</TableHeader>

                    <TableHeader breakpoint="md-lg" responsive className="text-right">
                        {t("tables.blocks.transactions")}
                    </TableHeader>

                    <TableHeader
                        className={classNames({
                            "whitespace-nowrap text-right": true,
                            "last-until-lg": !!network?.canBeExchanged,
                        })}
                        lastOn={network?.canBeExchanged ? "lg" : undefined}
                        tooltip={t("pages.wallets.blocks.total_reward_tooltip", {
                            currency: network!.currency,
                        })}
                        type="number"
                    >
                        {t("tables.blocks.total_reward", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    {network?.canBeExchanged && (
                        <TableHeader
                            className="whitespace-nowrap text-right"
                            breakpoint="lg"
                            responsive
                            tooltip={t("pages.wallets.blocks.value_tooltip", {
                                currency: network!.currency,
                            })}
                            type="number"
                        >
                            {t("tables.blocks.value", {
                                currency: network!.currency,
                            })}
                        </TableHeader>
                    )}
                </>
            }
        />
    );
}

export function BlocksListLoadingState({
    blocks,
    mobile,
    rowCount = 20,
}: {
    blocks?: IPaginatedResponse<IBlock>;
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData<BlocksListProps>();

    const columns: ILoadingTableColumn[] = [
        {
            name: t("tables.blocks.height"),
        },
        {
            name: t("tables.blocks.age"),
            breakpoint: "md-lg",
            responsive: true,
        },
        {
            name: t("tables.blocks.transactions"),
            type: "number",
        },
        {
            name: t("tables.blocks.total_reward", {
                currency: network!.currency,
            }),
            type: "number",
            tooltip: t("pages.wallets.blocks.total_reward_tooltip", {
                currency: network!.currency,
            }),
            lastOn: network?.canBeExchanged ? "lg" : undefined,
        },
    ];

    if (network?.canBeExchanged) {
        columns.push({
            name: t("tables.blocks.value", { currency: network!.currency }),
            type: "number",
            tooltip: t("pages.wallets.blocks.value_tooltip", {
                currency: network!.currency,
            }),
            breakpoint: "lg",
            responsive: true,
        });
    }

    return (
        <>
            <LoadingTable mobile={mobile} paginator={blocks} rowCount={rowCount} columns={columns} header />
        </>
    );
}

export default function BlocksListTableWrapper({
    mobile,
    rowCount = 20,
}: {
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();
    const { blocks } = useSharedData<BlocksListProps>();

    if (!blocks || isLoading) {
        return <BlocksListLoadingState blocks={blocks} mobile={mobile} rowCount={rowCount} />;
    }

    return (
        <div>
            <BlocksListTable blocks={blocks} mobile={mobile} />
        </div>
    );
}
