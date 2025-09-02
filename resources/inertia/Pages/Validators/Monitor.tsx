import { router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import PageHeader from "@/Components/General/PageHeader";
import MonitorTableWrapper from "@/Components/Tables/Desktop/Validators/Monitor";
import HeaderStats from "@/Components/Validator/Monitor/HeaderStats";
import ValidatorFavoritesProvider from "@/Providers/ValidatorFavorites/ValidatorFavoritesProvider";
import MonitorMobileTableWrapper from "@/Components/Tables/Mobile/Validators/Monitor";
import MobileDivider from "@/Components/General/MobileDivider";
import { IValidatorData } from "@/types";
import MissedBlocksTrackerProvider from "@/Providers/MissedBlocksTracker/MissedBlocksTrackerProvider";

export default function Monitor({ validatorData, height, rowCount }: {
    validatorData: IValidatorData;
    height: number;
    rowCount: number;
}) {
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
            title="Validator Monitor"
            subtitle="Validator block production observer tool"
        />

        <HeaderStats
            height={height}
            statistics={validatorData?.statistics}
        />

        <ValidatorFavoritesProvider>
            <MissedBlocksTrackerProvider validators={[
                ...(validatorData?.validators ?? []),
                ...(validatorData?.overflowValidators ?? []),
            ]}>
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
            </MissedBlocksTrackerProvider>
        </ValidatorFavoritesProvider>
    </>);
}
