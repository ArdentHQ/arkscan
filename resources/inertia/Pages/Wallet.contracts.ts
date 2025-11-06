import { IFilters, IPaginatedResponse, ITabbedData } from "@/types";
import { IBlock, IWallet, ITransaction } from "@/types/generated";

export interface WalletProps {
    transactions: IPaginatedResponse<ITransaction>;
    blocks?: IPaginatedResponse<IBlock>;
    voters?: IPaginatedResponse<IWallet>;
    wallet: IWallet;
    rates: Record<string, number>;
    filters: ITabbedData<IFilters>;
}
