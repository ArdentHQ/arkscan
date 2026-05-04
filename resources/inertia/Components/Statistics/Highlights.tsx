import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import HeaderItem from "@/Components/PageHeader/HeaderItem";
import useSharedData from "@/hooks/use-shared-data";
import { HighlightsData } from "@/Pages/Statistics.contracts";
import { networkCurrency } from "@/utils/number-formatter";
import Number from "@/Components/General/Number";

function HighlightStat({
    label,
    value,
    link,
}: {
    label: string;
    value: React.ReactNode;
    link?: { href: string; label: string };
}) {
    return (
        <HeaderItem title={label}>
            <div className="flex flex-grow flex-col justify-between space-y-2">
                <span className="divide-theme-secondary-300 text-theme-secondary-900 dark:divide-theme-dark-700 dark:text-theme-dark-50 flex space-x-3 divide-x text-sm leading-4.25! font-semibold whitespace-nowrap md:text-base md:leading-5!">
                    <span className="pr-3">{value}</span>

                    {link && (
                        <div>
                            <Link href={link.href} className="link">
                                {link.label}
                            </Link>
                        </div>
                    )}
                </span>
            </div>
        </HeaderItem>
    );
}

export default function Highlights({ data }: { data: HighlightsData }) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <div className="pb-6 md:pb-0">
            <div className="content-container-full-width">
                <div className="w-full px-6 md:px-10">
                    <div className="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 md:gap-3 xl:grid-cols-4">
                        <HighlightStat
                            label={t("pages.statistics.highlights.total_supply")}
                            value={networkCurrency(data.totalSupply, 8, true)}
                        />

                        <HighlightStat
                            label={t("pages.statistics.highlights.voting", {
                                percent: `${data.voting.percentage.toFixed(2)}%`,
                            })}
                            value={networkCurrency(data.voting.value, 0, true)}
                        />

                        <HighlightStat
                            label={t("pages.statistics.highlights.validators")}
                            value={<Number>{data.validators}</Number>}
                            link={{ href: route("validators"), label: t("actions.view_all") }}
                        />

                        <HighlightStat
                            label={t("pages.statistics.highlights.addresses")}
                            value={<Number>{data.wallets}</Number>}
                            link={{ href: route("top-accounts"), label: t("actions.view_all") }}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
