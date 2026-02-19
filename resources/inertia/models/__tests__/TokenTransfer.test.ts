jest.mock("@/hooks/use-shared-data", () => ({
    __esModule: true,
    default: () => ({
        network: {
            contractMethods: {
                transfer: "a9059cbb",
                multipayment: "1234abcd",
                vote: "5678ef01",
                unvote: "9abcdef0",
                validator_registration: "11223344",
                validator_resignation: "55667788",
                validator_update: "99aabbcc",
                username_registration: "ddeeff00",
                username_resignation: "ff112233",
                approve: "095ea7b3",
                contract_deployment: "22222222",
                batch_transfer: "33333333",
            },
        },
    }),
}));

jest.mock("react-i18next", () => ({
    useTranslation: () => ({
        t: (key: string) => key,
        i18n: { exists: () => false },
    }),
}));

jest.mock("@arkecosystem/typescript-crypto", () => ({
    Address: {
        fromPublicKey: (publicKey: string) => `ark:${publicKey}`,
    },
    UnitConverter: {
        formatUnits: (value: string, _unit: string) => Number(value) / 1e8,
    },
}));

import { TokenTransfer } from "../TokenTransfer";
import { Transaction } from "../Transaction";
import { makeMemoryWallet, makeToken, makeTokenTransfer, makeTransaction } from "./factories";

describe("TokenTransfer", () => {
    describe("from", () => {
        it("creates a TokenTransfer instance from raw data", () => {
            const tokenTransfer = TokenTransfer.from(makeTokenTransfer());

            expect(tokenTransfer).toBeInstanceOf(TokenTransfer);
        });

        it("assigns all ITokenTransfer properties to the instance", () => {
            const data = makeTokenTransfer({ transaction_hash: "0xdeadbeef" });
            const tokenTransfer = TokenTransfer.from(data);

            expect(tokenTransfer.transaction_hash).toBe("0xdeadbeef");
        });

        it("wraps the transaction as a Transaction instance", () => {
            const tokenTransfer = TokenTransfer.from(makeTokenTransfer());

            expect(tokenTransfer.transaction).toBeInstanceOf(Transaction);
        });

        it("assigns transaction properties correctly", () => {
            const txData = makeTransaction({ hash: "0xtxhash" });
            const tokenTransfer = TokenTransfer.from(makeTokenTransfer({ transaction: txData }));

            expect(tokenTransfer.transaction.hash).toBe("0xtxhash");
        });

        it("preserves from and to wallet data", () => {
            const from = makeMemoryWallet({ address: "from-wallet" });
            const to = makeMemoryWallet({ address: "to-wallet" });
            const tokenTransfer = TokenTransfer.from(makeTokenTransfer({ from, to }));

            expect(tokenTransfer.from.address).toBe("from-wallet");
            expect(tokenTransfer.to.address).toBe("to-wallet");
        });

        it("preserves token data", () => {
            const token = makeToken({ name: "My Token", symbol: "MTK" });
            const tokenTransfer = TokenTransfer.from(makeTokenTransfer({ token }));

            expect(tokenTransfer.token.name).toBe("My Token");
            expect(tokenTransfer.token.symbol).toBe("MTK");
        });

        it("preserves amount and value", () => {
            const tokenTransfer = TokenTransfer.from(
                makeTokenTransfer({ amount: 42.5, value: "42500000000000000000" }),
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

            const result = TokenTransfer.fromArray(data);

            expect(result).toHaveLength(2);
            expect(result[0]).toBeInstanceOf(TokenTransfer);
            expect(result[1]).toBeInstanceOf(TokenTransfer);
            expect(result[0].transaction_hash).toBe("0x1");
            expect(result[1].transaction_hash).toBe("0x2");
        });

        it("returns an empty array when given an empty array", () => {
            expect(TokenTransfer.fromArray([])).toEqual([]);
        });

        it("wraps each entry's transaction as a Transaction instance", () => {
            const result = TokenTransfer.fromArray([makeTokenTransfer(), makeTokenTransfer()]);

            for (const tt of result) {
                expect(tt.transaction).toBeInstanceOf(Transaction);
            }
        });
    });
});
