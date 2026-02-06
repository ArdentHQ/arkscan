import { useTranslation } from "react-i18next";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import { router, usePoll } from "@inertiajs/react";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { TransactionsProps } from "../Transactions.contracts";
import TokenTransfersTable from "@/Components/Tokens/TransfersTable";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { useEffect } from "react";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";

export default function TransactionsList({ network, statistics }: PageProps<TransactionsProps>) {
    const { t } = useTranslation();
    const { listen, enabled: usesBroadcasting } = useWebhooks();

    useEffect(() => {
        return listen("transactions", "NewTransaction", () => {
            router.reload({
                only: ["transfers"],
            });
        });
    }, []);

    usePoll(
        30 * 1000,
        {
            only: ["transfers"],
        },
        {
            autoStart: !usesBroadcasting,
        },
    );

    return (
        <Layout>
            <PageHeader
                title={t("pages.tokens.transfers.title")}
                subtitle={t("pages.tokens.transfers.subtitle", { network: network.name })}
            />

            <PageHandlerProvider>
                <TokenTransfersTable />
            </PageHandlerProvider>
        </Layout>
    );
}
