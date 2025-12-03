import Card from "@/Components/General/Card";
import Detail from "@/Components/General/Detail";
import Number from "@/Components/General/Number";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import LoadingText from "@/Components/Loading/Text";
import classNames from "classnames";
import { useTranslation } from "react-i18next";
import Badge from "../General/Badge";
import Currency from "../General/Currency";
import { currency } from "../../utils/number-formatter";
import useShareData from "@/hooks/use-shared-data";
import HeaderItem from "../PageHeader/HeaderItem";

// Import validator header background images
import headerBg from "@images/validators/header-bg.svg";
import headerBgMobile from "@images/validators/header-bg-mobile.svg";
import headerBgDark from "@images/validators/header-bg-dark.svg";
import headerBgMobileDark from "@images/validators/header-bg-mobile-dark.svg";
import headerBgDim from "@images/validators/header-bg-dim.svg";
import headerBgMobileDim from "@images/validators/header-bg-mobile-dim.svg";
import ExternalLink from "../General/ExternalLink";
import { IStatistics } from "@/Pages/Validators.contracts";

function ExploreHeaderStat() {
    const { t } = useTranslation();

    return (
        <HeaderItem
            className="flex-none bg-theme-primary-100 dim:bg-theme-dark-700 dark:bg-theme-dark-800 xl:flex-1"
            background={
                <>
                    <img src={headerBg} className="hidden max-w-none dark:hidden sm:block" />

                    <img src={headerBgMobile} className="max-w-none dark:hidden sm:hidden" />

                    <img src={headerBgDark} className="hidden max-w-none dim:sm:hidden dark:sm:block" />

                    <img src={headerBgMobileDark} className="hidden max-w-none dim:hidden dark:block dark:sm:hidden" />

                    <img src={headerBgDim} className="hidden max-w-none dark:hidden dim:sm:block" />

                    <img src={headerBgMobileDim} className="hidden max-w-none dim:block dark:hidden dim:sm:hidden" />
                </>
            }
        >
            <div className="absolute right-0 top-0 z-10 h-full w-full bg-gradient-to-t from-theme-primary-100 to-theme-primary-200 dim:bg-gradient-to-b dark:from-theme-dark-800 dark:to-theme-dark-700 sm:w-[400px] sm:bg-gradient-to-r dim:sm:bg-gradient-to-l"></div>

            <div className="relative z-30 flex flex-1 flex-col items-center space-y-3 sm:flex-row sm:justify-between sm:space-y-0">
                <div className="flex w-full flex-col space-y-1.5">
                    <div className="text-sm text-theme-primary-900 dark:text-theme-dark-50 md:text-lg md:leading-5.25">
                        {t("pages.validators.explore.title")}
                    </div>

                    <div className="text-xs leading-5 text-theme-secondary-700 dark:text-theme-dark-200 sm:leading-3.75">
                        {t("pages.validators.explore.subtitle")}
                    </div>
                </div>

                <div className="w-full sm:w-auto">
                    {/* TODO: fix icon offset */}
                    <ExternalLink
                        className="button-primary !flex items-center justify-center space-x-2 px-4 py-1.5"
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

export default function HeaderStats({ statistics }: { statistics: IStatistics }) {
    const { t } = useTranslation();
    const { network } = useShareData();

    return (
        <div className="flex flex-col space-y-2 px-6 pb-6 sm:space-y-3 md:mx-auto md:max-w-7xl md:px-10 xl:flex-row xl:space-x-3 xl:space-y-0">
            <div className="flex flex-1 flex-col space-y-2 sm:flex-row sm:space-x-2 sm:space-y-0 md:space-x-3">
                <Card className="flex-1">
                    <Detail
                        title={t("pages.validators.missed-blocks.title")}
                        className="flex space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700"
                    >
                        <div className="flex items-center space-x-2">
                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                {statistics.missedBlocks === 0 ? "-" : <Number>{statistics.missedBlocks}</Number>}
                            </span>

                            <Badge className="py-px">
                                {t(
                                    statistics.validatorsMissed === 1
                                        ? "pages.validators.x_validators_singular"
                                        : "pages.validators.x_validators_plural",
                                    {
                                        count: statistics?.validatorsMissed,
                                    },
                                )}
                            </Badge>
                        </div>

                        <button
                            className="link pl-3 text-sm !leading-5 md:text-base"
                            onClick={() => {
                                // TODO: switch tabs and scroll to table - https://app.clickup.com/t/86dynrhkp
                            }}
                        >
                            {t("actions.view")}
                        </button>
                    </Detail>
                </Card>

                <Card className="flex-1">
                    <Detail
                        title={t("pages.validators.voting_x_addresses", { count: statistics.voterCount })}
                        className="flex items-center space-x-2"
                    >
                        <span>
                            <Currency currency={network!.currency} decimals={0} value={statistics.totalVoted} />
                        </span>

                        <Badge className="py-px">{statistics.votesPercentage.toFixed(2)}%</Badge>
                    </Detail>
                </Card>
            </div>

            <ExploreHeaderStat />
        </div>
    );
}
