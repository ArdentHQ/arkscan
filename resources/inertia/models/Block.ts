import { DATE_TIME_FORMAT } from "@/constants";
import { IBlock } from "@/types/generated";
import dayjs from "dayjs";

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
        return dayjs(this.timestamp * 1000).format(DATE_TIME_FORMAT);
    }
}

export interface Block extends IBlock {}
