import { createContext, useContext } from "react";
import { BookmarksContextType } from "./types";

const BookmarksContext = createContext<BookmarksContextType | null>(null);

export function useBookmarks() {
    const context = useContext(BookmarksContext);
    if (!context) {
        throw new Error("useBookmarks must be used within a BookmarksProvider");
    }

    return context;
}

export default BookmarksContext;
