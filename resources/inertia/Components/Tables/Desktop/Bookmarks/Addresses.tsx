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

export default function BookmarkAddressesTable() {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            withHeader
            paginator={emptyPaginator}
            rowComponent={Row}
            noResultsMessage={t("tables.bookmarks.addresses.no_results")}
            columns={
                <>
                    <TableHeader type="id" className="whitespace-nowrap">
                        {t("general.wallet.address")}
                    </TableHeader>

                    <TableHeader>{t("general.wallet.name")}</TableHeader>

                    <TableHeader className="text-center" breakpoint="md-lg" responsive>
                        {t("general.wallet.type")}
                    </TableHeader>

                    <TableHeader className="text-center" breakpoint="lg" responsive>
                        {t("general.wallet.voting")}
                    </TableHeader>

                    <TableHeader className="last-until-lg text-right" lastOn="lg">
                        {t("general.wallet.balance_currency", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader
                        className="text-right"
                        breakpoint="md-lg"
                        responsive
                        type="number"
                        tooltip={t("pages.wallets.percentage_tooltip")}
                    >
                        {t("general.wallet.percentage")}
                    </TableHeader>
                </>
            }
        />
    );
}
