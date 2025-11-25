import Status from "@/Components/Validator/Monitor/Status";
import TimeToForge from "@/Components/Validator/Monitor/TimeToForge";
import Address from "@/Components/Wallet/Address";
import FavoriteIcon from "@/Components/Validator/Monitor/FavoriteIcon";
import { useValidatorFavorites } from "@/Providers/ValidatorFavorites/ValidatorFavoritesContext";
import classNames from "classnames";
import MobileTable from "../Table";
import MobileTableRow from "../Row";
import MissedWarning from "@/Components/Validator/Monitor/MissedWarning";
import TableCell from "../TableCell";
import BlockHeight from "@/Components/Validator/Monitor/BlockHeight";
import MobileDivider from "@/Components/General/MobileDivider";
import { MobileMonitorSkeletonTable } from "../Skeleton/Validators/Monitor";
import { IValidator } from "@/types";
import ValidatorStatusProvider from "@/Providers/ValidatorStatus/ValidatorStatusProvider";
import { useTranslation } from "react-i18next";

export function MonitorMobileHeader({ validator }: { validator: IValidator }) {
    return (
        <div className="flex min-w-0 flex-1 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
            <div className="flex items-center">
                <div className="hidden items-center pr-3 sm:flex">
                    <FavoriteIcon validator={validator} />
                </div>

                <span className="min-w-[32px] text-sm font-semibold leading-4.25 dark:text-theme-dark-200">
                    {validator.order}
                </span>
            </div>

            <div className="flex min-w-0 flex-1 items-center justify-between pl-3">
                <div className="flex min-w-0 flex-1 items-center space-x-2">
                    <TableCell className="min-w-0">
                        <Address wallet={validator.wallet} truncate className="sm:hidden" />

                        <Address wallet={validator.wallet} truncate={16} className="hidden sm:block md:hidden" />
                    </TableCell>

                    <MissedWarning
                        validator={validator}
                        testId={`validator-monitor:missed-warning-${validator.wallet.address}:mobile`}
                    />
                </div>

                <div className="flex h-[21px] items-center sm:space-x-3">
                    <div className="flex items-center sm:hidden">
                        <Status withText={false} />
                    </div>

                    <div className="hidden sm:block">
                        <Status />
                    </div>
                </div>
            </div>
        </div>
    );
}

export function MonitorMobileTable({ validators }: { validators: IValidator[] }) {
    const { t } = useTranslation();
    const { isFavorite } = useValidatorFavorites();

    return (
        <MobileTable>
            {validators.map((validator: IValidator, index) => (
                <ValidatorStatusProvider key={index} forgingAt={validator.forgingAt} validator={validator}>
                    <MobileTableRow
                        expandClass={classNames({
                            "space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700":
                                !validator.wallet?.isResigned,
                        })}
                        className={classNames({
                            "validator-monitor-favorite": isFavorite(validator.wallet.public_key),
                        })}
                        expandable={true}
                        header={<MonitorMobileHeader validator={validator} />}
                    >
                        <TableCell label={t("tables.validator-monitor.status")} className="sm:hidden">
                            <Status className="sm:hidden" />
                        </TableCell>

                        <TableCell label={t("tables.validator-monitor.time_to_forge")}>
                            <TimeToForge validator={validator} />
                        </TableCell>

                        <TableCell label={t("tables.validator-monitor.block_height")}>
                            <BlockHeight validator={validator} />
                        </TableCell>

                        <div className="mt-4 border-t border-theme-secondary-300 pt-4 dark:border-theme-dark-700 sm:mt-0 sm:hidden sm:border-t-0 sm:pt-0">
                            <FavoriteIcon validator={validator} label={t("tables.validator-monitor.favorite")} />
                        </div>
                    </MobileTableRow>
                </ValidatorStatusProvider>
            ))}
        </MobileTable>
    );
}

export function MobileFavoritesTable({ validators }: { validators: IValidator[] }) {
    const { isFavorite } = useValidatorFavorites();

    const favoritedValidators = (validators || []).filter((validator) => isFavorite(validator.wallet.public_key));
    if (favoritedValidators.length === 0) {
        return null;
    }

    return (
        <div>
            <div className="px-6 pb-3 font-semibold text-theme-secondary-700 dark:text-theme-dark-200 md:px-10">
                My Favorites
            </div>

            <MonitorMobileTable validators={favoritedValidators} />

            <MobileDivider className="my-6" />
        </div>
    );
}

export default function MonitorMobileTableWrapper({
    validators,
    rowCount,
}: {
    validators: IValidator[];
    rowCount: number;
}) {
    if (!validators || validators.length === 0) {
        return <MobileMonitorSkeletonTable rowCount={rowCount} />;
    }

    const { isFavorite } = useValidatorFavorites();

    const unfavoritedValidators = (validators || []).filter((validator) => !isFavorite(validator.wallet.public_key));

    return (
        <div className="md:hidden">
            <MobileFavoritesTable validators={validators} />

            <MonitorMobileTable validators={unfavoritedValidators} />
        </div>
    );
}
