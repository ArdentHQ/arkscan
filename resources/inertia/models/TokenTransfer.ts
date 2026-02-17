import { ITokenTransfer } from "@/types/generated";
import { Transaction } from "@/models/Transaction";

export class TokenTransfer {
    transaction: Transaction;

    constructor(data: ITokenTransfer) {
        Object.assign(this, data);

        this.transaction = Transaction.from(data.transaction!);
    }

    static from(data: ITokenTransfer): TokenTransfer {
        return new TokenTransfer(data);
    }

    static fromArray(data: ITokenTransfer[]): TokenTransfer[] {
        return data.map(TokenTransfer.from);
    }
}

export interface TokenTransfer extends ITokenTransfer {}
