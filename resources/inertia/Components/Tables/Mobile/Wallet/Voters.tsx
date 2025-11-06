import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileVotersSkeletonTable } from "../Skeleton/Wallet/Voters";
import { IPaginatedResponse } from "@/types";
import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { useConfig } from "@/Providers/Config/ConfigContext";
import Address from "@/Components/Wallet/Address";

export function VotersMobileTable({ voters }: { voters: IPaginatedResponse<IWallet> }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    return (
        <MobileTable noResultsMessage={voters.noResultsMessage} resultCount={voters.total ?? 0}>
            {voters.data.map((voter: IWallet, index) => (
                <MobileTableRow key={index} header={<Address wallet={voter} truncate />}>
                    <TableCell
                        label={t("tables.wallets.balance_currency", {
                            currency: network?.currency,
                        })}
                    >
                        {voter.formattedBalanceFull}
                    </TableCell>

                    <TableCell label={t("general.wallet.percentage")}>{voter.votePercentage || "0.00"}%</TableCell>
                </MobileTableRow>
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
        return <MobileVotersSkeletonTable rowCount={rowCount} />;
    }

    return (
        <div className="md:hidden">
            <VotersMobileTable voters={voters} />
        </div>
    );
}
