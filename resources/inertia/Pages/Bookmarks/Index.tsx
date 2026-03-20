import { useTranslation } from "react-i18next";
import Layout from "@/Layout";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { ITabsQueryString } from "@/Providers/Tabs/types";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import BookmarkAddressesTable from "@/Components/Tables/Desktop/Bookmarks/Addresses";
import BookmarkTransactionsTable from "@/Components/Tables/Desktop/Bookmarks/Transactions";
import BookmarkBlocksTable from "@/Components/Tables/Desktop/Bookmarks/Blocks";

const tabs = [
    { text: "Addresses", value: "addresses" },
    { text: "Transactions", value: "transactions" },
    { text: "Blocks", value: "blocks" },
];

const queryStringDefaults: ITabsQueryString = {
    addresses: {},
    transactions: {},
    blocks: {},
};

function BookmarksTabs() {
    const { currentTab } = useTabs();

    return (
        <div>
            {currentTab === "addresses" && <BookmarkAddressesTable />}
            {currentTab === "transactions" && <BookmarkTransactionsTable />}
            {currentTab === "blocks" && <BookmarkBlocksTable />}
        </div>
    );
}

export default function BookmarksIndex() {
    const { t } = useTranslation();

    return (
        <Layout>
            <TabsProvider
                defaultSelected="addresses"
                queryStringDefaults={queryStringDefaults}
                tabs={tabs}
                baseUrl="/bookmarks"
                useQueryParam
                ariaLabel={t("metatags.bookmarks.title")}
            >
                <BookmarksTabs />
            </TabsProvider>
        </Layout>
    );
}
