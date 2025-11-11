import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { IPaginatedResponse } from "@/types";
import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { Table } from "../Table";
import useConfig from "@/hooks/use-config";
import TableHeader from "../TableHeader";
import Address from "@/Components/Wallet/Address";
import useWebhookListener from "@/Providers/Webhooks/useWebhookListener";
import { WalletProps } from "@/Pages/Wallet.contracts";

export function Row({ row }: { row: IWallet }) {
    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <Address wallet={row} />
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50">
                {row.formattedBalanceFull}
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50">
                {row.votePercentage || "0.00"}%
            </TableCell>
        </tr>
    );
}

export function VotersTable({ voters, mobile }: { voters: IPaginatedResponse<IWallet>; mobile?: React.ReactNode }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    return (
        <Table
            withHeader
            withFooter
            paginator={voters}
            rowComponent={Row}
            resultCount={voters.total ?? 0}
            mobile={mobile}
            noResultsMessage={voters.noResultsMessage}
            columns={
                <>
                    <TableHeader type="id" className="whitespace-nowrap">
                        {t("general.wallet.address")}
                    </TableHeader>

                    <TableHeader className="text-right">
                        {t("tables.wallets.balance_currency", { currency: network!.currency })}
                    </TableHeader>

                    <TableHeader className="text-right" tooltip={t("pages.wallets.percentage_tooltip")}>
                        {t("general.wallet.percentage")}
                    </TableHeader>
                </>
            }
        />
    );
}

export default function VotersTableWrapper({
    voters,
    mobile,
    rowCount = 20,
}: {
    voters?: IPaginatedResponse<IWallet>;
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { wallet } = useConfig<WalletProps>();

    const reloadVoters = () => {
        window.Livewire.emit("reloadVoters");
    };

    useWebhookListener(`wallet-vote.${wallet.public_key}`, "WalletVote", reloadVoters);
    const { t } = useTranslation();
    const { network } = useConfig();

    if (!voters) {
        const columns: ILoadingTableColumn[] = [
            {
                name: t("general.wallet.address"),
            },
            {
                name: t("tables.wallets.balance_currency"),
                type: "number",
            },
            {
                name: t("general.wallet.percentage", {
                    currency: network!.currency,
                }),
                type: "number",
                tooltip: t("pages.wallets.percentage_tooltip"),
            },
        ];

        if (network?.canBeExchanged) {
            columns.push({
                name: t("general.wallet.percentage", { currency: network!.currency }),
                type: "number",
                tooltip: t("pages.wallets.voters.value_tooltip", {
                    currency: network!.currency,
                }),
            });
        }

        return (
            <>
                <div className="hidden md:block">
                    <LoadingTable rowCount={rowCount} columns={columns} />
                </div>

                {!!mobile && <div className="px-6 md:hidden md:px-10">{mobile}</div>}
            </>
        );
    }

    return (
        <div>
            <VotersTable voters={voters} mobile={mobile} />
        </div>
    );
}
