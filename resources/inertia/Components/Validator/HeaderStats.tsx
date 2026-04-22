import Card from "@/Components/General/Card";
import Detail from "@/Components/General/Detail";
import Number from "@/Components/General/Number";
import LoadingText from "@/Components/Loading/Text";
import { useTranslation } from "react-i18next";
import Badge from "../General/Badge";
import Currency from "../General/Currency";
import useShareData from "@/hooks/use-shared-data";
import HeaderItem from "../PageHeader/HeaderItem";
import ExternalLink from "../General/ExternalLink";
import { IValidatorsStatistics } from "@/Pages/Validators.contracts";

// Import validator header background images
import headerBg from "@images/validators/header-bg.svg";
import headerBgMobile from "@images/validators/header-bg-mobile.svg";
import headerBgDark from "@images/validators/header-bg-dark.svg";
import headerBgMobileDark from "@images/validators/header-bg-mobile-dark.svg";
import headerBgDim from "@images/validators/header-bg-dim.svg";
import headerBgMobileDim from "@images/validators/header-bg-mobile-dim.svg";
import { Link, router } from "@inertiajs/react";
import { formattedNumber } from "../General/Number";
import { useEffect } from "react";

function ExploreHeaderStat() {
    const { t } = useTranslation();

    return (
        <HeaderItem
            className="bg-theme-primary-100 dim:bg-theme-dark-700 dark:bg-theme-dark-800 flex-none xl:flex-1"
            background={
                <>
                    <img src={headerBg} className="hidden max-w-none sm:block dark:hidden" alt="" />

                    <img src={headerBgMobile} className="max-w-none sm:hidden dark:hidden" alt="" />

                    <img src={headerBgDark} className="dim:sm:hidden hidden max-w-none dark:sm:block" alt="" />

                    <img
                        src={headerBgMobileDark}
                        className="dim:hidden hidden max-w-none dark:block dark:sm:hidden"
                        alt=""
                    />

                    <img src={headerBgDim} className="dim:sm:block hidden max-w-none dark:hidden" alt="" />

                    <img
                        src={headerBgMobileDim}
                        className="dim:block dim:sm:hidden hidden max-w-none dark:hidden"
                        alt=""
                    />
                </>
            }
        >
            <div className="from-theme-primary-100 to-theme-primary-200 dim:bg-gradient-to-b dark:from-theme-dark-800 dark:to-theme-dark-700 dim:sm:bg-gradient-to-l absolute top-0 right-0 z-10 h-full w-full bg-gradient-to-t sm:w-[400px] sm:bg-gradient-to-r"></div>

            <div className="relative z-30 flex flex-1 flex-col items-center space-y-3 sm:flex-row sm:justify-between sm:space-y-0">
                <div className="flex w-full flex-col space-y-1.5">
                    <div className="text-theme-primary-900 dark:text-theme-dark-50 text-sm md:text-lg md:leading-5.25">
                        {t("pages.validators.explore.title")}
                    </div>

                    <div className="text-theme-secondary-700 dark:text-theme-dark-200 text-xs leading-5 sm:leading-3.75">
                        {t("pages.validators.explore.subtitle")}
                    </div>
                </div>

                <div className="w-full sm:w-auto">
                    <ExternalLink
                        className="button-primary flex! items-center justify-center space-x-2 px-4 py-1.5"
                        url={t("urls.docs.validator")}
                        innerClass="leading-5"
                        iconClass="inline relative flex-shrink-0 text-white w-4 h-4"
                    >
                        <span>{t("pages.validators.explore.action")}</span>
                    </ExternalLink>
                </div>
            </div>
        </HeaderItem>
    );
}

export default function HeaderStats({ statistics }: { statistics?: IValidatorsStatistics }) {
    const { t } = useTranslation();
    const { network } = useShareData();
    const isLoading = !statistics;
    const missedBlocks = statistics?.missedBlocks ?? 0;
    const validatorsMissed = statistics?.validatorsMissed ?? 0;
    const votesPercentage = statistics?.votesPercentage ?? 0;
    const voterCountLabel = statistics ? formattedNumber(statistics.voterCount) : "...";
    const totalVoted = statistics?.totalVoted ?? 0;

    useEffect(() => {
        if (statistics) {
            return;
        }

        router.reload({
            only: ["statistics"],
        });
    }, [statistics]);

    return (
        <div className="flex flex-col space-y-2 px-6 pb-6 sm:space-y-3 md:mx-auto md:max-w-7xl md:px-10 xl:flex-row xl:space-y-0 xl:space-x-3">
            <div className="flex flex-1 flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2 md:space-x-3">
                <Card className="flex-1">
                    <Detail
                        title={t("pages.validators.missed-blocks.title")}
                        className="divide-theme-secondary-300 dark:divide-theme-dark-700 flex space-x-3 divide-x"
                    >
                        <div className="flex items-center space-x-2 pr-3">
                            {isLoading ? (
                                <LoadingText width="w-[48px]" height="h-5" />
                            ) : (
                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                    {missedBlocks === 0 ? "-" : <Number>{missedBlocks}</Number>}
                                </span>
                            )}

                            {isLoading ? (
                                <LoadingText width="w-[92px]" height="h-5" />
                            ) : (
                                <Badge className="py-px">
                                    {t(
                                        validatorsMissed === 1
                                            ? "pages.validators.x_validators_singular"
                                            : "pages.validators.x_validators_plural",
                                        {
                                            value: validatorsMissed,
                                        },
                                    )}
                                </Badge>
                            )}
                        </div>

                        <Link
                            className="link text-sm leading-5! md:text-base"
                            href={route("validators", { view: "missed-blocks" }) + "#validators:tabs:content"}
                        >
                            {t("actions.view")}
                        </Link>
                    </Detail>
                </Card>

                <Card className="flex-1">
                    <Detail
                        title={t("pages.validators.voting_x_addresses", {
                            value: voterCountLabel,
                        })}
                        className="flex items-center space-x-2"
                    >
                        {isLoading ? (
                            <LoadingText width="w-[110px]" height="h-5" />
                        ) : (
                            <span>
                                <Currency currency={network!.currency} decimals={0} value={totalVoted} />
                            </span>
                        )}

                        {isLoading ? (
                            <LoadingText width="w-[52px]" height="h-5" />
                        ) : (
                            <Badge className="py-px">{votesPercentage.toFixed(2)}%</Badge>
                        )}
                    </Detail>
                </Card>
            </div>

            <ExploreHeaderStat />
        </div>
    );
}
