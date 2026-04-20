import RecentVotesTableWrapper from "@/Components/Tables/Desktop/Validators/RecentVotes";
import RecentVotesMobileTableWrapper from "@/Components/Tables/Mobile/Validators/RecentVotes";
import FilterProvider from "@/Providers/Filter/FilterProvider";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { useTranslation } from "react-i18next";

export default function RecentVotesTab({ recentVotes, filters }: Pick<ValidatorsProps, "recentVotes" | "filters">) {
    const { t } = useTranslation();

    return (
        <FilterProvider
            initialOptions={[
                {
                    label: t("tables.filters.recent-votes.vote"),
                    value: "vote",
                    selected: filters["recent-votes"].vote,
                },
                {
                    label: t("tables.filters.recent-votes.unvote"),
                    value: "unvote",
                    selected: filters["recent-votes"].unvote,
                },
            ]}
        >
            <RecentVotesTableWrapper
                recentVotes={recentVotes}
                mobile={<RecentVotesMobileTableWrapper recentVotes={recentVotes} />}
            />
        </FilterProvider>
    );
}
