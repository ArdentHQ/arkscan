import { IFilters, IPaginatedResponse, ITabbedData } from "@/types";
import { IBlock, IWallet, ITransaction, ITokenAction, ITokenHolder } from "@/types/generated";

export interface WalletProps {
    transactions: IPaginatedResponse<ITransaction>;
    tokenTransfers: IPaginatedResponse<ITokenAction>;
    tokens: IPaginatedResponse<ITokenHolder>;
    blocks?: IPaginatedResponse<IBlock>;
    voters?: IPaginatedResponse<IWallet>;
    wallet: IWallet;
    tokenHoldingsCount: number;
    rates: Record<string, number>;
    filters: ITabbedData<IFilters>;
    baseUrl: string;
}
