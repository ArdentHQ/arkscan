import Status from "@/Components/Validator/Monitor/Status";
import TimeToForge from "@/Components/Validator/Monitor/TimeToForge";
import Address from "@/Components/Wallet/Address";
import TableCell from "../TableCell";
import FavoriteIcon from "@/Components/Validator/Monitor/FavoriteIcon";
import { useValidatorFavorites } from "@/Providers/ValidatorFavorites/ValidatorFavoritesContext";
import classNames from "classnames";
import LoadingTable from "../LoadingTable";
import BlockHeight from "@/Components/Validator/Monitor/BlockHeight";
import ValidatorStatusProvider from "@/Providers/ValidatorStatus/ValidatorStatusProvider";
import { IValidator } from "@/types";
import { useTranslation } from "react-i18next";
import MissedWarning from "@/Components/Validator/Monitor/MissedWarning";

export function MonitorRow({
    validator,
    withFavoriteBorder = true,
}: {
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
                <TableCell className="w-[20px] text-center">
                    <FavoriteIcon validator={validator} />
                </TableCell>

                <TableCell className="w-[60px]">{validator.order}</TableCell>

                <TableCell className="text-left">
                    <div className="flex min-w-0 items-center space-x-2">
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

                <TableCell className="table-cell hidden w-[180px] text-left md:table-cell md-lg:hidden">
                    <Status validator={validator} withTime />
                </TableCell>

                <TableCell className="table-cell w-[180px] text-left md:hidden md-lg:table-cell xl:w-[374px]">
                    <Status validator={validator} />
                </TableCell>

                <TableCell className="w-[160px] whitespace-nowrap text-left md:table-cell md:hidden md-lg:table-cell">
                    <TimeToForge validator={validator} />
                </TableCell>

                <TableCell className="w-[100px] text-right">
                    <BlockHeight validator={validator} />
                </TableCell>
            </ValidatorStatusProvider>
        </tr>
    );
}

export function MonitorTable({
    validators,
    overflowValidators,
}: {
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
        <div className="px-6 pb-8 pt-6 md:mx-auto md:max-w-7xl md:px-10 md:pt-0">
            <div className="validator-monitor hidden w-full overflow-hidden rounded-b-xl rounded-t-xl border border-theme-secondary-300 dark:border-theme-dark-700 md:block">
                <div className="table-container table-encapsulated encapsulated-table-header-gradient px-6">
                    <table>
                        <thead>
                            <tr className="text-sm">
                                <th sorting-id="header-favorite">
                                    <span></span>
                                </th>

                                <th sorting-id="header-order">{t("tables.validator-monitor.order")}</th>

                                <th className="text-left">{t("tables.validator-monitor.validator")}</th>

                                <th className="table-cell hidden text-left md:table-cell md-lg:hidden">
                                    {t("tables.validator-monitor.status_time_to_forge")}
                                </th>

                                <th className="table-cell text-left md:hidden md-lg:table-cell">
                                    {t("tables.validator-monitor.status")}
                                </th>

                                <th className="table-cell whitespace-nowrap md:hidden md-lg:table-cell">
                                    {t("tables.validator-monitor.time_to_forge")}
                                </th>

                                <th className="whitespace-nowrap text-right">
                                    {t("tables.validator-monitor.block_height")}
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

                    {overflowValidators.length > 0 && (
                        <>
                            <table>
                                <tbody>
                                    {overflowValidators.map((validator, index) => (
                                        <MonitorRow key={index} validator={validator} withFavoriteBorder={false} />
                                    ))}
                                </tbody>
                            </table>

                            <div className="-mx-6 h-[5px] bg-theme-secondary-300 dark:bg-theme-dark-700"></div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
}

export default function MonitorTableWrapper({
    validators,
    overflowValidators,
    rowCount,
}: {
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
                    indicatorHeight="h-[20px]"
                    columns={[
                        {
                            type: "id",
                            className: "w-[20px]",
                        },
                        {
                            name: t("tables.validator-monitor.order"),
                            type: "number",
                            className: "w-[60px]",
                        },
                        {
                            name: t("tables.validator-monitor.validator"),
                            className: "text-left",
                        },
                        {
                            name: t("tables.validator-monitor.status_time_to_forge"),
                            type: "badge",
                            className: "text-left hidden md:table-cell md-lg:hidden w-[180px]",
                        },
                        {
                            name: t("tables.validator-monitor.status"),
                            type: "badge",
                            className: "text-left md:hidden md-lg:table-cell w-[180px] xl:w-[374px]",
                        },
                        {
                            name: t("tables.validator-monitor.time_to_forge"),
                            className: "md:hidden md-lg:table-cell w-[160px]",
                        },
                        {
                            name: t("tables.validator-monitor.block_height"),
                            className: "text-right w-[100px]",
                        },
                    ]}
                />
            </div>
        );
    }

    return (
        <div className="hidden md:block">
            <MonitorTable validators={validators} overflowValidators={overflowValidators} />
        </div>
    );
}
