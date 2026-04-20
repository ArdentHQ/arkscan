import { IPaginatedResponse } from "@/types";
import { IBlock, ITransaction } from "@/types/generated";

export interface BlockShowProps {
    block: IBlock;
    transactions?: IPaginatedResponse<ITransaction>;
}
