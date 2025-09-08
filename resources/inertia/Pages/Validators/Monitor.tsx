import { router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import PageHeader from "@/Components/General/PageHeader";
import MonitorTableWrapper from "@/Components/Tables/Desktop/Validators/Monitor";
import HeaderStats from "@/Components/Validator/Monitor/HeaderStats";
import ValidatorFavoritesProvider from "@/Providers/ValidatorFavorites/ValidatorFavoritesProvider";
import MonitorMobileTableWrapper from "@/Components/Tables/Mobile/Validators/Monitor";
import MobileDivider from "@/Components/General/MobileDivider";
import { IValidatorData } from "@/types";
import { useTranslation } from "react-i18next";

export default function Monitor({ validatorData, height, rowCount }: {
    validatorData: IValidatorData;
    height: number;
    rowCount: number;
}) {
    const { t } = useTranslation();
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        router.on('success', () => {
            pollingTimerRef.current = setTimeout(pollValidators, 2000);
        });

        const pollValidators = () => {
            router.reload({
                only: [
                    'height',
                    'validatorData',
                ],
            });
        };

        pollingTimerRef.current = setTimeout(pollValidators, 2000);

        return () => {
            if (! pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        }
    }, []);

    return (<>
        <PageHeader
            title={t('pages.validator-monitor.title')}
            subtitle={t('pages.validator-monitor.subtitle')}
        />

        <HeaderStats
            height={height}
            statistics={validatorData?.statistics}
        />

        <ValidatorFavoritesProvider>
            <MonitorTableWrapper
                validators={validatorData?.validators}
                overflowValidators={validatorData?.overflowValidators}
                rowCount={rowCount}
            />

            <MobileDivider />

            <div className="pt-6 pb-8 md:pt-0 md:mx-auto md:max-w-7xl">
                <MonitorMobileTableWrapper
                    validators={validatorData?.validators}
                    rowCount={rowCount}
                />
            </div>
        </ValidatorFavoritesProvider>
    </>);
}
