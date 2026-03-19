import { useTranslation } from "react-i18next";
import Layout from "@/Layout";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { ITabsQueryString } from "@/Providers/Tabs/types";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import BookmarkAddressesTable from "@/Components/Tables/Desktop/Bookmarks/Addresses";
import BookmarkTransactionsTable from "@/Components/Tables/Desktop/Bookmarks/Transactions";
import BookmarkBlocksTable from "@/Components/Tables/Desktop/Bookmarks/Blocks";
import HeaderBanner from "@/Components/Bookmarks/HeaderBanner";
import PageHeader from "@/Components/PageHeader/PageHeader";
import MobileDivider from "@/Components/General/MobileDivider";

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
            <PageHeader title={t("pages.bookmarks.title")} subtitle={t("pages.bookmarks.subtitle")} />

            <div className="mb-6 px-6 md:mx-auto md:max-w-7xl md:px-10">
                <HeaderBanner />
            </div>

            <MobileDivider className="mb-6" />

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
