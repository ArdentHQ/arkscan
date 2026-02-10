import { IFilters, IPaginatedResponse, ITabbedData } from "@/types";
import { IBlock, IWallet, ITransaction, ITokenTransfer } from "@/types/generated";

export interface WalletProps {
    transactions: IPaginatedResponse<ITransaction>;
    tokenTransfers: IPaginatedResponse<ITokenTransfer>;
    blocks?: IPaginatedResponse<IBlock>;
    voters?: IPaginatedResponse<IWallet>;
    wallet: IWallet;
    rates: Record<string, number>;
    filters: ITabbedData<IFilters>;
    baseUrl: string;
}
