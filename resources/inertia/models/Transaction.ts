import { ITransaction } from "@/types/generated";
import { TransactionMethod } from "@/models/TransactionMethod";

export class Transaction {
    method: TransactionMethod;

    constructor(data: ITransaction) {
        Object.assign(this, data);

        this.method = new TransactionMethod(data);

        if (data.validatorRegistration) {
            this.validatorRegistration = Transaction.from(data.validatorRegistration);
        }
    }

    static from(data: ITransaction): Transaction {
        return new Transaction(data);
    }

    static fromArray(data: ITransaction[]): Transaction[] {
        return data.map(Transaction.from);
    }

    get hasFailed(): boolean {
        return this.status === false;
    }
}

export interface Transaction extends ITransaction {
    validatorRegistration: Transaction | null;
}
