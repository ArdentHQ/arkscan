jest.mock("react-i18next", () => ({
    useTranslation: () => ({
        t: (key: string) => key,
        i18n: { exists: () => false },
    }),
}));

jest.mock("@arkecosystem/typescript-crypto", () => ({
    UnitConverter: {
        formatUnits: (value: string, _unit: string) => Number(value) / 1e8,
    },
}));

import { TokenTransfer } from "../TokenTransfer";
import { Transaction } from "../Transaction";
import { makeMemoryWallet, makeNetwork, makeToken, makeTokenTransfer, makeTransaction } from "./factories";

const network = makeNetwork();

describe("TokenTransfer", () => {
    describe("make", () => {
        it("creates a TokenTransfer instance from raw data", () => {
            const tokenTransfer = TokenTransfer.make(makeTokenTransfer(), network);

            expect(tokenTransfer).toBeInstanceOf(TokenTransfer);
        });

        it("assigns all ITokenTransfer properties to the instance", () => {
            const data = makeTokenTransfer({ transaction_hash: "0xdeadbeef" });
            const tokenTransfer = TokenTransfer.make(data, network);

            expect(tokenTransfer.transaction_hash).toBe("0xdeadbeef");
        });

        it("wraps the transaction as a Transaction instance", () => {
            const tokenTransfer = TokenTransfer.make(makeTokenTransfer(), network);

            expect(tokenTransfer.transaction).toBeInstanceOf(Transaction);
        });

        it("assigns transaction properties correctly", () => {
            const txData = makeTransaction({ hash: "0xtxhash" });
            const tokenTransfer = TokenTransfer.make(makeTokenTransfer({ transaction: txData }), network);

            expect(tokenTransfer.transaction.hash).toBe("0xtxhash");
        });

        it("preserves from and to wallet data", () => {
            const from = makeMemoryWallet({ address: "from-wallet" });
            const to = makeMemoryWallet({ address: "to-wallet" });
            const tokenTransfer = TokenTransfer.make(makeTokenTransfer({ from, to }), network);

            expect(tokenTransfer.from.address).toBe("from-wallet");
            expect(tokenTransfer.to.address).toBe("to-wallet");
        });

        it("preserves token data", () => {
            const token = makeToken({ name: "My Token", symbol: "MTK" });
            const tokenTransfer = TokenTransfer.make(makeTokenTransfer({ token }), network);

            expect(tokenTransfer.token.name).toBe("My Token");
            expect(tokenTransfer.token.symbol).toBe("MTK");
        });

        it("preserves amount and value", () => {
            const tokenTransfer = TokenTransfer.make(
                makeTokenTransfer({ amount: 42.5, value: "42500000000000000000" }),
                network,
            );

            expect(tokenTransfer.amount).toBe(42.5);
            expect(tokenTransfer.value).toBe("42500000000000000000");
        });
    });

    describe("fromArray", () => {
        it("returns an array of TokenTransfer instances", () => {
            const data = [
                makeTokenTransfer({ transaction_hash: "0x1" }),
                makeTokenTransfer({ transaction_hash: "0x2" }),
            ];

            const result = TokenTransfer.fromArray(data, network);

            expect(result).toHaveLength(2);
            expect(result[0]).toBeInstanceOf(TokenTransfer);
            expect(result[1]).toBeInstanceOf(TokenTransfer);
            expect(result[0].transaction_hash).toBe("0x1");
            expect(result[1].transaction_hash).toBe("0x2");
        });

        it("returns an empty array when given an empty array", () => {
            expect(TokenTransfer.fromArray([], network)).toEqual([]);
        });

        it("wraps each entry's transaction as a Transaction instance", () => {
            const result = TokenTransfer.fromArray([makeTokenTransfer(), makeTokenTransfer()], network);

            for (const tt of result) {
                expect(tt.transaction).toBeInstanceOf(Transaction);
            }
        });
    });
});
