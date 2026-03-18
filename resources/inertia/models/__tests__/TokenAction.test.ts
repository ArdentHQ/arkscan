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

import { TokenAction } from "../TokenAction";
import { Transaction } from "../Transaction";
import { makeMemoryWallet, makeNetwork, makeToken, makeTokenAction, makeTransaction } from "./factories";

const network = makeNetwork();

describe("TokenAction", () => {
    describe("make", () => {
        it("creates a TokenAction instance from raw data", () => {
            const tokenAction = TokenAction.make(makeTokenAction(), network);

            expect(tokenAction).toBeInstanceOf(TokenAction);
        });

        it("assigns all ITokenAction properties to the instance", () => {
            const data = makeTokenAction({ transaction_hash: "0xdeadbeef" });
            const tokenAction = TokenAction.make(data, network);

            expect(tokenAction.transaction_hash).toBe("0xdeadbeef");
        });

        it("wraps the transaction as a Transaction instance", () => {
            const tokenAction = TokenAction.make(makeTokenAction(), network);

            expect(tokenAction.transaction).toBeInstanceOf(Transaction);
        });

        it("assigns transaction properties correctly", () => {
            const txData = makeTransaction({ hash: "0xtxhash" });
            const tokenAction = TokenAction.make(makeTokenAction({ transaction: txData }), network);

            expect(tokenAction.transaction.hash).toBe("0xtxhash");
        });

        it("preserves from and to wallet data", () => {
            const from = makeMemoryWallet({ address: "from-wallet" });
            const to = makeMemoryWallet({ address: "to-wallet" });
            const tokenAction = TokenAction.make(makeTokenAction({ from, to }), network);

            expect(tokenAction.from.address).toBe("from-wallet");
            expect(tokenAction.to.address).toBe("to-wallet");
        });

        it("preserves token data", () => {
            const token = makeToken({ name: "My Token", symbol: "MTK" });
            const tokenAction = TokenAction.make(makeTokenAction({ token }), network);

            expect(tokenAction.token.name).toBe("My Token");
            expect(tokenAction.token.symbol).toBe("MTK");
        });

        it("preserves amount and value", () => {
            const tokenAction = TokenAction.make(
                makeTokenAction({ amount: 42.5, value: "42500000000000000000" }),
                network,
            );

            expect(tokenAction.amount).toBe(42.5);
            expect(tokenAction.value).toBe("42500000000000000000");
        });
    });

    describe("fromArray", () => {
        it("returns an array of TokenAction instances", () => {
            const data = [
                makeTokenAction({ transaction_hash: "0x1" }),
                makeTokenAction({ transaction_hash: "0x2" }),
            ];

            const result = TokenAction.fromArray(data, network);

            expect(result).toHaveLength(2);
            expect(result[0]).toBeInstanceOf(TokenAction);
            expect(result[1]).toBeInstanceOf(TokenAction);
            expect(result[0].transaction_hash).toBe("0x1");
            expect(result[1].transaction_hash).toBe("0x2");
        });

        it("returns an empty array when given an empty array", () => {
            expect(TokenAction.fromArray([], network)).toEqual([]);
        });

        it("wraps each entry's transaction as a Transaction instance", () => {
            const result = TokenAction.fromArray([makeTokenAction(), makeTokenAction()], network);

            for (const ta of result) {
                expect(ta.transaction).toBeInstanceOf(Transaction);
            }
        });
    });
});
