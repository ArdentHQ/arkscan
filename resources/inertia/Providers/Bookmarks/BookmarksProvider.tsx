import { useEffect, useState } from "react";
import BookmarksContext from "./BookmarksContext";
import { BookmarkType } from "./types";

const STORAGE_KEYS: Record<BookmarkType, string> = {
    addresses: "bookmarks:addresses",
    transactions: "bookmarks:transactions",
    blocks: "bookmarks:blocks",
};

const STORAGE_KEY_TO_TYPE: Record<string, BookmarkType> = Object.fromEntries(
    Object.entries(STORAGE_KEYS).map(([type, key]) => [key, type as BookmarkType]),
);

function readFromStorage(): Record<BookmarkType, string[]> {
    return {
        addresses: JSON.parse(localStorage.getItem(STORAGE_KEYS.addresses) || "[]"),
        transactions: JSON.parse(localStorage.getItem(STORAGE_KEYS.transactions) || "[]"),
        blocks: JSON.parse(localStorage.getItem(STORAGE_KEYS.blocks) || "[]"),
    };
}

export default function BookmarksProvider({ children }: { children: React.ReactNode }) {
    const [bookmarks, setBookmarks] = useState<Record<BookmarkType, string[]>>({
        addresses: [],
        transactions: [],
        blocks: [],
    });

    useEffect(() => {
        setBookmarks(readFromStorage());

        const onStorageChange = (event: StorageEvent) => {
            const type = event.key ? STORAGE_KEY_TO_TYPE[event.key] : null;

            if (type) {
                setBookmarks((prev) => ({
                    ...prev,
                    [type]: JSON.parse(event.newValue || "[]"),
                }));
            }
        };

        window.addEventListener("storage", onStorageChange);

        return () => window.removeEventListener("storage", onStorageChange);
    }, []);

    const isBookmarked = (type: BookmarkType, id: string) => {
        return bookmarks[type].includes(id);
    };

    const toggle = (type: BookmarkType, id: string) => {
        const current = [...bookmarks[type]];
        const index = current.indexOf(id);

        if (index >= 0) {
            current.splice(index, 1);
        } else {
            current.push(id);
        }

        const updated = { ...bookmarks, [type]: current };
        setBookmarks(updated);
        localStorage.setItem(STORAGE_KEYS[type], JSON.stringify(current));
    };

    const getBookmarks = (type: BookmarkType) => {
        return bookmarks[type];
    };

    return (
        <BookmarksContext.Provider value={{ isBookmarked, toggle, getBookmarks }}>{children}</BookmarksContext.Provider>
    );
}
