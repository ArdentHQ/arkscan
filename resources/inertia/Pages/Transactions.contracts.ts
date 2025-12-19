import { PageProps } from "@inertiajs/core";

export interface TransactionsProps
    extends PageProps<{
        transactionCount: number;
        volume: number;
        totalFees: number;
        averageFee: number;
    }> {}
