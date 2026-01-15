import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import HeaderItem from "@/Components/PageHeader/HeaderItem";
import useSharedData from "@/hooks/use-shared-data";
import { HighlightsData } from "@/Pages/Statistics.contracts";

function HighlightStat({
    label,
    value,
    link,
}: {
    label: string;
    value: string;
    link?: { href: string; label: string };
}) {
    return (
        <HeaderItem title={label}>
            <div className="flex flex-grow flex-col justify-between space-y-2">
            <span className="flex whitespace-nowrap text-sm font-semibold !leading-4.25 text-theme-secondary-900 divide-x divide-theme-secondary-300 dark:text-theme-dark-50 dark:divide-theme-dark-700 md:!leading-5 md:text-base space-x-3">
                    <span>{value}</span>

                    {link && (
                        <div className="pl-3">
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
                            value={`${data.totalSupply} ${network.currency}`}
                        />

                        <HighlightStat
                            label={t("pages.statistics.highlights.voting", { percent: data.voting.percentage })}
                            value={`${data.voting.value} ${network.currency}`}
                        />

                        <HighlightStat
                            label={t("pages.statistics.highlights.validators")}
                            value={data.validators}
                            link={{ href: route("validators"), label: t("actions.view_all") }}
                        />

                        <HighlightStat
                            label={t("pages.statistics.highlights.addresses")}
                            value={data.wallets}
                            link={{ href: route("top-accounts"), label: t("actions.view_all") }}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
