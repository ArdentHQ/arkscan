import { IPaginatedResponse } from "@/types";
import { IBlock, ITransaction } from "@/types/generated";

export interface HomeProps {
    blocks?: IPaginatedResponse<IBlock>;
    transactions?: IPaginatedResponse<ITransaction>;
    baseUrl: string;
}
