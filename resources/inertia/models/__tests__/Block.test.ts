import { Block } from "../Block";
import { makeBlock, makeMemoryWallet } from "./factories";

describe("Block", () => {
    describe("from", () => {
        it("creates a Block instance from raw data", () => {
            expect(Block.from(makeBlock())).toBeInstanceOf(Block);
        });

        it("assigns all IBlock properties to the instance", () => {
            const data = makeBlock({ hash: "0xdeadbeef" });
            const block = Block.from(data);

            expect(block.hash).toBe("0xdeadbeef");
        });
    });

    describe("fromArray", () => {
        it("returns an array of Block instances", () => {
            const blocks = Block.fromArray([makeBlock({ hash: "0x1" }), makeBlock({ hash: "0x2" })]);

            expect(blocks).toHaveLength(2);
            expect(blocks[0]).toBeInstanceOf(Block);
            expect(blocks[1]).toBeInstanceOf(Block);
            expect(blocks[0].hash).toBe("0x1");
            expect(blocks[1].hash).toBe("0x2");
        });

        it("returns an empty array when given an empty array", () => {
            expect(Block.fromArray([])).toEqual([]);
        });
    });

    describe("totalReward", () => {
        it("returns the sum of reward and fee", () => {
            const block = Block.from(makeBlock({ reward: 2.0, fee: 0.5 }));

            expect(block.totalReward).toBe(2.5);
        });

        it("returns reward when fee is 0", () => {
            const block = Block.from(makeBlock({ reward: 2.0, fee: 0 }));

            expect(block.totalReward).toBe(2.0);
        });

        it("returns 0 when both reward and fee are 0", () => {
            const block = Block.from(makeBlock({ reward: 0, fee: 0 }));

            expect(block.totalReward).toBe(0);
        });
    });

    describe("data passthrough", () => {
        it("exposes proposer from data", () => {
            const proposer = makeMemoryWallet({ address: "proposer-address" });
            const block = Block.from(makeBlock({ proposer }));

            expect(block.proposer.address).toBe("proposer-address");
        });

        it("calculates rewardFiat and totalRewardFiat from exchangeRates", () => {
            const block = Block.from(makeBlock({ reward: 2.0, fee: 0.5, exchangeRates: { USD: 1.5 } }));

            expect(block.rewardFiat("USD")).toBe(3.0);
            expect(block.totalRewardFiat("USD")).toBe(3.75);
        });
    });
});
