import { useEffect } from "react";
import { useTranslation } from "react-i18next";
import { router } from "@inertiajs/react";
import { PageProps } from "@inertiajs/core";
import Layout from "@/Layout";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { ITabsQueryString } from "@/Providers/Tabs/types";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useBookmarks } from "@/Providers/Bookmarks/BookmarksContext";
import { IPaginatedResponse } from "@/types";
import { IBlock, ITransaction, IWallet } from "@/types/generated";
import BookmarkAddressesTable from "@/Components/Tables/Desktop/Bookmarks/Addresses";
import BookmarkTransactionsTable from "@/Components/Tables/Desktop/Bookmarks/Transactions";
import BookmarkBlocksTable from "@/Components/Tables/Desktop/Bookmarks/Blocks";

interface BookmarksProps {
    addresses?: IPaginatedResponse<IWallet>;
    transactions?: IPaginatedResponse<ITransaction>;
    blocks?: IPaginatedResponse<IBlock>;
}

const tabs = [
    { text: "Addresses", value: "addresses" },
    { text: "Transactions", value: "transactions" },
    { text: "Blocks", value: "blocks" },
];

const queryStringDefaults: ITabsQueryString = {
    addresses: { page: 1, "per-page": 25 },
    transactions: { page: 1, "per-page": 25 },
    blocks: { page: 1, "per-page": 25 },
};

function BookmarksTabs({ addresses, transactions, blocks }: BookmarksProps) {
    const { currentTab } = useTabs();
    const { getBookmarks } = useBookmarks();

    useEffect(() => {
        if (!currentTab) {
            return;
        }

        const bookmarkIds = getBookmarks(currentTab as "addresses" | "transactions" | "blocks");

        router.reload({
            only: [currentTab],
            headers: {
                "X-Bookmarks": JSON.stringify({ [currentTab]: bookmarkIds }),
            },
        });
    }, [currentTab]);

    return (
        <div>
            {currentTab === "addresses" && <BookmarkAddressesTable addresses={addresses} />}
            {currentTab === "transactions" && <BookmarkTransactionsTable transactions={transactions} />}
            {currentTab === "blocks" && <BookmarkBlocksTable blocks={blocks} />}
        </div>
    );
}

export default function BookmarksIndex({ addresses, transactions, blocks }: PageProps<BookmarksProps>) {
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
                <BookmarksTabs addresses={addresses} transactions={transactions} blocks={blocks} />
            </TabsProvider>
        </Layout>
    );
}
