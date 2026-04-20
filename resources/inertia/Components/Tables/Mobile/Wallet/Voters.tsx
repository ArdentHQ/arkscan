import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileVotersSkeletonTable } from "../Skeleton/Wallet/Voters";
import { IPaginatedResponse } from "@/types";
import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import Address from "@/Components/Wallet/Address";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import useWalletFormatting from "@/hooks/use-wallet-formatting";

function VoterRow({ voter }: { voter: IWallet }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const { formattedBalanceFull } = useWalletFormatting(voter.balance);

    return (
        <MobileTableRow header={<Address wallet={voter} truncate />}>
            <TableCell
                label={t("tables.wallets.balance_currency", {
                    currency: network?.currency,
                })}
            >
                {formattedBalanceFull}
            </TableCell>

            <TableCell label={t("general.wallet.percentage")}>{voter.votePercentage || "0.00"}%</TableCell>
        </MobileTableRow>
    );
}

export function VotersMobileTable({ voters }: { voters: IPaginatedResponse<IWallet> }) {
    return (
        <MobileTable noResultsMessage={voters.noResultsMessage} resultCount={voters.total ?? 0}>
            {voters.data.map((voter: IWallet, index) => (
                <VoterRow key={index} voter={voter} />
            ))}
        </MobileTable>
    );
}

export default function VotersMobileTableWrapper({
    voters,
    rowCount = 10,
}: {
    voters?: IPaginatedResponse<IWallet>;
    rowCount?: number;
}) {
    if (!voters) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0} />

                <MobileVotersSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <VotersMobileTable voters={voters} />
        </div>
    );
}
