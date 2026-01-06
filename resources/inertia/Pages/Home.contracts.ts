import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";

export interface HomeProps {
    transactions?: IPaginatedResponse<ITransaction>;
    baseUrl: string;
}
