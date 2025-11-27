import { createContext, useContext } from "react";
import { SearchResult } from "@/Components/General/NavbarSearch/NavbarResults";
import { useEffect, useState } from "react";

interface INavbarContext {
    query: string;
    setQuery: (query: string) => void;
    results: SearchResult[];
    hasResults: boolean;
    isLoading: boolean;
    clear: () => void;
}

const NavbarContext = createContext<INavbarContext | null>(null);

export function NavbarProvider({ children }: { children: React.ReactNode }) {
    const [query, setQuery] = useState("");
    const [results, setResults] = useState<SearchResult[]>([]);
    const [hasResults, setHasResults] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    const clear = () => {
        setQuery("");
        setResults([]);
        setHasResults(false);
    };

    useEffect(() => {
        if (!query) {
            setResults([]);
            setHasResults(false);
            setIsLoading(false);

            return;
        }

        const controller = new AbortController();
        setIsLoading(true);
        setResults([]);
        setHasResults(false);

        const timeoutId = window.setTimeout(async () => {
            try {
                const response = await fetch(`/navbar/search?query=${encodeURIComponent(query)}`, {
                    signal: controller.signal,
                    headers: {
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    throw new Error("Unable to fetch search results");
                }

                const data = await response.json();
                setResults(data.results ?? []);
                setHasResults(Boolean(data.hasResults));
            } catch (error) {
                if (!controller.signal.aborted) {
                    setResults([]);
                    setHasResults(false);
                }
            } finally {
                if (!controller.signal.aborted) {
                    setIsLoading(false);
                }
            }
        }, 200);

        return () => {
            controller.abort();
            window.clearTimeout(timeoutId);
        };
    }, [query]);

    return (
        <NavbarContext.Provider
            value={{
                clear,
                query,
                setQuery,
                results,
                hasResults,
                isLoading,
            }}
        >
            {children}
        </NavbarContext.Provider>
    );
}

export function useNavbar() {
    const context = useContext(NavbarContext);
    if (!context) {
        throw new Error("useNavbar must be used within a NavbarProvider");
    }

    return context;
}
