import { ITransaction } from "@/types/generated";
import { TransactionMethod } from "@/models/TransactionMethod";
import { Address, UnitConverter } from "@arkecosystem/typescript-crypto";
import BigNumber, { toFloat } from "@/utils/big-number";

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

    isSent(address: string): boolean {
        return Address.fromPublicKey(this.sender_public_key) === address;
    }

    isReceived(address: string): boolean {
        console.log(this.to, address, this.to === address);
        return this.to === address;
    }

    isSentToSelf(address: string): boolean {
        if (!this.method.isTransfer && !this.method.isTokenTransfer) {
            return false;
        }

        if (!this.isSent(address)) {
            return false;
        }

        if (!this.method.isMultiPayment && address !== this.to) {
            return false;
        }

        return true;
    }

    get amount(): number {
        if (!this.method.isMultiPayment) {
            return UnitConverter.formatUnits(this.value, "ark");
        }

        let amount = new BigNumber(0);
        for (const recipient of this.multiPaymentRecipients) {
            amount = amount.plus(recipient.amount);
        }

        return toFloat(amount);
    }

    get amountForItself(): number {
        if (!this.method.isMultiPayment) {
            return 0;
        }

        const sender = this.sender;
        const recipients = this.multiPaymentRecipients.filter(function (recipient): boolean {
            return sender !== null && sender.address.toLowerCase() === recipient.address.toLowerCase();
        });

        let amount = new BigNumber(0);
        for (const recipient of recipients) {
            amount = amount.plus(recipient.amount);
        }

        return toFloat(amount);
    }

    get amountExcludingItself(): number {
        if (!this.method.isMultiPayment) {
            return 0;
        }

        const sender = this.sender;
        const recipients = this.multiPaymentRecipients.filter(function (recipient): boolean {
            return sender === null || sender.address.toLowerCase() !== recipient.address.toLowerCase();
        });

        let amount = new BigNumber(0);
        for (const recipient of recipients) {
            amount = amount.plus(recipient.amount);
        }

        return toFloat(amount);
    }

    get amountWithFee(): number {
        return toFloat(this.value) + this.fee;
    }

    amountReceived(address?: string): number {
        if (!this.method.isMultiPayment || !address) {
            return this.amount;
        }

        const results = this.multiPaymentRecipients.filter(function (recipient): boolean {
            return address.toLowerCase() === recipient.address.toLowerCase();
        });

        let amount = new BigNumber(0);
        for (const recipient of results) {
            amount = amount.plus(recipient.amount);
        }

        return toFloat(amount);
    }

    get fee(): number {
        const gasPrice = new BigNumber(this.gas_price).multipliedBy(this.gas_used);

        return UnitConverter.formatUnits(gasPrice.toString(), "ark");
    }
}

export interface Transaction extends ITransaction {
    validatorRegistration: Transaction | null;
}
