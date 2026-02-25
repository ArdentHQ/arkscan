import { router } from "@inertiajs/react";
import { PageProps } from "@inertiajs/core";
import { useEffect } from "react";

import Layout from "@/Layout";
import { BlockShowProps } from "@/Pages/Block.contracts";
import {
    BlockHeader,
    BlockDetails,
    GeneratedBy,
    BlockSummary,
    Confirmations,
    TransactionList,
} from "@/Components/Block/Page";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { Block } from "@/models/Block";

export default function Show({ block, transactions }: PageProps<BlockShowProps>) {
    useEffect(() => {
        if (block.transactionCount > 0) {
            router.reload({
                only: ["transactions"],
            });
        }
    }, []);

    const blockModel = Block.from(block);

    return (
        <Layout>
            <BlockHeader block={block} />

            <div>
                <BlockDetails block={blockModel} />

                <GeneratedBy block={block} />

                <BlockSummary block={block} />

                <Confirmations block={block} />

                {block.transactionCount > 0 && (
                    <PageHandlerProvider>
                        <TransactionList noMargins transactions={transactions} />
                    </PageHandlerProvider>
                )}
            </div>
        </Layout>
    );
}
