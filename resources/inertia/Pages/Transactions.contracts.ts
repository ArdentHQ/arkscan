import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import { PageProps } from "@inertiajs/core";

export interface TransactionsProps
    extends PageProps<{
        statistics: {
            transactionCount: number;
            volume: number;
            totalFees: number;
            averageFee: number;
        };
        transactions?: IPaginatedResponse<ITransaction>;
        filters: {
            transfers: boolean;
            multipayments: boolean;
            votes: boolean;
            validator: boolean;
            username: boolean;
            contract_deployment: boolean;
            others: boolean;
        };
    }> {}
