import Number from "@/Components/General/Number";
import Status from "@/Components/Validator/Monitor/Status";
import TimeToForge from "@/Components/Validator/Monitor/TimeToForge";
import Address from "@/Components/Wallet/Address";
import TableCell from "../../TableCell";
import FavoriteIcon from "@/Components/Validator/Monitor/FavoriteIcon";
import { useValidatorFavorites } from "@/Providers/ValidatorFavorites/ValidatorFavoritesContext";
import classNames from "@/utils/class-names";
import LoadingTable from "../LoadingTable";

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
                <Address wallet={validator.wallet} />
            </TableCell>

            <TableCell
                className="table-cell text-left"
                width={374}
            >
                <Status wallet={validator.wallet} />
            </TableCell>

            <TableCell
                className="md:table-cell text-left whitespace-nowrap"
                width={160}
            >
                <TimeToForge forgingAt={validator.forgingAt} wallet={validator.wallet} />
            </TableCell>

            <TableCell
                className="text-right"
                width={100}
            >
                {validator.wallet.hasForged ? (
                    <a
                        href={`/blocks/${validator?.lastBlock?.hash}`}
                        className="link"
                    >
                        <Number>{validator?.lastBlock?.number}</Number>
                    </a>
                ) : (
                    <span className="text-theme-secondary-500 dark:text-theme-dark-500">
                        {validator.wallet.justMissed ? 'N/A' : 'TBD'}
                    </span>
                )}
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
                                    <span>Order</span>
                                </th>

                                <th className="text-left">
                                    Validator
                                </th>

                                <th className="table-cell text-left">
                                    <div>Status</div>
                                </th>

                                <th className="table-cell whitespace-nowrap">
                                    Time to Forge
                                </th>

                                <th className="text-right whitespace-nowrap">
                                    <span>Block Height</span>
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
            <LoadingTable
                rowCount={rowCount || 10}
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
                        name: "Status",
                        width: 374,
                        type: "badge",
                        className: "text-left",
                    },
                    {
                        name: "Time to Forge",
                        width: 160,
                    },
                    {
                        name: "Block Height",
                        className: "text-right",
                    },
                ]}
            />
        );
    }

    return (
        <MonitorTable
            validators={validators}
            overflowValidators={overflowValidators}
        />
    );
}
