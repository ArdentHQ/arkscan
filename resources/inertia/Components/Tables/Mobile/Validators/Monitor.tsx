import Status from "@/Components/Validator/Monitor/Status";
import TimeToForge from "@/Components/Validator/Monitor/TimeToForge";
import Address from "@/Components/Wallet/Address";
import FavoriteIcon from "@/Components/Validator/Monitor/FavoriteIcon";
import { useValidatorFavorites } from "@/Providers/ValidatorFavorites/ValidatorFavoritesContext";
import classNames from "@/utils/class-names";
import LoadingTable from "../LoadingTable";
import MobileTable from "../Table";
import MobileTableRow from "../Row";
import MissedWarning from "@/Components/Validator/Monitor/MissedWarning";
import TableCell from "../TableCell";
import BlockHeight from "@/Components/Validator/Monitor/BlockHeight";
import MobileDivider from "@/Components/General/MobileDivider";

export function MonitorMobileHeader({ validator }) {
    return (
        <div className="flex flex-1 min-w-0 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
            <div className="flex items-center">
                <div className="hidden items-center pr-3 sm:flex">
                    <FavoriteIcon validator={validator} />
                </div>

                <span className="text-sm font-semibold leading-4.25 min-w-[32px] dark:text-theme-dark-200">
                    {validator.order}
                </span>
            </div>

            <div className="flex flex-1 justify-between items-center pl-3 min-w-0">
                <div className="flex items-center">
                    <TableCell>
                        <Address
                            wallet={validator.wallet}
                            truncate
                            className="sm:hidden"
                        />

                        <Address
                            wallet={validator.wallet}
                            truncate="dynamic"
                            className="hidden sm:block md:hidden"
                        />
                    </TableCell>

                    <MissedWarning validator={validator} />
                </div>

                <div className="flex items-center sm:space-x-3 h-[21px]">
                    <div className="flex items-center sm:hidden">
                        <Status wallet={validator.wallet} withText={false} />
                    </div>

                    <div className="hidden sm:block">
                        <Status wallet={validator.wallet} />
                    </div>
                </div>
            </div>
        </div>
    );
}

export function MonitorMobileTable({ validators }: { validators: any[] }) {
    const { isFavorite } = useValidatorFavorites();

    // const sortedValidators = [...validators].sort((a, b) => {
    //     const aIsFavorite = isFavorite(a.wallet.public_key);
    //     const bIsFavorite = isFavorite(b.wallet.public_key);

    //     if (aIsFavorite === bIsFavorite) {
    //         return a.order - b.order;
    //     }

    //     if (aIsFavorite) {
    //         return -1;
    //     }

    //     return 1;
    // });

    return (
        <MobileTable>
            {validators.map((validator, index) => (
                <MobileTableRow
                    key={index}
                    expandClass={classNames({
                        'space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700': ! validator.wallet.isResigned,
                    })}
                    className={classNames({
                        'validator-monitor-favorite': isFavorite(validator.wallet.public_key),
                    })}
                    expandable={true}
                    header={<MonitorMobileHeader validator={validator} />}
                >
                    <TableCell label="Status">
                        <Status
                            wallet={validator.wallet}
                            className="sm:hidden"
                        />
                    </TableCell>

                    <TableCell label="Time to Forge">
                        <TimeToForge
                            forgingAt={validator.forgingAt}
                            wallet={validator.wallet}
                        />
                    </TableCell>

                    <TableCell label="Block Height">
                        <BlockHeight validator={validator} />
                    </TableCell>

                    <div className="sm:hidden pt-4 mt-4 border-t sm:border-t-0 sm:pt-0 sm:mt-0 border-theme-secondary-300 dark:border-theme-dark-700">
                        <FavoriteIcon
                            validator={validator}
                            label="Favorite"
                        />
                    </div>
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export function MobileFavoritesTable({ validators }: { validators: any[] }) {
    const { isFavorite } = useValidatorFavorites();

    const favoritedValidators = (validators || []).filter((validator) => isFavorite(validator.wallet.public_key));
    if (favoritedValidators.length === 0) {
        return null;
    }

    return (
        <div>
            <div className="px-6 md:px-10 pb-3 font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                My Favorites
            </div>

            <MonitorMobileTable validators={favoritedValidators} />
        </div>
    );
}

export default function MonitorMobileTableWrapper({ validators, rowCount }: {
    validators: any[];
    rowCount: number;
}) {
    // if (!validators || validators.length === 0) {
    //     return (
    //         <div className="md:hidden">
    //             <LoadingTable
    //                 rowCount={rowCount || 10}
    //                 columns={[
    //                     {
    //                         type: 'id',
    //                         width: 20,
    //                     },
    //                     {
    //                         name: "Order",
    //                         type: "number",
    //                         width: 60,
    //                     },
    //                     {
    //                         name: "Validator",
    //                         className: "text-left",
    //                     },
    //                     {
    //                         name: "Status",
    //                         width: 374,
    //                         type: "badge",
    //                         className: "text-left",
    //                     },
    //                     {
    //                         name: "Time to Forge",
    //                         width: 160,
    //                     },
    //                     {
    //                         name: "Block Height",
    //                         className: "text-right",
    //                     },
    //                 ]}
    //             />
    //         </div>
    //     );
    // }

    const { isFavorite } = useValidatorFavorites();

    const unfavoritedValidators = (validators || []).filter((validator) => ! isFavorite(validator.wallet.public_key));

    return (
        <div className="md:hidden">
            <MobileFavoritesTable validators={validators} />

            <MobileDivider className="my-6" />

            <MonitorMobileTable validators={unfavoritedValidators} />
        </div>
    );
}
