import Layout from "@/Layout";
import HomeTransactionsTableWrapper from "@/Components/Home/TransactionsTable";
import { PageProps } from "@inertiajs/core";
import { HomeProps } from "@/Pages/Home.contracts";
import { router } from "@inertiajs/react";
import { useEffect, useRef } from "react";

export default function HomeIndex({ transactions }: PageProps<HomeProps>) {
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        const pollTransactions = () => {
            router.reload({
                only: ["transactions"],
            });
        };

        const removeListener = router.on("success", () => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(pollTransactions, 10000);
        });

        pollTransactions();

        return () => {
            removeListener();

            if (!pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        };
    }, []);

    return (
        <Layout>
            <div className="mt-6 pb-8 md:pb-6">
                <HomeTransactionsTableWrapper transactions={transactions} />
            </div>
        </Layout>
    );
}
