import RecentVotesTableWrapper from "@/Components/Tables/Desktop/Validators/RecentVotes";
import RecentVotesMobileTableWrapper from "@/Components/Tables/Mobile/Validators/RecentVotes";
import FilterProvider from "@/Providers/Filter/FilterProvider";
import { ValidatorsProps } from "@/Pages/Validators.contracts";
import { useTranslation } from "react-i18next";

export default function RecentVotesTab({ recentVotes, filters }: Pick<ValidatorsProps, "recentVotes" | "filters">) {
    const { t } = useTranslation();

    return (
        <FilterProvider
            initialOptions={
                [
                    // @TODO: implement validators filters logic https://app.clickup.com/t/86dyqe7cg
                ]
            }
            onChange={() => {}}
        >
            <RecentVotesTableWrapper
                recentVotes={recentVotes}
                mobile={<RecentVotesMobileTableWrapper recentVotes={recentVotes} />}
            />
        </FilterProvider>
    );
}
