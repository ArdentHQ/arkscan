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

export function MonitorRow({ validator, withFavoriteBorder = true }: {
    validator: any;
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
            <TableCell
                className="text-center"
                width={20}
            >
                <FavoriteIcon validator={validator} />
            </TableCell>

            <TableCell width={60}>
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

            <TableCell
                className="table-cell text-left hidden md:table-cell md-lg:hidden"
                width={172}
            >
                <Status validator={validator} withTime />
            </TableCell>

            <TableCell
                className="table-cell text-left md:hidden md-lg:table-cell"
                width={374}
            >
                <Status validator={validator} />
            </TableCell>

            <TableCell
                className="md:table-cell text-left whitespace-nowrap md:hidden md-lg:table-cell"
                width={160}
            >
                <TimeToForge forgingAt={validator.forgingAt} wallet={validator.wallet} />
            </TableCell>

            <TableCell
                className="text-right"
                width={100}
            >
                <BlockHeight validator={validator} />
            </TableCell>
        </tr>
    );
}

export function MonitorTable({ validators, overflowValidators }: {
    validators: any[];
    overflowValidators: any[];
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
                                <MonitorRow key={index} validator={validator} />
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
    validators: any[];
    overflowValidators: any[];
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
                            width: 20,
                        },
                        {
                            name: "Order",
                            type: "number",
                            width: 60,
                        },
                        {
                            name: "Validator",
                            className: "text-left",
                        },
                        {
                            name: "Status / Time to Forge",
                            width: 172,
                            type: "badge",
                            className: "text-left hidden md:table-cell md-lg:hidden",
                        },
                        {
                            name: "Status",
                            width: 374,
                            type: "badge",
                            className: "text-left md:hidden md-lg:table-cell",
                        },
                        {
                            name: "Time to Forge",
                            width: 160,
                            className: "md:hidden md-lg:table-cell"
                        },
                        {
                            name: "Block Height",
                            className: "text-right",
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
