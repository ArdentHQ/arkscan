import { INetwork, ITokenTransfer } from "@/types/generated";
import { Transaction } from "@/models/Transaction";

export class TokenTransfer {
    transaction: Transaction;

    constructor(data: ITokenTransfer, network: INetwork) {
        Object.assign(this, data);

        this.transaction = Transaction.make(data.transaction, network);
    }

    static make(data: ITokenTransfer, network: INetwork): TokenTransfer {
        return new TokenTransfer(data, network);
    }

    static fromArray(data: ITokenTransfer[], network: INetwork): TokenTransfer[] {
        return data.map(item => TokenTransfer.make(item, network));
    }
}

export interface TokenTransfer extends ITokenTransfer {}
