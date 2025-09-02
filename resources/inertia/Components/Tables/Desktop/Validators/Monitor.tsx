import Number from "@/Components/General/Number";
import Status from "@/Components/Validator/Monitor/Status";
import TimeToForge from "@/Components/Validator/Monitor/TimeToForge";
import Address from "@/Components/Wallet/Address";
import TableCell from "../TableCell";
import FavoriteIcon from "@/Components/Validator/Monitor/FavoriteIcon";
import { useValidatorFavorites } from "@/Providers/ValidatorFavorites/ValidatorFavoritesContext";
import classNames from "@/utils/class-names";
import LoadingTable from "../LoadingTable";
import BlockHeight from "@/Components/Validator/Monitor/BlockHeight";
import ValidatorStatusProvider from "@/Providers/ValidatorStatus/ValidatorStatusProvider";
import { IValidator } from "@/types";

export function MonitorRow({ validator, withFavoriteBorder = true }: {
    validator: IValidator;
    withFavoriteBorder?: boolean;
}) {
    const { isFavorite } = useValidatorFavorites();

    return (
        <tr
            className={classNames({
                "text-sm font-semibold": true,
                "validator-monitor-favorite": withFavoriteBorder && isFavorite(validator.wallet.public_key),
            })}
        >
            <ValidatorStatusProvider forgingAt={validator.forgingAt} validator={validator}>
                <TableCell className="text-center w-[20px]">
                    <FavoriteIcon validator={validator} />
                </TableCell>

                <TableCell className="w-[60px]">
                    {validator.order}
                </TableCell>

                <TableCell className="text-left">
                    <div className="md:hidden lg:block">
                        <Address wallet={validator.wallet} />
                    </div>

                    <div className="hidden md:block lg:hidden">
                        <Address wallet={validator.wallet} truncate />
                    </div>
                </TableCell>

                <TableCell className="table-cell text-left hidden md:table-cell md-lg:hidden w-[180px]">
                    <Status validator={validator} withTime />
                </TableCell>

                <TableCell className="table-cell text-left md:hidden md-lg:table-cell w-[180px] xl:w-[374px]">
                    <Status validator={validator} />
                </TableCell>

                <TableCell className="md:table-cell text-left whitespace-nowrap md:hidden md-lg:table-cell w-[160px]">
                    <TimeToForge validator={validator} />
                </TableCell>

                <TableCell className="text-right w-[100px]">
                    <BlockHeight validator={validator} />
                </TableCell>
            </ValidatorStatusProvider>
        </tr>
    );
}

export function MonitorTable({ validators, overflowValidators }: {
    validators: IValidator[];
    overflowValidators: IValidator[];
}) {
    const { isFavorite } = useValidatorFavorites();

    const sortedValidators = [...validators].sort((a, b) => {
        const aIsFavorite = isFavorite(a.wallet.public_key);
        const bIsFavorite = isFavorite(b.wallet.public_key);

        if (aIsFavorite === bIsFavorite) {
            return a.order - b.order;
        }

        if (aIsFavorite) {
            return -1;
        }

        return 1;
    });

    return (
        <div className="px-6 pt-6 pb-8 md:px-10 md:pt-0 md:mx-auto md:max-w-7xl">
            <div className="border border-theme-secondary-300 dark:border-theme-dark-700 overflow-hidden rounded-t-xl rounded-b-xl hidden w-full md:block validator-monitor">
                <div className="px-6 table-container table-encapsulated encapsulated-table-header-gradient">
                    <table>
                        <thead>
                            <tr className="text-sm">
                                <th sorting-id="header-favorite">
                                    <span></span>
                                </th>

                                <th sorting-id="header-order">
                                    Order
                                </th>

                                <th className="text-left">
                                    Validator
                                </th>

                                <th className="table-cell text-left hidden md:table-cell md-lg:hidden">
                                    Status / Time to Forge
                                </th>

                                <th className="table-cell text-left md:hidden md-lg:table-cell">
                                    Status
                                </th>

                                <th className="table-cell whitespace-nowrap md:hidden md-lg:table-cell">
                                    Time to Forge
                                </th>

                                <th className="text-right whitespace-nowrap">
                                    Block Height
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {sortedValidators.map((validator, index) => (
                                <MonitorRow
                                    key={index}
                                    validator={validator}
                                />
                            ))}
                        </tbody>
                    </table>

                    <div className="-mx-6 h-[5px] bg-theme-secondary-300 dark:bg-theme-dark-700"></div>

                    {overflowValidators.length > 0 && (<>
                        <table>
                            <tbody>
                                {overflowValidators.map((validator, index) => (
                                    <MonitorRow
                                        key={index}
                                        validator={validator}
                                        withFavoriteBorder={false}
                                    />
                                ))}
                            </tbody>
                        </table>

                        <div className="-mx-6 h-[5px] bg-theme-secondary-300 dark:bg-theme-dark-700"></div>
                    </>)}
                </div>
            </div>
        </div>
    );
}

export default function MonitorTableWrapper({ validators, overflowValidators, rowCount }: {
    validators: IValidator[];
    overflowValidators: IValidator[];
    rowCount: number;
}) {
    if (!validators || validators.length === 0) {
        return (
            <div className="hidden md:block">
                <LoadingTable
                    rowCount={rowCount}
                    columns={[
                        {
                            type: 'id',
                            className: "w-[20px]",
                        },
                        {
                            name: "Order",
                            type: "number",
                            className: "w-[60px]",
                        },
                        {
                            name: "Validator",
                            className: "text-left",
                        },
                        {
                            name: "Status / Time to Forge",
                            type: "badge",
                            className: "text-left hidden md:table-cell md-lg:hidden w-[180px]",
                        },
                        {
                            name: "Status",
                            type: "badge",
                            className: "text-left md:hidden md-lg:table-cell w-[180px] xl:w-[374px]",
                        },
                        {
                            name: "Time to Forge",
                            className: "md:hidden md-lg:table-cell w-[160px]",
                        },
                        {
                            name: "Block Height",
                            className: "text-right w-[100px]",
                        },
                    ]}
                />
            </div>
        );
    }

    return (
        <div className="hidden md:block">
            <MonitorTable
                validators={validators}
                overflowValidators={overflowValidators}
            />
        </div>
    );
}
