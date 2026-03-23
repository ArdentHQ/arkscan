export type BookmarkType = "addresses" | "transactions" | "blocks";

export interface BookmarksContextType {
    isBookmarked: (type: BookmarkType, id: string) => boolean;
    toggle: (type: BookmarkType, id: string) => void;
    getBookmarks: (type: BookmarkType) => string[];
}
