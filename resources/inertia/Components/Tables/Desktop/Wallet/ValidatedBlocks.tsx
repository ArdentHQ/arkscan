import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { IPaginatedResponse } from "@/types";
import { IBlock } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { Table } from "../Table";
import { useConfig } from "@/Providers/Config/ConfigContext";
import TableHeader from "../TableHeader";
import classNames from "@/utils/class-names";
import UnderlineArrowDownIcon from "@ui/icons/arrows/underline-arrow-down.svg?react";
import Height from "@/Components/Block/Height";
import Age from "@/Components/Model/Age";
import Reward from "@/Components/Block/Reward";
import { useState } from "react";
import { usePage } from "@inertiajs/react";
import { PageProps } from "@inertiajs/core";
import { WalletProps } from "@/Pages/Wallet.contracts";
import ExportBlocksModal from "./ExportBlocksModal";

export function Row({ row }: { row: IBlock }) {
    const { network } = useConfig();

    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <Height block={row} />
            </TableCell>

            <TableCell breakpoint="md-lg" responsive>
                <Age timestamp={row.timestamp} />
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50">
                {row.transactionCount}
            </TableCell>

            <TableCell className="text-right" last-on={network?.canBeExchanged ? "lg" : undefined}>
                <Reward block={row} withoutValue={!network?.canBeExchanged} />
            </TableCell>
        </tr>
    );
}

export function ValidatedBlocksTable({
    blocks,
    mobile,
}: {
    blocks: IPaginatedResponse<IBlock>;
    mobile?: React.ReactNode;
}) {
    const { t } = useTranslation();
    const { network } = useConfig();
    const hasForgedBlocks = (blocks.total ?? blocks.data?.length ?? 0) > 0;

    return (
        <Table
            withHeader
            withFooter
            paginator={blocks}
            rowComponent={Row}
            resultCount={blocks.total ?? 0}
            mobile={mobile}
            headerActions={<ValidatedBlocksHeaderActions hasForgedBlocks={hasForgedBlocks} />}
            noResultsMessage={blocks.noResultsMessage}
            columns={
                <>
                    <TableHeader type="id" className="whitespace-nowrap">
                        {t("tables.blocks.height")}
                    </TableHeader>

                    <TableHeader breakpoint="md-lg" responsive>
                        {t("tables.blocks.age")}
                    </TableHeader>

                    <TableHeader className="text-right">{t("tables.blocks.transactions")}</TableHeader>

                    <TableHeader
                        className={classNames({
                            "whitespace-nowrap text-right": true,
                            "last-until-lg": !!network?.canBeExchanged,
                        })}
                        lastOn={network?.canBeExchanged ? "lg" : undefined}
                        tooltip={t("pages.wallets.blocks.total_reward_tooltip", {
                            currency: network!.currency,
                        })}
                    >
                        {t("tables.blocks.total_reward", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    {network?.canBeExchanged && (
                        <>
                            <TableHeader
                                className="whitespace-nowrap"
                                breakpoint="lg"
                                responsive
                                tooltip={t("pages.wallets.blocks.value_tooltip", {
                                    currency: network!.currency,
                                })}
                            >
                                {t("tables.blocks.value", {
                                    currency: network!.currency,
                                })}
                            </TableHeader>
                        </>
                    )}
                </>
            }
        />
    );
}

export default function ValidatedBlocksTableWrapper({
    blocks,
    mobile,
    rowCount = 20,
}: {
    blocks?: IPaginatedResponse<IBlock>;
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    if (!blocks) {
        const { t } = useTranslation();
        const { network } = useConfig();

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
                <div className="hidden md:block">
                    <LoadingTable rowCount={rowCount} header={<ValidatedBlocksHeaderActions />} columns={columns} />
                </div>

                {!!mobile && <div className="px-6 md:hidden md:px-10">{mobile}</div>}
            </>
        );
    }

    return (
        <div>
            <ValidatedBlocksTable blocks={blocks} mobile={mobile} />
        </div>
    );
}

export function ValidatedBlocksHeaderActions({ hasForgedBlocks }: { hasForgedBlocks: boolean }) {
    const { t } = useTranslation();
    const { network, settings } = useConfig();
    const {
        props: { wallet, rates },
    } = usePage<PageProps<WalletProps>>();
    const [isBlocksExportModalOpen, setIsBlocksExportModalOpen] = useState(false);

    return (
        <div className="flex items-center justify-end space-x-3">
            <div className="flex-1">
                <button
                    type="button"
                    className="button-secondary flex w-full items-center justify-center space-x-2 py-1.5 sm:px-4"
                    disabled={!hasForgedBlocks}
                    onClick={() => setIsBlocksExportModalOpen(true)}
                >
                    <UnderlineArrowDownIcon className="h-4 w-4" />

                    <span>{t("actions.export")}</span>
                </button>

                <ExportBlocksModal
                    isOpen={isBlocksExportModalOpen}
                    onClose={() => setIsBlocksExportModalOpen(false)}
                    address={wallet.address}
                    network={network}
                    userCurrency={settings?.currency || ""}
                    rates={rates}
                    canBeExchanged={network?.canBeExchanged || false}
                    filename={wallet.username || wallet.address}
                />
            </div>
        </div>
    );
}
