import { INetwork, ITokenAction } from "@/types/generated";
import { Transaction } from "@/models/Transaction";

export class TokenAction {
    transaction: Transaction;

    constructor(data: ITokenAction, network: INetwork) {
        Object.assign(this, data);

        this.transaction = Transaction.make(data.transaction, network);
    }

    static make(data: ITokenAction, network: INetwork): TokenAction {
        return new TokenAction(data, network);
    }

    static fromArray(data: ITokenAction[], network: INetwork): TokenAction[] {
        return data.map((item) => TokenAction.make(item, network));
    }
}

export interface TokenAction extends ITokenAction {}
