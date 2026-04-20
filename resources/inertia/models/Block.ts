import { IBlock } from "@/types/generated";
import { formatDateTime } from "@/utils/formatter";

export class Block {
    constructor(data: IBlock) {
        Object.assign(this, data);
    }

    static from(data: IBlock): Block {
        return new Block(data);
    }

    static fromArray(data: IBlock[]): Block[] {
        return data.map(Block.from);
    }

    get totalReward(): number {
        return this.reward + this.fee;
    }

    get timestampFormatted(): string {
        return formatDateTime(this.timestamp);
    }

    fiatRate(currency: string): number {
        return (this.exchangeRates as Record<string, number>)[currency] ?? 0;
    }

    rewardFiat(currency: string): number {
        return this.reward * this.fiatRate(currency);
    }

    feeFiat(currency: string): number {
        return this.fee * this.fiatRate(currency);
    }

    totalRewardFiat(currency: string): number {
        return this.totalReward * this.fiatRate(currency);
    }
}

export interface Block extends IBlock {}
