import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { Table } from "../Table";
import TableHeader from "../TableHeader";

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

export default function BookmarkTransactionsTable() {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            withHeader
            paginator={emptyPaginator}
            rowComponent={Row}
            noResultsMessage={t("tables.bookmarks.transactions.no_results")}
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>

                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.transactions.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.transactions.method")}</TableHeader>

                    <TableHeader>{t("tables.transactions.addressing")}</TableHeader>

                    <TableHeader className="last-until-lg text-right" lastOn="lg">
                        {t("tables.transactions.amount", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader className="text-right" responsive breakpoint="lg">
                        {t("tables.transactions.fee", {
                            currency: network!.currency,
                        })}
                    </TableHeader>
                </>
            }
        />
    );
}
