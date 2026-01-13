import { IPaginatedResponse } from "@/types";
import { IBlockDetails, ITransaction } from "@/types/generated";

export interface BlockShowProps {
    block: IBlockDetails;
    transactions?: IPaginatedResponse<ITransaction>;
}
