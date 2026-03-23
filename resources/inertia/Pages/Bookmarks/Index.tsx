import { IBlock, ITransaction, IWallet } from "@/types/generated";
import { useEffect, useRef, useState } from "react";

import BookmarkAddressesTable from "@/Components/Tables/Desktop/Bookmarks/Addresses";
import BookmarkBlocksTable from "@/Components/Tables/Desktop/Bookmarks/Blocks";
import BookmarkTransactionsTable from "@/Components/Tables/Desktop/Bookmarks/Transactions";
import { BookmarkType } from "@/Providers/Bookmarks/types";
import HeaderBanner from "@/Components/Bookmarks/HeaderBanner";
import { IPaginatedResponse } from "@/types";
import { ITabsQueryString } from "@/Providers/Tabs/types";
import Layout from "@/Layout";
import MobileDivider from "@/Components/General/MobileDivider";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { PageProps } from "@inertiajs/core";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { router } from "@inertiajs/react";
import { useBookmarks } from "@/Providers/Bookmarks/BookmarksContext";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useTranslation } from "react-i18next";

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
    const [loading, setLoading] = useState<Record<string, boolean>>({});
    const loadedTabs = useRef<Record<string, boolean>>({});

    useEffect(() => {
        if (!currentTab || loadedTabs.current[currentTab]) {
            return;
        }

        loadedTabs.current[currentTab] = true;

        setLoading((prev) => ({ ...prev, [currentTab]: true }));

        router.reload({
            only: [currentTab],
            headers: {
                "X-Bookmarks": JSON.stringify({ [currentTab]: getBookmarks(currentTab as BookmarkType) }),
            },
            onFinish: () => {
                setLoading((prev) => ({ ...prev, [currentTab]: false }));
            },
        });
    }, [currentTab]);

    const bookmarkCount = (type: BookmarkType) => getBookmarks(type).length;
    const skeletonCount = (type: BookmarkType) => Math.min(bookmarkCount(type), 25) || 3;
    const isLoading = (tab: string) => loading[tab] !== false;

    return (
        <div>
            {currentTab === "addresses" && (
                <BookmarkAddressesTable
                    addresses={isLoading("addresses") ? undefined : addresses}
                    rowCount={skeletonCount("addresses")}
                    resultCount={bookmarkCount("addresses")}
                />
            )}
            {currentTab === "transactions" && (
                <BookmarkTransactionsTable
                    transactions={isLoading("transactions") ? undefined : transactions}
                    rowCount={skeletonCount("transactions")}
                    resultCount={bookmarkCount("transactions")}
                />
            )}
            {currentTab === "blocks" && (
                <BookmarkBlocksTable
                    blocks={isLoading("blocks") ? undefined : blocks}
                    rowCount={skeletonCount("blocks")}
                    resultCount={bookmarkCount("blocks")}
                />
            )}
        </div>
    );
}

export default function BookmarksIndex({ addresses, transactions, blocks }: PageProps<BookmarksProps>) {
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
                <BookmarksTabs addresses={addresses} transactions={transactions} blocks={blocks} />
            </TabsProvider>
        </Layout>
    );
}
