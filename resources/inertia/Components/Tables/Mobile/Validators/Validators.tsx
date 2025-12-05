import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTransactionsSkeletonTable } from "../Skeleton/Wallet/Transactions";
import { IValidator, IWallet } from "@/types/generated";
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
import { ValidatorStatus, ValidatorsHeaderActions } from "@/Components/Tables/Desktop/Validators/Validators";
import Number from "@/Components/General/Number";
import Identity from "@/Components/General/Identity";
import { useMemo } from "react";
import VoteLink from "@/Components/Validator/VoteLink";

// @foreach ($validators as $validator)
//         <x-tables.rows.mobile
//             wire:key="{{ Helpers::generateId('validator-mobile', $validator->address()) }}"
//             :expand-class="Arr::toCssClasses(['space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700' => ! $validator->isResigned(),
//             ])"
//             expandable
//             :content-class="config('arkscan.arkconnect.enabled') ? '!pb-0 sm:!pb-3' : ''"
//         >
//             <x-slot name="header">
//                 <div class="flex flex-1 min-w-0 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
//                     <x-tables.rows.mobile.encapsulated.validators.rank
//                         :model="$validator"
//                         class="min-w-[32px]"
//                     />

//                     <div class="flex flex-1 justify-between items-center pl-3 min-w-0">
//                         <x-tables.rows.mobile.encapsulated.validators.address
//                             :model="$validator"
//                             class="min-w-0"
//                             identity-class="min-w-0"
//                             identity-content-class="min-w-0"
//                             identity-link-class="pr-2 min-w-0"
//                             without-clipboard
//                             without-label
//                         />

//                         <div class="flex items-center">
//                             <x-tables.rows.mobile.encapsulated.validators.status
//                                 :model="$validator"
//                                 class="hidden sm:block"
//                                 without-label
//                             />

//                             <x-tables.rows.mobile.encapsulated.validators.vote-link
//                                 :model="$validator"
//                                 class="sm:pl-3 sm:ml-3 sm:border-l border-theme-secondary-300 dark:border-theme-dark-700"
//                             />
//                         </div>
//                     </div>
//                 </div>
//             </x-slot>

//             <x-tables.rows.mobile.encapsulated.validators.status :model="$validator" />

//             <x-tables.rows.mobile.encapsulated.validators.number-of-voters
//                 :model="$validator"
//                 class="sm:hidden"
//             />

//             <x-tables.rows.mobile.encapsulated.validators.votes :model="$validator" />

//             <x-tables.rows.mobile.encapsulated.validators.votes-percentage :model="$validator" />

//             <x-tables.rows.mobile.encapsulated.validators.missed-blocks :model="$validator" />

//             @if (config('arkscan.arkconnect.enabled'))
//                 <div class="sm:hidden">
//                     <x-tables.rows.mobile.encapsulated.validators.voting-for :model="$validator" />
//                 </div>
//             @endif
//         </x-tables.rows.mobile>
//     @endforeach
// </x-tables.mobile.includes.encapsulated>

export function ValidatorsMobileTable({ validators }: Pick<ValidatorsProps, "validators">) {
    const { t } = useTranslation();
    const { network, arkconnectConfig } = useSharedData();

    return (
        <MobileTable noResultsMessage={validators.noResultsMessage} resultCount={validators.data.length ?? 0}>
            {validators.data.map((validator: IValidator, index) => (
                <MobileTableRow
                    key={index}
                    header={
                        <>
                            <div className="flex min-w-0 flex-1 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                                <Number className="min-w-[32px]">{validator.rank ?? 0}</Number>

                                <div className="flex min-w-0 flex-1 items-center justify-between pl-3">
                                    <Identity
                                        model={validator}
                                        className="w-full min-w-0"
                                        contentClassName="min-w-0 w-full"
                                        linkClassName="pr-2 min-w-0"
                                    />

                                    <div className="flex items-center">
                                        <span className="inline-block">
                                            <ValidatorStatus validator={validator} />
                                        </span>

                                        <div className="border-theme-secondary-300 dark:border-theme-dark-700 sm:ml-3 sm:border-l sm:pl-3">
                                            <VoteLink wallet={validator} />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </>
                    }
                    expandable
                    expandClass={
                        !validator.isResigned
                            ? "space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700"
                            : ""
                    }
                    contentClass={arkconnectConfig.enabled ? "!pb-0 sm:!pb-3" : ""}
                >
                    {/* //     <TableCell label={validator.type} className="sm:flex-1">
                //         <Addressing wallet={validator} withoutLink={validator.isSentToSelf} />
                //     </TableCell>

                //     <TableCell
                //         label={t("tables.transactions.amount", {
                //             currency: network?.currency,
                //         })}
                //     >
                //         <Amount wallet={validator} hideCurrency={true} />
                //     </TableCell>

                //     <div className="sm:flex sm:flex-1 sm:justify-end">
                //         <TableCell
                //             label={t("tables.transactions.fee", {
                //                 currency: network?.currency,
                //             })}
                //         >
                //             <Fee wallet={validator} />
                //         </TableCell>
                //     </div> */}
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
