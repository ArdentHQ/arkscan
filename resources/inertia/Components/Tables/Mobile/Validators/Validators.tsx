import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTransactionsSkeletonTable } from "../Skeleton/Wallet/Transactions";
import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Model/Age";
import Amount from "@/Components/Transaction/Amount";
import useSharedData from "@/hooks/use-shared-data";
import Fee from "@/Components/Transaction/Fee";
import Addressing from "@/Components/Transaction/Addressing";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { ValidatorsHeaderActions } from "@/Components/Tables/Desktop/Validators/Validators";

export function ValidatorsMobileTable({ validators }: Pick<ValidatorsProps, "validators">) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <MobileTable noResultsMessage={validators.noResultsMessage} resultCount={validators.data.length ?? 0}>
            {validators.data.map((validator: IWallet, index) => (
                <MobileTableRow
                    key={index}
                    header={
                        <>
                            <ID wallet={validator} />

                            <Age timestamp={validator.timestamp} />
                        </>
                    }
                >
                    <TableCell label={validator.type} className="sm:flex-1">
                        <Addressing wallet={validator} withoutLink={validator.isSentToSelf} />
                    </TableCell>

                    <TableCell
                        label={t("tables.transactions.amount", {
                            currency: network?.currency,
                        })}
                    >
                        <Amount wallet={validator} hideCurrency={true} />
                    </TableCell>

                    <div className="sm:flex sm:flex-1 sm:justify-end">
                        <TableCell
                            label={t("tables.transactions.fee", {
                                currency: network?.currency,
                            })}
                        >
                            <Fee wallet={validator} />
                        </TableCell>
                    </div>
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export default function ValidatorsMobileTableWrapper({
    validators,
    rowCount = 10,
}: Pick<ValidatorsProps, "validators"> & {
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();

    if (!validators || isLoading) {
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
            <ValidatorsMobileTable validators={validators} />
        </div>
    );
}
