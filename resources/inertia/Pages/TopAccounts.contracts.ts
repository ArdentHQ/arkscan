import { IPaginatedResponse } from "@/types";
import { IWallet } from "@/types/generated";

export interface TopAccountsProps {
    wallets?: IPaginatedResponse<IWallet>;
}
