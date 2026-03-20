import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import classNames from "classnames";

// TODO: replace with real data from localStorage
const emptyPaginator = {
    data: [],
    current_page: 1,
    first_page_url: "",
    from: 0,
    last_page: 1,
    last_page_url: "",
    links: [],
    meta: { pageName: "page", urlParams: {} },
    next_page_url: null,
    path: "",
    per_page: 25,
    prev_page_url: null,
    to: 0,
    total: 0,
    noResultsMessage: "",
    perPageOptions: null,
};

function Row() {
    return <tr></tr>;
}

export default function BookmarkBlocksTable() {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            withHeader
            paginator={emptyPaginator}
            rowComponent={Row}
            noResultsMessage={t("tables.bookmarks.blocks.no_results")}
            columns={
                <>
                    <TableHeader type="id" className="whitespace-nowrap">
                        {t("tables.blocks.height")}
                    </TableHeader>

                    <TableHeader breakpoint="md-lg" responsive>
                        {t("tables.blocks.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.blocks.generated_by")}</TableHeader>

                    <TableHeader breakpoint="md-lg" responsive className="text-right">
                        {t("tables.blocks.transactions")}
                    </TableHeader>

                    <TableHeader
                        className={classNames({
                            "whitespace-nowrap text-right": true,
                            "last-until-lg": !!network?.canBeExchanged,
                        })}
                        lastOn={network?.canBeExchanged ? "lg" : undefined}
                        tooltip={t("pages.wallets.blocks.total_reward_tooltip", {
                            currency: network!.currency,
                        })}
                        type="number"
                    >
                        {t("tables.blocks.total_reward", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    {network?.canBeExchanged && (
                        <TableHeader
                            className="whitespace-nowrap text-right"
                            breakpoint="lg"
                            responsive
                            tooltip={t("pages.wallets.blocks.value_tooltip", {
                                currency: network!.currency,
                            })}
                            type="number"
                        >
                            {t("tables.blocks.value", {
                                currency: network!.currency,
                            })}
                        </TableHeader>
                    )}
                </>
            }
        />
    );
}
