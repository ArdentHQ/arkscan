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
import { useTranslation } from "react-i18next";
import MissedWarning from "@/Components/Validator/Monitor/MissedWarning";

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
                    <div className="flex items-center space-x-2 min-w-0">
                        <div>
                            <div className="md:hidden lg:block">
                                <Address wallet={validator.wallet} />
                            </div>

                            <div className="hidden md:block lg:hidden">
                                <Address wallet={validator.wallet} truncate />
                            </div>
                        </div>

                        <MissedWarning validator={validator} />
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
    const { t } = useTranslation();
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
                                    {t('tables.validator-monitor.order')}
                                </th>

                                <th className="text-left">
                                    {t('tables.validator-monitor.validator')}
                                </th>

                                <th className="table-cell text-left hidden md:table-cell md-lg:hidden">
                                    {t('tables.validator-monitor.status_time_to_forge')}
                                </th>

                                <th className="table-cell text-left md:hidden md-lg:table-cell">
                                    {t('tables.validator-monitor.status')}
                                </th>

                                <th className="table-cell whitespace-nowrap md:hidden md-lg:table-cell">
                                    {t('tables.validator-monitor.time_to_forge')}
                                </th>

                                <th className="text-right whitespace-nowrap">
                                    {t('tables.validator-monitor.block_height')}
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
        const { t } = useTranslation();

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
                            name: t('tables.validator-monitor.order'),
                            type: "number",
                            className: "w-[60px]",
                        },
                        {
                            name: t('tables.validator-monitor.validator'),
                            className: "text-left",
                        },
                        {
                            name: t('tables.validator-monitor.status_time_to_forge'),
                            type: "badge",
                            className: "text-left hidden md:table-cell md-lg:hidden w-[180px]",
                        },
                        {
                            name: t('tables.validator-monitor.status'),
                            type: "badge",
                            className: "text-left md:hidden md-lg:table-cell w-[180px] xl:w-[374px]",
                        },
                        {
                            name: t('tables.validator-monitor.time_to_forge'),
                            className: "md:hidden md-lg:table-cell w-[160px]",
                        },
                        {
                            name: t('tables.validator-monitor.block_height'),
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
