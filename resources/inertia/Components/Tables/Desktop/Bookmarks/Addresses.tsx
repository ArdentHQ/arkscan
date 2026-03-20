import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import TableCell from "../TableCell";
import { IPaginatedResponse } from "@/types";
import { IWallet } from "@/types/generated";
import Address from "@/Components/Wallet/Address";
import useWalletFormatting from "@/hooks/use-wallet-formatting";
import BookmarkButton from "@/Components/General/BookmarkButton";

function Row({ row }: { row: IWallet }) {
    const { formattedBalanceFull } = useWalletFormatting(row.balance);

    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <Address wallet={row} />
            </TableCell>

            <TableCell>
                <span className="leading-4.25">{row.username ?? null}</span>
            </TableCell>

            <TableCell className="text-right text-theme-secondary-900 dark:text-theme-dark-50">
                {formattedBalanceFull}
            </TableCell>

            <TableCell className="text-center">
                <BookmarkButton type="addresses" id={row.address} />
            </TableCell>
        </tr>
    );
}

export default function BookmarkAddressesTable({ addresses }: { addresses?: IPaginatedResponse<IWallet> }) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!addresses) {
        return null;
    }

    return (
        <Table
            withHeader
            withFooter
            paginator={addresses}
            rowComponent={Row}
            noResultsMessage={addresses.noResultsMessage}
            columns={
                <>
                    <TableHeader type="id" className="whitespace-nowrap">
                        {t("general.wallet.address")}
                    </TableHeader>

                    <TableHeader>{t("general.wallet.name")}</TableHeader>

                    <TableHeader className="text-right">
                        {t("general.wallet.balance_currency", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader className="text-center">{""}</TableHeader>
                </>
            }
        />
    );
}
